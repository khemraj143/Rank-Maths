<?php
/*
Template Name: Home
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>
    
    <meta name="description" content="<?php echo get_theme_mod('meta_description', 'Default meta description if empty'); ?>">
    <meta name="keywords" content="<?php echo get_theme_mod('meta_keywords', 'default, keywords'); ?>">

    <?php wp_head(); $role = ''; ?>
</head>
<body <?php body_class(); ?>>

    <!-- Video Background -->
    <video class="bgimg" autoplay muted loop>
        <source src="/wp-content/uploads/2025/03/futuristic-digital-landscape.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="theme-container <?php echo is_user_logged_in() ? 'logged-in' : ''; ?>">
        <div class="theme-banner theme-content">
            <!-- Dynamically Display Logo from Theme Settings -->
            <div class="theme-logo">
            <?php if (has_custom_logo()) : ?>
                <?php 
                    // Get the custom logo ID
                    $logo_id = get_theme_mod('custom_logo');
                    
                    // Get the image URL using the image ID
                    $logo_url = wp_get_attachment_image_url($logo_id, 'full');
                ?>
                <img src="<?php echo esc_url($logo_url); ?>" width="200px" alt="Logo" />
            <?php else : ?>
                <h2>Logo</h2>
            <?php endif; ?>
            </div>
            
            <!-- Login and Registration Buttons -->
            <div class="theme-buttons">
                <?php if(!is_user_logged_in()) :?>
                    <a href="#" class="home-btn login" data-popup-type="login">Login</a>
                    <a href="#" class="home-btn register" data-popup-type="register">Register</a>
                <?php else: ?>
                    <a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout">Logout</a>
                <?php endif; ?>
            </div>
        </div>
        <?php if(is_user_logged_in()) :
            $current_user = wp_get_current_user();
            $user_roles = $current_user->roles;

            // Display self data based on user role
            if(!empty($user_roles)) :
                $role = $user_roles[0];
                ?>
                
                <div class="user-info">
                    <h3>Your Information</h3>
                    <p><img src="<?php echo esc_url(get_avatar_url($current_user->ID)); ?>" alt="Gravatar"></p>
                    <p><strong>First Name:</strong> <?php echo esc_html($current_user->first_name); ?></p>
                    <p><strong>Last Name:</strong> <?php echo esc_html($current_user->last_name); ?></p>
                    <p><strong>Email Address:</strong> <?php echo esc_html($current_user->user_email); ?></p>
                    <p><strong>Country:</strong> <?php echo esc_html(get_user_meta($current_user->ID, 'country', true)); ?></p>
                    <p><strong>Role:</strong> <?php echo esc_html($role); ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
        // List of users based on the role
        if(!empty($role) && ($role == "cooler_kid" || $role == "coolest_kid")) :
            $args = array(
                'role__in' => array('cool_kid', 'cooler_kid', 'coolest_kid'),
                'exclude' => array($current_user->ID),
            );
            $user_query = new WP_User_Query($args);

            if (!empty($user_query->results)) :
                ?>
                <div class="ck_users">
                <h3>Users List</h3>
                <table>
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Country</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($user_query->results as $user) : ?>
                        <tr>
                            <td><?php echo esc_html($user->user_login); ?></td>
                            <td><?php echo esc_html(get_user_meta($user->ID, 'country', true)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php
        // Additional list of users for "coolest_kid"
        if(!empty($role) && $role == "coolest_kid") :
            $args = array(
                'role__in' => array('cool_kid', 'cooler_kid', 'coolest_kid'),
                'exclude' => array($current_user->ID), // Exclude the current user
            );
            $user_query = new WP_User_Query($args);

            if (!empty($user_query->results)) :
                ?>
                <div class="ck_users">
                <h3>Full Users List</h3>
                <table>
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Country</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($user_query->results as $user) : ?>
                        <tr>
                            <td><?php echo esc_html($user->user_login); ?></td>
                            <td><?php echo esc_html($user->user_email); ?></td>
                            <td><?php echo esc_html(implode(', ', $user->roles)); ?></td>
                            <td><?php echo esc_html(get_user_meta($user->ID, 'country', true)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        <?php endif;
    ?>

    <section id="home-popup">
        <span class="overlay"></span>
        <div class="modal-box">
            <h2 id="popup-title">Register Now</h2>
            <form method="post" action="" id="popup-form">
                <input type="email" name="us_email" class="field text" required>
                <div class="buttons">
                    <input type="submit" name="require" class="field submit" value="Register">
                </div>
            </form>
        </div>
    </section>
    <script>
        jQuery(document).ready(function($){
            const section = document.getElementById("home-popup");
            const overlay = document.querySelector(".overlay");
            const showBtns = document.querySelectorAll(".home-btn");
            const popupTitle = document.getElementById("popup-title");
            const popupFormSubmit = document.querySelector("#popup-form .submit");
            const formSubmit = document.getElementById("popup-form");

            showBtns.forEach(button => {
                button.addEventListener("click", (e) => {
                    const popupType = e.target.getAttribute("data-popup-type");
                    
                    if (popupType === "register") {
                        popupTitle.textContent = "Register Now";
                        popupFormSubmit.value = "Register";
                    } else if (popupType === "login") {
                        popupTitle.textContent = "Login Now";
                        popupFormSubmit.value = "Login";
                    }
                    
                    section.classList.add("active");
                });
            });

            overlay.addEventListener("click", () => {
                section.classList.remove("active");
            });

            formSubmit.addEventListener("submit", (e) => {
                e.preventDefault();

                var user_email = $('input[name="us_email"]').val();
                var form_sub = $('input[name="require"]').val();

                var formData = {
                    action: form_sub.toLowerCase()+'_user',
                    email: user_email,
                    nonce: ajax_obj.nonce
                };

                $.ajax({
                    url: ajax_obj.ajax_url,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('.submit').val('Processing...');
                    },
                    success: function(response) {
                        //let res = JSON.parse(response);
                        if (response.data.success) {
                            if (response.data.redirect_url) {
                                window.location.href = response.data.redirect_url;
                            }
                            alert(response.data.message);
                        } else {
                            alert('Error: ' + response.data.message);
                        }
                        $('.submit').val(form_sub);
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred. Please try again later.');
                        $('.submit').val(form_sub);
                    }
                });
            });
        })
    </script>

    <?php wp_footer(); ?>
</body>
</html>