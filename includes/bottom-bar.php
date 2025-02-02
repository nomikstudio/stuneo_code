<!-- Bottom Bar für mobile Geräte -->
<div class="bottom-bar" id="mobileBottomBar">
    <div class="bottom-bar-item">
        <a href="index.php" class="icon-link <?php echo ($page == 'Home') ? 'active' : ''; ?>">
            <i class="<?php echo ($page == 'Home') ? 'ri-home-fill' : 'ri-home-line'; ?>"></i>
            <p><?php echo __('Home'); ?></p>
        </a>
    </div>
    <div class="bottom-bar-item">
        <a href="genres.php" class="icon-link <?php echo ($page == 'Genres') ? 'active' : ''; ?>">
            <i class="<?php echo ($page == 'Genres') ? 'ri-music-fill' : 'ri-music-line'; ?>"></i>
            <p><?php echo __('Genres'); ?></p>
        </a>
    </div>
    <div class="bottom-bar-item">
        <a href="favorites.php" class="icon-link <?php echo ($page == 'Favorites') ? 'active' : ''; ?>">
            <i class="<?php echo ($page == 'Favorites') ? 'ri-heart-fill' : 'ri-heart-line'; ?>"></i>
            <p><?php echo __('Favorites'); ?></p>
        </a>
    </div>
    <div class="bottom-bar-item">
        <a href="account.php" class="icon-link <?php echo ($page == 'Account') ? 'active' : ''; ?>">
            <i class="<?php echo ($page == 'Account') ? 'ri-user-fill' : 'ri-user-line'; ?>"></i>
            <p> <?= $username ?></p>
        </a>
    </div>
</div>
<link rel="stylesheet" href="src/css/bottom_bar.css?v=<?= time(); ?>">

