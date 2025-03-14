<?php

namespace Theme;

class Cool_kids {
    private static $name;

    public function __construct(string $name) {
        self::$name = $name;
        self::init();
    }

    // Static class for theme initialize
    public static function init() {
        add_action('after_setup_theme', [self::class, 'theme_setup']);
        self::load_theme_custom_roles();
    }

    public static function theme_setup() {
        /** automatic feed link */
        add_theme_support('automatic-feed-links');

        /** tag-title */
        add_theme_support('title-tag');

        /** post formats */
        $post_formats = array('aside', 'image', 'gallery', 'video', 'audio', 'link', 'quote', 'status');
        add_theme_support('post-formats', $post_formats);

        /** post thumbnail */
        add_theme_support('post-thumbnails');

        /** HTML5 support */
        add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));

        /** refresh widgets */
        add_theme_support('customize-selective-refresh-widgets');

        /** custom background */
        $bg_defaults = array(
            'default-image' => '',
            'default-preset' => 'default',
            'default-size' => 'cover',
            'default-repeat' => 'no-repeat',
            'default-attachment' => 'scroll',
        );
        add_theme_support('custom-background', $bg_defaults);

        /** custom header */
        $header_defaults = array(
            'default-image' => '',
            'width' => 300,
            'height' => 60,
            'flex-height' => true,
            'flex-width' => true,
            'default-text-color' => '',
            'header-text' => true,
            'uploads' => true,
        );
        add_theme_support('custom-header', $header_defaults);

        /** custom logo */
        add_theme_support('custom-logo', array(
            'height' => 60,
            'width' => 400,
            'flex-height' => true,
            'flex-width' => true,
            'header-text' => array('site-title', 'site-description'),
        ));
    }

    public static function load_theme_asset_files() {
        wp_enqueue_style('ck-style', get_template_directory_uri() . '/style.css');
        wp_enqueue_style('ck-responsive', get_template_directory_uri() . '/assets/css/responsive.css');
        wp_enqueue_script('ck-script', get_template_directory_uri() . '/assets/js/theme_scripts.js', ['jquery'], false, true);
        
        // Localize script to pass AJAX URL and nonce for security
        wp_localize_script('ck-script', 'ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('request-validate')
        ));
    }

    public static function load_theme_custom_roles() {
        // Add 'cool_kid' role - Subscriber-like role (default)
        add_role('cool_kid', 'Cool Kid', array(
            'read' => true,
        ));

        // Add 'cooler_kid' role - Moderator-like role
        add_role('cooler_kid', 'Cooler Kid', array(
            'read' => true,
            'edit_posts' => true,
            'moderate_comments' => true,
        ));

        // Add 'coolest_kid' role - Administrator-like role
        add_role('coolest_kid', 'Coolest Kid', array(
            'read' => true,
            'edit_posts' => true,
            'publish_posts' => true,
            'moderate_comments' => true,
            'manage_options' => true,
            'activate_plugins' => true,
            'edit_users' => true,
        ));
    }

    public static function get_random_user(){
        // The URL to fetch data from
        $url = "https://randomuser.me/api/";
        $user = '';
    
        // Initialize cURL session
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        // Execute cURL request and get the response
        $response = curl_exec($ch);
    
        // Check for errors
        if ($response === false) {
            return 'cURL Error: ' . curl_error($ch); // Return error message
        } else {
            // Decode the JSON response into an array
            $user = json_decode($response, true);
        }
    
        // Close cURL session
        curl_close($ch);
    
        // Return user data
        return $user;
    }
}