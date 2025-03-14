<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Include registration class
require_once( get_template_directory() . '/includes/theme.php' );

// Include Rest APIs class
require_once( get_template_directory() . '/includes/rest_api.php' );

new \Rest_API\UserRoleAPI();

// Initialize Theme
$theme = new \Theme\Cool_kids("Cool Kids");

if($theme){
    add_action('wp_enqueue_scripts', [$theme::class, 'load_theme_asset_files']);
    add_action('customize_register', 'custom_meta_customizer_settings');
    update_option('default_role','cool_kid');
}

// Add theme customizer settings for meta information
function custom_meta_customizer_settings($wp_customize) {
    // Add a section for meta information
    $wp_customize->add_section('custom_meta_section', array(
        'title'    => __('Custom Meta Information', 'your-theme'),
        'priority' => 30,
    ));

    // Add setting for meta description
    $wp_customize->add_setting('meta_description', array(
        'default'   => '',
        'transport' => 'refresh',
    ));
    
    // Add control for meta description
    $wp_customize->add_control('meta_description_control', array(
        'label'    => __('Meta Description', 'your-theme'),
        'section'  => 'custom_meta_section',
        'settings' => 'meta_description',
        'type'     => 'text',
    ));

    // Add setting for meta keywords
    $wp_customize->add_setting('meta_keywords', array(
        'default'   => '',
        'transport' => 'refresh',
    ));

    // Add control for meta keywords
    $wp_customize->add_control('meta_keywords_control', array(
        'label'    => __('Meta Keywords', 'your-theme'),
        'section'  => 'custom_meta_section',
        'settings' => 'meta_keywords',
        'type'     => 'text',
    ));
}

add_action('wp_ajax_register_user', 'handle_user_registration');
add_action('wp_ajax_nopriv_register_user', 'handle_user_registration');

function handle_user_registration() {

    // Check nonce to verify the request is valid
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'request-validate')) {
        throw new Exception('Invalid request.');
    }

    // Get email from POST request
    $email = sanitize_email($_POST['email']);
    if (empty($email)) {
        throw new Exception('Email is required.');
    }

    $user_exist = email_exists( $email );
    if ( $user_exist ) {
        throw new Exception("That E-mail is registered to user number " . $user_exist);
    }

    try {

        // Fetch random user data from the custom cURL function
        $theme = new \Theme\Cool_kids("Cool Kids");
        $random_user_data = $theme::get_random_user();
        
        // Check if there's an error in fetching data
        if (is_string($random_user_data) && strpos($random_user_data, 'cURL Error') !== false) {
            throw new Exception($random_user_data);
        }

        // Extract the user details from the fetched random user data
        if (isset($random_user_data['results'][0])) {
            $user_data = $random_user_data['results'][0];
            
            $first_name = sanitize_text_field($user_data['name']['first']);
            $last_name = sanitize_text_field($user_data['name']['last']);
            $username = sanitize_user($user_data['login']['username']);
            $country = sanitize_text_field($user_data['location']['country']);
            $alt_email = sanitize_text_field($user_data['email']);
            $password = wp_generate_password();
            $user_email = $email;
            
            // Create the user
            $user_id = wp_create_user($username, $password, $user_email);

            // Check if the user creation was successful
            if (is_wp_error($user_id)) {
                throw new Exception('User creation failed: ' . $user_id->get_error_message());
            }

            // Update user meta with first and last name
            $user_update = wp_update_user(array(
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
            ));

            // Check if the user meta update was successful
            if (is_wp_error($user_update)) {
                throw new Exception('Failed to update user meta.');
            }

            // Update country and alternate email as user meta
            update_user_meta($user_id, 'country', $country);
            update_user_meta($user_id, 'alternate_email', $alt_email);

            // Fetch and set the user avatar
            $avatar_url = $user_data['picture']['large']; // Avatar URL from the API
            $avatar_response = wp_remote_get($avatar_url);
            
            // Check if the avatar fetch was successful
            if (!is_wp_error($avatar_response) && isset($avatar_response['body'])) {
                // Get the file contents
                $avatar_data = wp_remote_retrieve_body($avatar_response);
                
                // Upload the avatar as a WordPress media file
                $upload = wp_upload_bits(basename($avatar_url), null, $avatar_data);
                
                if (!$upload['error']) {
                    // Set the avatar for the user
                    update_user_meta($user_id, 'profile_picture', $upload['url']);
                } else {
                    throw new Exception('Failed to upload avatar.');
                }
            } else {
                throw new Exception('Failed to fetch avatar.');
            }

            // Success response
            wp_send_json_success(array( 'success' => true, 'message' => 'Registration successful!', 'redirect_url' => home_url() ));
        } else {
            throw new Exception('Invalid response from the API.');
        }
        
    } catch (Exception $e) {
        // In case of any error, send an error response with the exception message
        wp_send_json_error(array('success' => false, 'message' => $e->getMessage()));
    }
}


add_action('wp_ajax_login_user', 'handle_user_login');
add_action('wp_ajax_nopriv_login_user', 'handle_user_login');

function handle_user_login() {
    // Verify nonce to prevent CSRF attacks
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'request-validate')) {
        wp_send_json_error(array('success' => false, 'message' => 'Invalid request.'));
    }

    // Get the email from the POST request
    $email = sanitize_email($_POST['email']);

    if (empty($email)) {
        wp_send_json_error(array('success' => false, 'message' => 'Email is required.'));
    }

    // Attempt to get the user by email
    $user = get_user_by('email', $email);

    if (!$user) {
        wp_send_json_error(array('success' => false, 'message' => 'No user found with this email.'));
    }

    // Set up the login session using wp_set_auth_cookie()
    wp_set_auth_cookie($user->ID);

    // Redirect the user to the frontend page (for example, the homepage or a dashboard page)
    $redirect_url = home_url();

    // Send success response and the redirect URL
    wp_send_json_success(array('success' => true, 'message' => 'Login successful!', 'redirect_url' => $redirect_url));
}
