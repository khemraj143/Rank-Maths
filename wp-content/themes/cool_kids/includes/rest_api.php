<?php

namespace Rest_API;

use WP_REST_Request;
use WP_REST_Response;
use WP_User;
use Firebase\JWT\JWT;

class UserRoleAPI {

    public function __construct() {
        // Register the API endpoint
        add_action('rest_api_init', [self::class, 'register_routes']);
    }

    /**
     * Register REST routes
     */
    public static function register_routes() {
        // Endpoint for generating JWT token based on email
        register_rest_route('ck_user/jwt/v1', '/generate-token/', [
            'methods' => 'POST',
            'callback' => [self::class, 'generate_token'],
            'permission_callback' => '__return_true',
        ]);

        // Endpoint to set the user role
        register_rest_route('ck_user/v1', '/set-role/', [
            'methods' => 'POST',
            'callback' => [self::class, 'set_user_role'],
            'permission_callback' => [self::class, 'check_user_permissions'],
        ]);
    }

    /**
     * Generate JWT token based on email
     */
    public static function generate_token(WP_REST_Request $request) {
        $email = sanitize_email($request->get_param('email'));

        // Check if the email exists
        $user = get_user_by('email', $email);
        if (!$user) {
            return new WP_REST_Response('User with this email does not exist.', 404);
        }

        // Generate JWT token for the user
        $token = self::generate_jwt_token($user);

        if (!$token) {
            return new WP_REST_Response('Failed to generate token.', 500);
        }

        return new WP_REST_Response([
            'token' => $token,
            'user_email' => $email,
            'user_nicename' => $user->user_nicename,
            'user_display_name' => $user->display_name,
        ], 200);
    }

    /**
     * Helper function to generate JWT token
     */
    public static function generate_jwt_token(WP_User $user) {
        $secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : '';
        if (empty($secret_key)) {
            return false;
        }

        $issued_at = time();
        $expiration_time = $issued_at + (60 * 60); // 1 hour
        $payload = [
            'iat' => $issued_at,
            'exp' => $expiration_time,
            'user' => [
                'ID' => $user->ID,
                'email' => $user->user_email,
                'nicename' => $user->user_nicename,
                'display_name' => $user->display_name,
            ],
        ];

        // Encode the JWT token using the secret key and HS256 algorithm
        return JWT::encode($payload, $secret_key, 'HS256'); // Added 'HS256' as the third argument
    }

    /**
     * Check if the user has permission (admin only) via JWT
     */
    public static function check_user_permissions() {
        // Check if the Authorization header is set
        $auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';

        if (empty($auth_header) || strpos($auth_header, 'Bearer ') !== 0) {
            return new WP_REST_Response('Authorization header missing or malformed.', 401);
        }

        // Extract the token from the header
        $jwt_token = substr($auth_header, 7);

        // Validate the token (using the JWT plugin)
        $user = self::get_user_from_jwt_token($jwt_token);

        if (!$user) {
            return new WP_REST_Response('Invalid token or expired.', 401);
        }

        // Optionally, you can check the user's role, etc.
        if (!current_user_can('administrator', $user->ID)) {
            return new WP_REST_Response('Permission denied: Only administrators can change user roles.', 403);
        }

        return true;
    }

    /**
     * Get user by JWT token
     * This function assumes the JWT plugin is validating the token.
     *
     * @param string $jwt_token The JWT token.
     * @return WP_User|null
     */
    public static function get_user_from_jwt_token($jwt_token) {
        // Decode the JWT token to get the user ID
        $payload = JWT::decode($jwt_token, defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : '', ['HS256']);
        
        if (isset($payload->user->ID)) {
            return get_user_by('id', $payload->user->ID);
        }

        return null;
    }

    /**
     * Set a user's role
     *
     * @param WP_REST_Request $request The REST request.
     * @return WP_REST_Response
     */
    public static function set_user_role(WP_REST_Request $request) {
        $email = sanitize_email($request->get_param('email'));
        $first_name = sanitize_text_field($request->get_param('first_name'));
        $last_name = sanitize_text_field($request->get_param('last_name'));
        $role = sanitize_text_field($request->get_param('role'));

        $us_email = $email ?: false;
        $us_name = $first_name ?: false;
        $us_lastname = $last_name ?: false;

        // Validate the role
        if (!$role || empty($role) || !in_array($role, ['Cool Kid', 'Cooler Kid', 'Coolest Kid'])) {
            return new WP_REST_Response('Invalid role. Valid roles are Cool Kid, Cooler Kid, and Coolest Kid.', 400);
        }

        $role_slug = null;
        foreach (wp_roles()->roles as $slug => $wp_role) {
            if ($wp_role['name'] == $role) {
                $role_slug = $slug;
                break;
            }
        }

        if($us_email || ($us_name && $us_lastname)){
            // Look for a user by email or first and last names
            $user = self::get_user_by_email_or_name($us_email, $us_name, $us_lastname);
        }

        // If no user is found, return an error
        if (!$user) {
            return new WP_REST_Response('User not found.', 404);
        }

        if(!$role_slug){
            return new WP_REST_Response("The role were didnot matched. Valid roles are Cool Kid, Cooler Kid, and Coolest Kid.", 404);
        }

        // Set the role
        $user->set_role($role_slug);

        return new WP_REST_Response("Role '{$role}' has been successfully assigned to {$user->user_login}.", 200);
    }

    /**
     * Get a user by email or first and last names
     *
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @return WP_User|null
     */
    public static function get_user_by_email_or_name($email, $first_name, $last_name) {
        if ($email) {
            return get_user_by('email', $email);
        } else if ($first_name && $last_name) {
            $users = get_users([
                'meta_key' => 'first_name',
                'meta_value' => $first_name,
                'meta_key' => 'last_name',
                'meta_value' => $last_name,
            ]);
            return !empty($users) ? $users[0] : null;
        }
        return null;
    }
}