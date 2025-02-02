<?php 
$page = "Discover";
include 'access_control.php';
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Benutzer-ID aus der Session
$user_id = $_SESSION['user_data']['user_id'];

// Filter abrufen
$filterCountry = $_GET['country'] ?? null;
$filterLanguage = $_GET['language'] ?? null;

// Basis-SQL für Stationen mit Filter
$sql = "SELECT * FROM stations WHERE status = 'approved'";
$params = [];

// Filter hinzufügen
if ($filterCountry) {
    $sql .= " AND country = :country";
    $params['country'] = $filterCountry;
}
if ($filterLanguage) {
    $sql .= " AND language = :language";
    $params['language'] = $filterLanguage;
}

// Stationen alphabetisch sortieren
$sql .= " ORDER BY name ASC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$newStations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Abrufen der Favoriten des Benutzers
$fav_stmt = $conn->prepare("SELECT station_id FROM favorites WHERE user_id = :user_id");
$fav_stmt->execute(['user_id' => $user_id]);
$favoritesArray = $fav_stmt->fetchAll(PDO::FETCH_COLUMN); // Liste mit Favoriten-IDs

// Länder und Sprachen für Filter abrufen
$countries_stmt = $conn->prepare("SELECT DISTINCT country FROM stations WHERE status = 'approved' ORDER BY country ASC");
$countries_stmt->execute();
$countries = $countries_stmt->fetchAll(PDO::FETCH_COLUMN);

$languages_stmt = $conn->prepare("SELECT DISTINCT language FROM stations WHERE status = 'approved' ORDER BY language ASC");
$languages_stmt->execute();
$languages = $languages_stmt->fetchAll(PDO::FETCH_COLUMN);
?>


<!-- Hauptbereich -->
<!-- Hauptbereich -->
<div class="main-content" id="content">
    <!-- Header mit Titel -->
    <header class="level mb-6">
        <div class="level-left">
            <h3 class="has-text-white" style="font-size: 24px; font-weight: 800;"><?php echo __('Discover'); ?></h3>
        </div>
        <div class="level-right hide-on-mobile">
            <?php include_once('includes/account_button.php'); ?>
        </div>
    </header>

    <!-- Filterbereich -->
    <div class="filters mb-4">
    <form method="GET" action="discover.php" class="columns is-multiline">
        <!-- Länderfilter -->
        <div class="column is-one-third">
            <div class="field">
                <label class="label has-text-white"><?php echo __('Country'); ?></label>
                <div class="control">
                <div class="select-container is-fullwidth has-icons-left">
                    <select name="country" class="custom-select">
                            <option value=""><?php echo __('All Countries'); ?></option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= htmlspecialchars($country); ?>" <?= $filterCountry === $country ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($country); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sprachenfilter -->
        <div class="column is-one-third">
            <div class="field">
                <label class="label has-text-white"><?php echo __('Language'); ?></label>
                <div class="control">
                <div class="select-container is-fullwidth has-icons-left">
                    <select name="language" class="custom-select">
                            <option value=""><?php echo __('All Languages'); ?></option>
                            <?php foreach ($languages as $language): ?>
                                <option value="<?= htmlspecialchars($language); ?>" <?= $filterLanguage === $language ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($language); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter anwenden -->
        <div class="column is-one-third">
            <div class="field">
                <label class="label has-text-white">&nbsp;</label>
                <div class="control">
                    <button class="button is-light is-fullwidth"><?php echo __('Apply Filters'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>



    <!-- Stationen anzeigen -->
    <div class="columns is-multiline">
        <?php if (count($newStations) > 0): ?>
            <?php foreach ($newStations as $station): ?>
                <?php
                $station_id = htmlspecialchars($station['station_id']);
                $isFavorite = in_array($station_id, $favoritesArray);
                ?>
                
                <!-- Include station_card.php -->
                <?php include 'station_card.php'; ?>
                
            <?php endforeach; ?>
        <?php else: ?>
            <p class="column is-full has-text-white"><?php echo __('No stations available to discover.'); ?></p>
        <?php endif; ?>
    </div>
</div>


<?php include_once('includes/footer.php'); ?>
