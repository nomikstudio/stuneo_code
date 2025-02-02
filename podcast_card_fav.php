
<!-- Podcasts anzeigen -->
<?php
                // Sicherheitsprüfungen und Variablen
                $podcast_id = htmlspecialchars($podcast['podcast_id']);
                $podcast_title = htmlspecialchars_decode($podcast['title'] ?? 'Unknown Podcast');
                $podcast_description = htmlspecialchars_decode($podcast['description'] ?? 'No description available.');
                $category_name = htmlspecialchars($podcast['category_name'] ?? 'Uncategorized');
                $podcast_image = htmlspecialchars($podcast['image'] ?? 'src/img/no_image.jpg'); // Standardbild
                $is_adult = !empty($podcast['is_adult']) && $podcast['is_adult'] == 1;
                ?>

            <!-- Podcast-Karte -->
            <div class="column is-full-mobile is-one-quarter-desktop" style="display: flex;">
                <div class="podcast-card" style="
                    position: relative; 
                    background-color: rgba(31, 31, 31, 0.7); 
                    backdrop-filter: blur(8px);
                    border: 1px solid #333333; 
                    border-radius: 12px; 
                    overflow: hidden; 
                    transition: transform 0.2s ease;
                    display: flex;
                    flex-direction: column;
                    height: var(--card-height, 350px); /* Einheitliche Höhe */
                    width: 100%;
                ">

                    <?php if ($is_adult): ?>
                        <!-- Badge for Adult Content -->
                        <span style="
                            position: absolute;
                            top: 10px;
                            left: 10px;
                            background-color: #ff0000;
                            color: #fff;
                            font-size: 12px;
                            font-weight: bold;
                            padding: 0.2rem 0.5rem;
                            border-radius: 5px;
                            z-index: 10;">
                            18+
                        </span>
                    <?php endif; ?>

                    <!-- Bild -->
                    <figure class="image" style="
                        height: 200px; 
                        overflow: hidden; 
                        margin: 0;">
                        <img src="<?= $podcast_image; ?>" alt="<?= $podcast_title; ?>" style="
                            object-fit: cover; 
                            width: 100%; 
                            height: 100%; 
                            transition: transform 0.3s ease;">
                    </figure>

                    <!-- Inhalt -->
                    <div class="card-content" style="padding: 1rem; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <!-- Titel und Favoriten-Button -->
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h4 style="font-size: 16px; color: #fff; font-weight: bold; margin-bottom: 0.5rem;">
                                    <?= htmlspecialchars($podcast_title); ?>
                                </h4>
                                <!-- Favoriten-Button -->
                                <button 
                                    class="btn btn-outline-light btn-sm favorite-btn" 
                                    data-podcast-id="<?= htmlspecialchars($podcast_id); ?>" 
                                    style="border: none; background: none; padding: 0; cursor: pointer;">
                                    <i 
                                        class="<?= in_array($podcast_id, $favoritePodcastsArray) ? 'ri-heart-fill ri-lg' : 'ri-heart-line ri-lg'; ?>" 
                                        id="heart-icon-<?= htmlspecialchars($podcast_id); ?>" 
                                        style="<?= in_array($podcast_id, $favoritePodcastsArray) ? 'color: white;' : ''; ?>;">
                                    </i>
                                </button>
                            </div>

                            <!-- Kategorie-Badge -->
                            <span style="
                                display: inline-block;
                                font-size: 12px;
                                color: #ccc;
                                border: 1px solid #555;
                                padding: 0.2rem 0.5rem;
                                border-radius: 12px;
                                background: rgb(43, 43, 43);
                                margin-bottom: 1rem;">
                                <?= $category_name; ?>
                            </span>

                            <p style="
                                font-size: 13px; 
                                color: #b0b0b0; 
                                line-height: 1.4; 
                                margin-bottom: 1rem; 
                                max-height: 40px; 
                                overflow: hidden; 
                                text-overflow: ellipsis;">
                                <?= $podcast_description; ?>
                            </p>
                        </div>
                        <!-- Buttons -->
                        <div class="buttons" style="
                            display: flex; 
                            justify-content: space-between; 
                            align-items: center;">
                            <!-- Play Button -->
                            <a href="podcast/<?= $podcast_id; ?>" class="button is-small" style="
                                background-color: #ff6f01; 
                                color: #fff; 
                                font-size: 14px; 
                                border-radius: 20px; 
                                padding: 0.5rem 1rem;">
                                <i class="ti ti-player-play-filled" style="margin-right: 0.5rem;"></i>
                                <?= __('Play') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
