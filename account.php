<?php
// page info and includes
$page = "Account";
include 'access_control.php';
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Datenbankverbindung und Session
$user_id = $_SESSION['user_data']['user_id'];
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db.php';

// Benutzerdaten abrufen
$stmt = $conn->prepare("SELECT username, email, firstname, lastname, updated_at, created_at, plan_id, birthdate, language, system_language, country, is_2fa_enabled, stripe_customer_id FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
    $errors[] = __("Could not load user data.");
    $user_data = [];
}

// Alle Sprachen abrufen
$languageStmt = $conn->prepare("SELECT language_code, language_name FROM languages ORDER BY language_name");
$languageStmt->execute();
$languages = $languageStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Alle Länder abrufen
$countryStmt = $conn->prepare("SELECT country_code, country_name FROM countries ORDER BY country_name");
$countryStmt->execute();
$countries = $countryStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

$errors = [];
$success = false;

// Verarbeitung der Formulardaten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $birthdate = isset($_POST['birthdate']) ? trim($_POST['birthdate']) : '';
    $language = isset($_POST['language']) ? trim($_POST['language']) : '';
    $system_language = isset($_POST['system_language']) ? trim($_POST['system_language']) : '';
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';
    $is_2fa_enabled = isset($_POST['is_2fa_enabled']) ? 1 : 0;

    // Validierung
    if (empty($username)) $errors[] = __('Username cannot be empty');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = __('Invalid email address.');
    if (empty($firstname)) $errors[] = __('Firstname is required.');
    if (empty($lastname)) $errors[] = __('Lastname is required.');
    if (empty($birthdate)) $errors[] = __('Birthday is required.');

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email, firstname = :firstname, lastname = :lastname, language = :language, birthdate = :birthdate, system_language = :system_language, country = :country, is_2fa_enabled = :is_2fa_enabled WHERE user_id = :user_id");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'language' => $language,
                'birthdate' => $birthdate,
                'system_language' => $system_language,
                'country' => $country,
                'is_2fa_enabled' => $is_2fa_enabled,
                'user_id' => $user_id
            ]);

            // Passwort aktualisieren
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                $stmt->execute(['password' => $hashed_password, 'user_id' => $user_id]);
            }

            // Aktualisiere nur die system_language in der Session
            $_SESSION['user_data']['system_language'] = $system_language;
            $_SESSION['user_data']['is_2fa_enabled'] = $is_2fa_enabled;

            $success = true;
        } catch (PDOException $e) {
            $errors[] = __('Update error: ') . $e->getMessage();
        }
    }
}

