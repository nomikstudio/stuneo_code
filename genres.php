<?php 
$page = "Genres";
include 'access_control.php';
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Alle Genres abrufen
$stmt = $conn->prepare("SELECT * FROM radio_genres ORDER BY genre_name");
$stmt->execute();
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hauptbereich -->
<div class="main-content">
    <!-- Header mit Titel und Zurück-Button -->
    <header class="level mb-6">
        <div class="level-left">
            <h1 class="has-text-white" style="font-size: 28px; font-weight: bold;"><?php echo __("Genres"); ?></h1>
        </div>
        <div class="level-right hide-on-mobile">
            <?php include_once('includes/account_button.php'); ?>
        </div>
    </header>

    <!-- Genre-Buttons -->
    <div class="tabs is-toggle is-centered mt-5">
        <ul class="genres-tabs" style="flex-wrap: wrap; gap: 12px;">
            <li class="genre-tab is-active" data-genre-id="0">
                <a><?php echo __("All"); ?></a>
            </li>
            <?php foreach ($genres as $genre): ?>
                <li class="genre-tab" data-genre-id="<?= htmlspecialchars($genre['genre_id']); ?>">
                    <a><?= htmlspecialchars(__($genre['genre_name'])); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Bereich für die Stationsanzeige -->
    <section id="stations-container" class="columns is-multiline mt-5">
        <!-- Die Stationskarten werden hier per AJAX geladen -->
    </section>
</div>

<link rel="stylesheet" href="src/css/genres.css?v=<?= time(); ?>">
<script src="src/js/genres.js?v=<?= time(); ?>"></script>

<?php include_once('includes/footer.php'); ?>