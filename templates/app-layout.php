<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="sot-tr-app-header">
    <div class="sot-tr-app-container">
        <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
        
        <?php if ( is_user_logged_in() ) : 
            $current_user = wp_get_current_user();
        ?>
            <div class="sot-tr-app-user-info" onclick="this.classList.toggle('active')">
                <span><?php echo esc_html( $current_user->display_name ); ?></span>
                <?php echo get_avatar( $current_user->ID, 32, '', '', array('class' => 'sot-tr-app-user-info-img') ); ?>
                
                <div class="sot-tr-app-user-dropdown">
                    <a href="<?php echo esc_url( admin_url('profile.php') ); ?>" class="sot-tr-app-user-dropdown-link">
                        <span class="dashicons dashicons-admin-users sot-tr-dashicon-align"></span>
                        Edit Profile
                    </a>
                    <hr class="sot-tr-dropdown-divider">
                    <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="sot-tr-app-user-dropdown-link sot-tr-logout-link">
                        <span class="dashicons dashicons-logout sot-tr-dashicon-align"></span>
                        Logout
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>

<script>
// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userInfo = document.querySelector('.sot-tr-app-user-info');
    if (userInfo && !userInfo.contains(event.target)) {
        userInfo.classList.remove('active');
    }
});
</script>

<main class="app-main">
    <div class="sot-tr-app-container">
        <?php
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
</main>

<footer class="sot-tr-app-footer">
    <!-- Footer content hidden as per requirements, but wp_footer() is essential -->
</footer>

<?php wp_footer(); ?>
</body>
</html>
