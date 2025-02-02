
<!-- Podcasts anzeigen -->
<div class="columns is-multiline" style="gap: 0.5rem;">
    <?php if (count($podcasts) > 0): ?>
        <?php foreach ($podcasts as $podcast): ?>
            <?php
                // Sicherheitsprüfungen und Variablen
                $podcast_id = htmlspecialchars($podcast['podcast_id']);
                $podcast_title = htmlspecialchars($podcast['title'] ?? 'Unknown Podcast');
                $podcast_description = htmlspecialchars($podcast['description'] ?? 'No description available.');
                $category_name = htmlspecialchars($podcast['category_name'] ?? 'Uncategorized');
                $podcast_image = htmlspecialchars($podcast['image'] ?? 'src/img/no_image.jpg'); // Standardbild
                $is_adult = !empty($podcast['is_adult']) && $podcast['is_adult'] == 1;
                ?>

                <!-- Podcast-Karte -->
                <div class="column is-one-quarter" style="max-width: 340px;">
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
                        height: var(--card-height, 350px);
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
                        <div class="card-content" style="padding: 1rem;">
                            <h4 style="
                                font-size: 16px; 
                                color: #fff; 
                                font-weight: bold; 
                                margin-bottom: 0.5rem;">
                                <?= $podcast_title; ?>
                            </h4>

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

        <?php endforeach; ?>
    <?php endif; ?>
</div>
