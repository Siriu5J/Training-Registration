<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        .app-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 30px;
            text-align: center;
        }
        .app-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .app-header a {
            text-decoration: none;
            color: inherit;
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
    </div>
</header>

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
