<?php
session_start();
include 'includes/db.php';

$page = "Add radio";
include 'access_control.php';
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

$error = '';
$success = '';

// Daten für Dropdowns abrufen
$stmtCountry = $conn->prepare("SELECT country_name FROM countries ORDER BY country_name");
$stmtCountry->execute();
$countries = $stmtCountry->fetchAll(PDO::FETCH_ASSOC);

$stmtLanguage = $conn->prepare("SELECT language_name FROM languages ORDER BY language_name");
$stmtLanguage->execute();
$languages = $stmtLanguage->fetchAll(PDO::FETCH_ASSOC);

$stmtGenre = $conn->prepare("SELECT genre_id, genre_name FROM radio_genres ORDER BY genre_name");
$stmtGenre->execute();
$genres = $stmtGenre->fetchAll(PDO::FETCH_ASSOC);

// Formularverarbeitung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $stream_url = trim($_POST['stream_url']);
    $logo_url = trim($_POST['logo_url']);
    $country = trim($_POST['country']);
    $language = trim($_POST['language']);
    $genre_id = trim($_POST['genre']);

    if (empty($name) || empty($description) || empty($stream_url) || empty($logo_url) || empty($country) || empty($language) || empty($genre_id)) {
        $error = "Please fill out all fields.";
    } else {
        // Überprüfung, ob der Radiosender bereits existiert
        $stmt = $conn->prepare("SELECT COUNT(*) FROM stations WHERE name = :name OR stream_url = :stream_url");
        $stmt->execute(['name' => $name, 'stream_url' => $stream_url]);
        
        if ($stmt->fetchColumn() > 0) {
            $error = "<?php echo __('This radio station is already in our database.'); ?>";
        } else {
            // Daten in die stations-Tabelle einfügen
            $stmt = $conn->prepare("INSERT INTO stations (name, description, stream_url, logo_url, country, language, genre_id, status) 
                                    VALUES (:name, :description, :stream_url, :logo_url, :country, :language, :genre_id, 'pending')");
            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'stream_url' => $stream_url,
                'logo_url' => $logo_url,
                'country' => $country,
                'language' => $language,
                'genre_id' => $genre_id
            ]);
            $success = "<?php echo __('Your radio station has been submitted for approval.'); ?>";
        }
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tunespace_cdn@latest/src/css/add-radio.css">


<!-- Hauptbereich -->
<div class="main-content">
    <!-- Benutzerbereich und Zurück-Button -->
    <header class="level mb-6">
        <div class="level-left">
            <h3 class="has-text-white" style="font-size: 24px; font-weight: 800;"><?php echo __('Add radio'); ?></h3>
        </div>
        <div class="level-right hide-on-mobile">
            <?php include_once('includes/account_button.php'); ?>
        </div>
    </header>
    <!-- Rückmeldungen -->
    <?php if ($error): ?>
        <script>showToast('Error', '<?= $error ?>', 'error');</script>
    <?php endif; ?>
    <?php if ($success): ?>
        <script>
            showToast('Success', '<?= $success ?>', 'success');
        </script>
        <?php unset($_SESSION['success']); // Löscht die Erfolgs-Session nach dem Toast ?>
    <?php endif; ?>


    <!-- Anleitungsbox -->
    <div class="notification is-light mb-5 mt-5" 
        style="background-color: rgba(50, 50, 50, 0.7); 
                color: #eee; 
                border-radius: 8px; 
                padding: 20px; 
                backdrop-filter: blur(6px); 
                border: 1px solid rgba(255, 255, 255, 0.2);">
        <h4 class="has-text-white" style="font-size: 22px; font-weight: 800; margin-bottom: 10px;">
            <i class="ri-information-2-fill"></i> <?php echo __('Guidelines for adding a radio station'); ?>
        </h4>
        <p style="font-size: 16px; line-height: 1.5; color: #ddd; margin-bottom: 15px;">
            <?php echo __('Please make sure your radio station follows these guidelines before submission:'); ?>
        </p>
        <ul style="margin-top: 10px; padding-left: 20px; list-style-type: disc; color: #bbb; font-size: 15px;">
            <li><?php echo __('The stream URL must be active and accessible.'); ?></li>
            <li><?php echo __('The station logo URL should be a valid link to a PNG or JPG image.'); ?></li>
            <li><?php echo __('Only family-friendly stations are accepted; explicit content is prohibited.'); ?></li>
        </ul>
        <p style="font-size: 16px; margin-top: 15px; color: #ccc;">
            <?php echo __('Your radio station has been submitted for approval.'); ?>
        </p>
    </div>



    <!-- Formular für das Hinzufügen eines Radiosenders -->
    <form action="" method="POST" class="mt-4">
        <div class="columns is-multiline">
            <!-- Name -->
            <div class="column is-half">
                <div class="field">
                    <label class="label has-text-white"><?php echo __('Radioname'); ?></label>
                    <div class="control has-icons-left">
                        <input class="input custom-input" type="text" name="name" placeholder="Radio Name" required>
                        <span class="icon is-small is-left">
                            <i class="ri-radio-line"></i>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Stream URL -->
            <div class="column is-half">
                <div class="field">
                    <label class="label has-text-white"><?php echo __('Stream URL'); ?></label>
                    <div class="control has-icons-left">
                        <input class="input custom-input" type="url" name="stream_url" placeholder="Stream URL" required>
                        <span class="icon is-small is-left">
                            <i class="ri-link"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="column is-full">
                <div class="field">
                    <label class="label has-text-white"><?php echo __('Description'); ?></label>
                    <div class="control">
                        <textarea class="textarea custom-input" name="description" placeholder="<?php echo __('Description'); ?>" required></textarea>
                    </div>
                </div>
            </div>

            <!-- Logo URL -->
            <div class="column is-half">
                <div class="field">
                    <label class="label has-text-white"><?php echo __('Logo URL'); ?></label>
                    <div class="control has-icons-left">
                        <input class="input custom-input" type="url" name="logo_url" placeholder="Logo URL" required>
                        <span class="icon is-small is-left">
                            <i class="ri-image-line"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Country -->
            <div class="column is-half">
                <div class="field">
                    <label class="label has-text-white"><?php echo __('Country'); ?></label>
                    <div class="control select-container">
                        <select class="custom-select" name="country" required>
                            <option value="" disabled selected><?php echo __('Select country'); ?></option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?php echo __($country['country_name']) ?>"><?php echo __($country['country_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Language -->
            <div class="column is-half">
                <div class="field">
                    <label class="label has-text-white"><?php echo __('Language'); ?></label>
                    <div class="control select-container">
                        <select class="custom-select" name="language" required>
                            <option value="" disabled selected><?php echo __('Select Language'); ?></option>
                            <?php foreach ($languages as $language): ?>
                                <option value="<?php echo __($language['language_name']) ?>"><?php echo __($language['language_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Genre -->
            <div class="column is-half">
                <div class="field">
                    <label class="label has-text-white">Genre</label>
                    <div class="control select-container">
                        <select class="custom-select" name="genre" required>
                            <option value="" disabled selected><?php echo __('Select Genre'); ?></option>
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?= htmlspecialchars($genre['genre_id']) ?>"><?php echo __($genre['genre_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="field is-grouped is-grouped-left">
            <div class="control">
                <button type="submit" class="button is-primary"><i class="ri-checkbox-circle-fill mr-2"></i> <?php echo __('Submit for approval'); ?></button>
            </div>
        </div>
    </form>
</div>

<?php include_once('includes/footer.php'); ?>