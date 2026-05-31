<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        .app-header {
            padding: 16px 0;
            background-color: #012552;
            margin-bottom: 30px;
            color: #fff;
        }
        .app-header .app-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .app-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .app-header a {
            text-decoration: none;
            color: #fff;
        }
        .app-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            cursor: pointer;
            position: relative;
            padding: 6px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
            user-select: none;
            color: #fff;
        }
        .app-user-info:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
        }
        .app-user-info.active {
            border-color: #fecc00;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 1px #fecc00;
        }
        .app-user-info img {
            border-radius: 50%;
            display: block;
            border: 1px solid #eee;
        }
        .app-user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            padding: 8px 0;
            min-width: 180px;
            z-index: 1000;
            display: none;
        }
        .app-user-info.active .app-user-dropdown {
            display: block;
        }
        .app-user-dropdown a {
            display: block;
            padding: 8px 16px;
            font-size: 0.9rem;
            color: #333 !important;
            transition: background 0.2s;
        }
        .app-user-dropdown a:hover {
            background: #f0f0f0;
        }
        .app-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .app-footer {
            margin-top: 50px;
            padding: 20px;
            text-align: center;
            font-size: 0.8rem;
            color: #666;
        }
    </style>
</head>
<body <?php body_class(); ?>>

<header class="app-header">
    <div class="app-container">
        <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
        
        <?php if ( is_user_logged_in() ) : 
            $current_user = wp_get_current_user();
        ?>
            <div class="app-user-info" onclick="this.classList.toggle('active')">
                <span><?php echo esc_html( $current_user->display_name ); ?></span>
                <?php echo get_avatar( $current_user->ID, 32 ); ?>
                
                <div class="app-user-dropdown">
                    <a href="<?php echo esc_url( admin_url('profile.php') ); ?>">
                        <span class="dashicons dashicons-admin-users" style="font-size: 16px; width: 16px; height: 16px; margin-right: 8px; vertical-align: text-bottom;"></span>
                        Edit Profile
                    </a>
                    <hr style="margin: 4px 0; border: 0; border-top: 1px solid #eee;">
                    <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" style="color: #d63638 !important;">
                        <span class="dashicons dashicons-logout" style="font-size: 16px; width: 16px; height: 16px; margin-right: 8px; vertical-align: text-bottom;"></span>
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
    const userInfo = document.querySelector('.app-user-info');
    if (userInfo && !userInfo.contains(event.target)) {
        userInfo.classList.remove('active');
    }
});
</script>

<main class="app-main">
    <div class="app-container">
        <?php
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
        ?>
    </div>
</main>

<footer class="app-footer">
    <!-- Footer content hidden as per requirements, but wp_footer() is essential -->
</footer>

<?php wp_footer(); ?>
</body>
</html>
