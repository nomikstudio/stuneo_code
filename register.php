<?php
session_start();

// Überprüfen, ob der Benutzer bereits angemeldet ist
if (isset($_SESSION['user_data']) && !empty($_SESSION['user_data']['user_id'])) {
    // Wenn der Benutzer angemeldet ist, weiterleiten
    header("Location: index");
    exit;
}
include 'includes/db.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Browser-Sprache erkennen
$browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$supportedLanguages = ['de', 'en'];
$language = in_array($browserLanguage, $supportedLanguages) ? $browserLanguage : 'en';

// Sprachdatei laden
$translations = [];
if ($language === 'de') {
    $translations = include 'languages/de_DE/register.php';
} else {
    $translations = include 'languages/en_US/register.php';
}

$error = '';
$success = '';

// Dropdown für Sprachen und Länder
$stmtLang = $conn->prepare("SELECT * FROM languages");
$stmtLang->execute();
$languages = $stmtLang->fetchAll(PDO::FETCH_ASSOC);

$stmtCountry = $conn->prepare("SELECT * FROM countries");
$stmtCountry->execute();
$countries = $stmtCountry->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $birthdate = trim($_POST['birthdate']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $language = trim($_POST['language']);
    $country = trim($_POST['country']);
    $system_language = trim($_POST['system_language']);


    if (empty($username) || empty($firstname) || empty($lastname) || empty($email) || empty($birthdate) || empty($password) || empty($language) || empty($country)) {
        $error = "Please fill out all fields.";
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email OR username = :username");
        $stmt->execute(['email' => $email, 'username' => $username]);

        if ($stmt->fetchColumn() > 0) {
            $error = "Username or email already in use.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, firstname, lastname, email, birthdate, password, language, system_language, country, plan_id) 
                                    VALUES (:username, :firstname, :lastname, :email, :birthdate, :password, :language, :system_language, :country, :plan_id)");
            $stmt->execute([
                'username' => $username,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'birthdate' => $birthdate,
                'password' => $password,
                'language' => $language,
                'country' => $country,
                'system_language' => $system_language,
                'plan_id' => 1 
            ]);

            // Sprachdatei laden
            $languageCode = $language === 'de' ? 'de_DE' : 'en_US';
            $translations = include "languages/{$languageCode}/register.php";
            
            // E-Mail-Inhalt
            $subject = $translations['welcome_subject'];
            $htmlContent = "
            <html>
                <body style=\"font-family: Arial, sans-serif; color: #333; background-color: #f9f9f9; padding: 0; margin: 0;\">
                    <div style=\"max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden;\">
                       <img src=\"https://stuneo.com/stuneo_logo_color.png\" alt=\"stuneo\" style=\"height: auto; width: 150px; object-fit: contain; margin-top: 15px; margin-bottom: 15px; padding-left: 15px;\">
                        <div style=\"padding: 20px;\">
                            <h2 style=\"color: #333; font-size: 24px; margin-bottom: 10px;\">{$translations['welcome_message']}, $firstname!</h2>
                            <p>{$translations['registration_details']}</p>
                            <div style=\"background-color: #f3f3f3; padding: 15px; border-radius: 6px; margin-top: 20px;\">
                                <p><strong>{$translations['username']}</strong> $username</p>
                                <p><strong>{$translations['plan']}</strong> {$translations['plan_free']}</p>
                            </div>
                            <p>{$translations['start_exploring']}</p>
                            <p>{$translations['contact_us']} <a href='mailto:help@stuneo.com' style='color: #0d3b4d; text-decoration: none;'>help@stuneo.com</a>.</p>
                            <p>{$translations['best_regards']}<br>{$translations['stuneo_team']}</p>
                        </div>
                        <div style=\"background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #999;\">
                            <p style=\"margin: 0;\">&copy; 2025 stuneo. {$translations['all_rights_reserved']}</p>
                            <p><a href=\"https://stuneo.com\" style=\"color: #0d3b4d; text-decoration: none;\">{$translations['visit_website']}</a></p>
                        </div>
                    </div>
                </body>
            </html>";

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.hostinger.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'noreply@stuneo.com';
                $mail->Password = 'stuneo_2024%Dominik';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->setFrom('noreply@stuneo.com', 'stuneo');
                $mail->addAddress($email, "$firstname $lastname");

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $htmlContent;

                if ($mail->send()) {
                    $success = $translations['success_registration'];
                } else {
                    $error = $translations['error_email_not_sent'];
                }
            } catch (Exception $e) {
                $error = str_replace('{reason}', $mail->ErrorInfo, $translations['error_email_not_sent_with_reason']);
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($translations['page_title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

    <link rel="stylesheet" href="src/css/register.css?v=<?= time(); ?>">
    <link rel="icon" href="icon.png" type="image/png">

    <!-- Toasts CSS -->
    <link rel="stylesheet" href="src/custom_toast.css?v=<?= time(); ?>">
    <style>
            input[type="date"]::-webkit-calendar-picker-indicator {
            display: none;
        }
    </style>
</head>
<body>

<div class="registration-container">
    <img src="src/img/stuneo_logo_light.svg" class="mb-3" width="160px" alt="stuneo" />
    <p class="mb-4"><?= htmlspecialchars($translations['register_heading']) ?></p>

    <form id="registrationForm" method="POST">
        <!-- Schritt 1: Persönliche Informationen -->
        <div id="step1" class="step">
            <h5 class="subtitle has-text-white"><b><?= htmlspecialchars($translations['step1_title']) ?></b></h5>
            <div class="field">
                <div class="control has-icons-left">
                    <input class="input" type="text" name="firstname" placeholder="<?= htmlspecialchars($translations['firstname_placeholder']) ?>" required>
                    <span class="icon is-small is-left"><i class="ri-user-line"></i></span>
                </div>
                <div class="control has-icons-left">
                    <input class="input" type="text" name="lastname" placeholder="<?= htmlspecialchars($translations['lastname_placeholder']) ?>" required>
                    <span class="icon is-small is-left"><i class="ri-user-line"></i></span>
                </div>
                <div class="control">
                    <label class="label has-text-white"><?= htmlspecialchars($translations['birthday_label']) ?></label>
                    <input class="input" type="date" name="birthdate" required>
                </div>

            </div>
            <button type="button" class="button is-link" onclick="nextStep(2)"><?= htmlspecialchars($translations['continue_button']) ?></button>
        </div>

        <!-- Schritt 2: Konto erstellen -->
        <div id="step2" class="step hidden">
            <h5 class="subtitle has-text-white"><b><?= htmlspecialchars($translations['step2_title']) ?></b></h5>
            <div class="field">
                <div class="control has-icons-left">
                    <input class="input" type="text" name="username" placeholder="<?= htmlspecialchars($translations['username_placeholder']) ?>" required>
                    <span class="icon is-small is-left"><i class="ri-user-line"></i></span>
                </div>
                <div class="control has-icons-left">
                    <input class="input" type="email" name="email" placeholder="<?= htmlspecialchars($translations['email_placeholder']) ?>" required>
                    <span class="icon is-small is-left"><i class="ri-mail-line"></i></span>
                </div>
                <div class="control has-icons-left">
                    <input class="input" type="password" name="password" placeholder="<?= htmlspecialchars($translations['password_placeholder']) ?>" required>
                    <span class="icon is-small is-left"><i class="ri-lock-line"></i></span>
                </div>
            </div>
            <button type="button" class="button is-link mb-3" onclick="prevStep(1)"><?= htmlspecialchars($translations['back_button']) ?></button>
            <button type="button" class="button is-link" onclick="nextStep(3)"><?= htmlspecialchars($translations['continue_button']) ?></button>
        </div>

        <!-- Schritt 3: Sprache & Land -->
        <div id="step3" class="step hidden">
            <h5 class="subtitle has-text-white"><b><?= htmlspecialchars($translations['step3_title']) ?></b></h5>
            <div class="field">
            <div class="control has-icons-left">
                    <label class="label has-text-white"><?= htmlspecialchars($translations['system_language_label']) ?></label>
                    <div class="select-container">
                        <select name="system_language" required>
                            <option value="en_US" selected>English</option>
                            <option value="de_DE">Deutsch</option>
                        </select>
                        <span class="icon select-icon"><i class="ri-arrow-down-s-line"></i></span>
                    </div>
                </div>
            <div class="control has-icons-left">
                    <label class="label has-text-white"><?= htmlspecialchars($translations['language_label']) ?></label>
                    <div class="select-container">
                        <select name="language" required>
                            <option value="" disabled selected><?= htmlspecialchars($translations['select_language']) ?></option>
                            <?php foreach ($languages as $language): ?>
                                <option value="<?= $language['language_code']; ?>"><?= $language['language_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="icon select-icon"><i class="ri-arrow-down-s-line"></i></span>
                    </div>
            </div>
            <div class="control has-icons-left">
                    <label class="label has-text-white"><?= htmlspecialchars($translations['country_label']) ?></label>
                    <div class="select-container">
                        <select name="country" required>
                            <option value="" disabled selected><?= htmlspecialchars($translations['select_country']) ?></option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= $country['country_code']; ?>"><?= $country['country_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="icon select-icon"><i class="ri-arrow-down-s-line"></i></span>
                    </div>
            </div>
        </div>
            <button type="button" class="button is-link mb-3" onclick="prevStep(2)"><?= htmlspecialchars($translations['back_button']) ?></button>
            <button type="submit" class="button is-success"><?= htmlspecialchars($translations['register_button']) ?></button>
        </div>
    </form>
    <div class="links mt-5">
        <a href="login"><?= htmlspecialchars($translations['go_to_login']) ?></a>
    </div>
</div>

<div id="toast-container"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    <?php if (!empty($error)): ?>
        showToast('<?= $error ?>', 'error');
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        showToast('<?= $success ?>', 'success');
        setTimeout(() => { window.location.href = 'login'; }, 3000);
    <?php endif; ?>
});
</script>
<script src="src/js/custom_toast_register.js?v=<?= time(); ?>"></script>    
<script src="src/js/register.js?v=<?= time(); ?>"></script>   
<?php
include "../security/config.php";
include "../security/project-security.php";
?>
</body>
</html>
