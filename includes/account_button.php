<div class="level-right hide-on-mobile">
<!-- Download-Button -->
<a href="<?php echo $downloadLink; ?>" id="downloadButton" style="
    display: flex; 
    align-items: center; 
    justify-content: center; 
    gap: 8px;
    background: rgba(31, 31, 31, 0.7); 
    backdrop-filter: blur(8px); 
    color: white; 
    font-weight: bold; 
    padding: 10px 16px; 
    border-radius: 50px; /* Mehr Rundung */
    text-decoration: none; 
    font-size: 14px; 
    border: 1px solid rgba(255, 255, 255, 0.1); 
">
    <i class="ri-download-line" id="downloadIcon" style="font-size: 18px; transition: transform 0.3s ease;"></i>
    <span id="downloadButtonText"><?php echo $downloadText; ?></span>
</a>



            <?php if ($is_logged_in): ?>
                <div class="user-account-dropdown" style="position: relative; display: inline-block;">
                    <!-- Benutzerkonto mit Hintergrund -->
                    <div class="user-icon-container" onclick="toggleDropdown()" style="
                        display: flex; 
                        align-items: center; 
                        cursor: pointer; 
                        padding: 6px; 
                        background: rgba(31, 31, 31, 0.7); 
                        backdrop-filter: blur(8px); 
                        border-radius: 50px; /* Mehr Rundung */
                        border: 1px solid rgba(255, 255, 255, 0.1);
                        gap: 5px; /* Weniger Abstand */
                    ">
                        <div style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background-color: #ff6f01; color: white; font-size: 14px;">
                            <?php echo strtoupper(substr($_SESSION['user_data']['username'] ?? 'Guest', 0, 1)); ?>
                        </div>
                        <i class="ri-arrow-down-s-line" style="font-size: 16px; color: white;"></i>
                    </div>

                    <!-- Dropdown-Menü -->
                    <div id="dropdownMenu" class="dropdown-content" style="
                        display: none;
                        position: absolute;
                        top: 50px; /* Anpassung für kleinere Größe */
                        right: 0;
                        background-color: rgba(31, 31, 31, 0.8);
                        backdrop-filter: blur(10px);
                        border-radius: 16px; /* Mehr Rundung für das Dropdown */
                        border: 1px solid rgba(255, 255, 255, 0.1);
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
                        z-index: 1000;
                        overflow: hidden;
                    ">
                        <ul style="list-style: none; margin: 0; padding: 8px; text-align: left; color: white; font-size: 14px;">
                            <li style="padding: 6px 10px; cursor: pointer;">
                                <a href="account" style="text-decoration: none; color: white;"><?php echo __('Account'); ?></a>
                            </li>
                            <li style="padding: 6px 10px; cursor: pointer;">
                                <a href="favorites" style="text-decoration: none; color: white;"><?php echo __('Favorites'); ?></a>
                            </li>
                            <li style="padding: 6px 10px; cursor: pointer;">
                                <a href="logout" style="text-decoration: none; color: white;"><?php echo __('Logout'); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
        <?php endif; ?>
        </div>