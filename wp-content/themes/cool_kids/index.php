<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>
    
    <meta name="description" content="<?php echo get_theme_mod('meta_description', 'Default meta description if empty'); ?>">
    <meta name="keywords" content="<?php echo get_theme_mod('meta_keywords', 'default, keywords'); ?>">

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

    <!-- Video Background -->
    <video class="bgimg" autoplay muted loop>
        <source src="/wp-content/uploads/2025/03/futuristic-digital-landscape.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="theme-container">
        <div class="theme-banner theme-content">
            <!-- Dynamically Display Logo from Theme Settings -->
            <div class="theme-logo">
                <?php if (has_custom_logo()) : ?>
                    <img src="<?php echo esc_url(get_theme_mod('custom_logo')); ?>" alt="Logo" />
                <?php else : ?>
                    <h2>Logo</h2>
                <?php endif; ?>
            </div>
            
            <h1 class="theme-title">COMING SOON</h1>
            <hr class="theme-hr">
            
            <!-- Login and Registration Buttons -->
            <div class="theme-buttons">
                <a href="<?php echo wp_login_url(); ?>" class="button">Login</a>
                <a href="<?php echo wp_registration_url(); ?>" class="button">Register</a>
            </div>
            
        </div>
    </div>

    <?php wp_footer(); ?> <!-- Include necessary WordPress footer scripts -->
</body>
</html>
