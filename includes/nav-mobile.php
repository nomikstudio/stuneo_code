<?php
// Version und Benutzername laden
$version = isset($site_settings['version']) ? $site_settings['version'] : '1.0';
$username = isset($_SESSION['user_data']['username']) ? htmlspecialchars($_SESSION['user_data']['username']) : 'My account';
$country = isset($_SESSION['user_data']['country']) ? htmlspecialchars($_SESSION['user_data']['country']) : 'My country';
?>
<!-- Navbar für mobile Geräte -->
<nav class="navbar mobile-navbar">
    <div class="navbar-brand">
            <a class="logo-text" href="."><img src="src/img/stuneo_logo_light.svg" class="mt-1" width="150px" alt="stuneo" /></a>
        <?php if ($is_logged_in): ?>
        <span class="navbar-burger burger" data-target="navbarMenu" aria-label="menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
            <span></span>

        </span>
        <?php else: ?>
        <!-- Button für nicht angemeldete Benutzer -->
        <div class="buttons">
            <a href="login" class="button btn-login"><?php echo __('Login'); ?></a>
         </div>
        <?php endif; ?>
    </div>

    <?php if ($is_logged_in): ?>
    <div id="navbarMenu" class="navbar-menu is-hidden">
        <div class="navbar-start">
            <a class="navbar-item <?php echo ($page == 'Home') ? 'active' : ''; ?>" href="index">
                <i class="<?php echo ($page == 'Home') ? 'ri-home-fill' : 'ri-home-line'; ?>"></i> <?php echo __('Home'); ?>
            </a>
            <a class="navbar-item <?php echo ($page == 'Discover') ? 'active' : ''; ?>" href="discover">
                <i class="<?php echo ($page == 'Discover') ? 'ri-compass-discover-fill' : 'ri-compass-discover-line'; ?>"></i> <?php echo __('Discover'); ?>
            </a>
            <a class="navbar-item <?php echo ($page == 'Genres') ? 'active' : ''; ?>" href="genres">
                <i class="<?php echo ($page == 'Genres') ? 'ri-music-fill' : 'ri-music-line'; ?>"></i> <?php echo __('Genres'); ?>
            </a>
            <a class="navbar-item <?php echo ($page == 'Favorites') ? 'active' : ''; ?>" href="favorites">
                <i class="<?php echo ($page == 'Favorites') ? 'ri-heart-fill' : 'ri-heart-line'; ?>"></i> <?php echo __('Favorites'); ?>
            </a>
            <a class="navbar-item <?php echo ($page == 'New Stations') ? 'active' : ''; ?>" href="new-stations">
                <i class="<?php echo ($page == 'New Stations') ? 'ri-music-ai-fill' : 'ri-music-ai-line'; ?>"></i> <?php echo __('New Stations'); ?>
            </a>
            <a class="navbar-item <?php echo ($page == 'My country stations') ? 'active' : ''; ?>" href="my-country-stations">
                <i class="<?php echo ($page == 'My country stations') ? 'ri-earth-fill' : 'ri-earth-line'; ?>"></i> <?php echo __('Stations in'); ?> <?= $country ?>
            </a>
                <a class="navbar-item <?php echo ($page == 'Podcasts') ? 'active' : ''; ?>" href="podcasts">
                <i class="<?php echo ($page == 'Podcasts') ? 'ri-base-station-fill' : 'ri-base-station-line'; ?>"></i> <?php echo __('Podcasts'); ?>
            </a>     
            <a class="navbar-item <?php echo ($page == 'Add radio') ? 'active' : ''; ?>" href="add-radio">
                <i class="<?php echo ($page == 'Add radio') ? 'ri-radio-2-fill' : 'ri-radio-2-line'; ?>"></i> <?php echo __('Add radio'); ?>
            </a>
            <a class="navbar-item <?php echo ($page == 'Account') ? 'active' : ''; ?>" href="account">
                <i class="<?php echo ($page == 'Account') ? 'ri-user-fill' : 'ri-user-line'; ?>"></i> <?php echo __('Account'); ?>
            </a>
            <a class="navbar-item <?php echo ($page == 'Logout') ? 'active' : ''; ?>" href="logout">
                <i class="<?php echo ($page == 'Logout') ? 'ri-logout-box-fill' : 'ri-logout-box-line'; ?>"></i> <?php echo __('Logout'); ?>
            </a>
        </div>
    </div>
    <?php else: ?>
    <?php endif; ?>
</nav>

<!-- CSS zur Anpassung des Layouts -->
<link rel="stylesheet" href="src/css/nav-mobile.css?v=<?= time(); ?>">