// Plan abrufen
$stmt = $conn->prepare("SELECT p.plan_name, p.price FROM users u JOIN plans p ON u.plan_id = p.plan_id WHERE u.user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$plan_data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
?>
    <style>
            input[type="date"]::-webkit-calendar-picker-indicator {
            display: none;
        }
    </style>
<!-- Hauptbereich -->
<div class="main-content">
    <!-- Benutzerbereich und Zurück-Button -->
    <header class="level mb-6">
        <div class="level-left">
            <h3 class="has-text-white" style="font-size: 24px; font-weight: 800;">
                <?php echo __("Account settings"); ?>
            </h3>
        </div>
        <div class="level-right hide-on-mobile">
            <?php include_once('includes/account_button.php'); ?>
        </div>
    </header>

    <!-- Benutzerinformationen und Tarif -->
    <div class="columns">
        <div class="column is-half">
            <div class="pricing-card" style="
                background-color: rgba(31, 31, 31, 0.7);
                backdrop-filter: blur(8px);
                border: 1px solid #333333;
                padding: 15px; border-radius: 8px; max-width: 400px;">
                <h2 class="title is-6 mt-3 has-text-white" style="font-size: 20px;">
                <i class="ri-account-circle-fill ri-xl mr-2"></i> <?php echo __("Account Details"); ?>
                </h2>

                <div class="account-info">
                    <p><strong class="has-text-white"><?php echo __("Username"); ?>:</strong> <?php echo htmlspecialchars($user_data['username']); ?></p>
                    <p><strong class="has-text-white"><?php echo __("Email"); ?>:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                    <p><strong class="has-text-white"><?php echo __("Updated at"); ?>:</strong> <?php echo date("d.m.Y H:i", strtotime($user_data['updated_at'])); ?></p>
                    <p><strong class="has-text-white"><?php echo __("On tunespace since"); ?>:</strong> <?php echo date("d.m.Y", strtotime($user_data['created_at'])); ?></p>
                </div>
            </div>
            <div class="pricing-card" style="background-color: rgba(31, 31, 31, 0.7);
                backdrop-filter: blur(8px);
                border: 1px solid #333333;
                padding: 15px; border-radius: 8px; max-width: 400px;">
                <h2 class="title is-6 mt-3 has-text-white" style="font-size: 20px;">
                    <i class="ri-money-euro-circle-fill ri-xl mr-2"></i> <?php echo __("Pricing Plan"); ?>
                </h2>
                <div class="pricing-plan">
                    <p><strong class="has-text-white"><?php echo __("Plan"); ?>:</strong> <?php echo htmlspecialchars($plan_data['plan_name']); ?></p>
                    <p><strong class="has-text-white"><?php echo __("Price"); ?>:</strong> €<?php echo htmlspecialchars(number_format($plan_data['price'], 2)); ?>/<?php echo __("Month"); ?></p>
                </div>
            </div>

            <h2 class="has-text-white title is-5 version" style="margin-top: 40px;"><?php echo __("Site information"); ?></h2>
            <div class="pricing-card" style="background-color: rgba(31, 31, 31, 0.7);
                backdrop-filter: blur(8px);
                border: 1px solid #333333;
                padding: 15px; border-radius: 8px; max-width: 400px;">
                <div class="pricing-plan">
                <p class="has-text-white version"><strong class="has-text-white"><?php echo __("Web Version"); ?>:</strong> v<?php echo htmlspecialchars($version); ?>
                <p class="has-text-white version" style="font-size: 13px;"><?php echo __("For more informations click"); ?> <a href="https://stuneo.com/changelog" target="_blank"><u><?php echo __("here"); ?></u></a></p>
                </div>
            </div>
           
        </div>

        <div class="column is-half ">
            <h4 class="title is-5 has-text-white"><?php echo __("Settings"); ?></h4>
            <form action="" method="POST">
                <!-- Username -->
                <div class="field">
                    <label class="label has-text-white"><?php echo __("Username"); ?></label>
                    <div class="control has-icons-left">
                        <input type="text" class="input" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                        <span class="icon is-small is-left">
                            <i class="ri-user-line"></i>
                        </span>
                    </div>
                </div>
                <!-- Firstname -->
                <div class="field">
                    <label class="label has-text-white"><?php echo __("Firstname"); ?></label>
                    <div class="control has-icons-left">
                        <input type="text" class="input" name="firstname" value="<?php echo htmlspecialchars($user_data['firstname']); ?>" placeholder="<?php echo __("Enter your firstname"); ?>" required>
                        <span class="icon is-small is-left">
                            <i class="ri-user-line"></i>
                        </span>
                    </div>
                </div>
                <!-- Lastname -->
                <div class="field">
                    <label class="label has-text-white"><?php echo __("Lastname"); ?></label>
                    <div class="control has-icons-left">
                        <input type="text" class="input" name="lastname" value="<?php echo htmlspecialchars($user_data['lastname']); ?>" placeholder="<?php echo __("Enter your lastname"); ?>" required>
                        <span class="icon is-small is-left">
                            <i class="ri-user-line"></i>
                        </span>
                    </div>
                </div>
                <!-- Email -->
                <div class="field">
                    <label class="label has-text-white"><?php echo __("Email"); ?></label>
                    <div class="control has-icons-left">
                        <input type="email" class="input" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        <span class="icon is-small is-left">
                            <i class="ri-mail-line"></i>
                        </span>
                    </div>
                </div>
                <!-- Email -->
                <div class="field">
                    <label class="label has-text-white"><?php echo __("Birthdate"); ?></label>
                    <div class="control has-icons-left">
                        <input type="date" class="input" name="birthdate" value="<?php echo htmlspecialchars($user_data['birthdate']); ?>" required>
                        <span class="icon is-small is-left">
                            <i class="ri-id-card-line"></i>
                        </span>
                    </div>
                </div>
                <!-- New password -->
                <div class="field">
                    <label class="label has-text-white"><?php echo __("New password"); ?></label>
                    <div class="control has-icons-left">
                        <input type="password" class="input" name="new_password" placeholder="<?php echo __("Enter new password"); ?>">
                        <span class="icon is-small is-left">
                            <i class="ri-lock-line"></i>
                        </span>
                    </div>
                </div>
                
                <!-- Language and Country Fields Side-by-Side -->
                <div class="field is-grouped is-grouped-multiline">
                    <!-- Language -->
                    <div class="control is-expanded">
                        <label class="label has-text-white"><?php echo __("Language"); ?></label>
                        <div class="select-container is-fullwidth has-icons-left">
                            <select name="language" class="custom-select">
                                <?php foreach ($languages as $lang): ?>
                                    <option value="<?php echo $lang['language_code']; ?>" <?php echo ($lang['language_code'] == $user_data['language']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($lang['language_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Country -->
                    <div class="control is-expanded">
                        <label class="label has-text-white"><?php echo __("Country"); ?></label>
                        <div class="select-container is-fullwidth has-icons-left">
                            <select name="country" class="custom-select">
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?php echo $country['country_code']; ?>" <?php echo ($country['country_code'] == $user_data['country']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($country['country_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- System Language Auswahl -->
                <div class="field">
                    <label class="label has-text-white"><?php echo __("System Language"); ?></label>
                    <div class="select-container is-fullwidth has-icons-left">
                    <select name="system_language" class="custom-select">
                            <option value="en_US" <?php echo ($user_data['system_language'] == 'en_US') ? 'selected' : ''; ?>>English</option>
                            <option value="de_DE" <?php echo ($user_data['system_language'] == 'de_DE') ? 'selected' : ''; ?>>Deutsch</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label class="label  has-text-white"><?= __('Enable Two-Factor Authentication') ?></label>
                    <div class="control  has-text-white">
                        <label class="checkbox">
                            <input type="checkbox" name="is_2fa_enabled" <?= $user_data['is_2fa_enabled'] ? 'checked' : '' ?>>
                            <?= __('Enable 2FA for added account security') ?>
                        </label>
                    </div>
                </div>

                <!-- Save button -->
                <div class="field">
                    <button type="submit" class="button is-white is-fullwidth"><?php echo __("Save"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $error): ?>
        <script>showToast('Error', '<?= htmlspecialchars(__($error)) ?>', 'error');</script>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($success) && $success): ?>
    <script>
        showToast('Success', '<?php echo __("Settings saved"); ?>', 'success');
    </script>
<?php endif; ?>

<link rel="stylesheet" href="src/css/account.css?v=<?= time(); ?>">

<?php include_once('includes/footer.php'); ?>