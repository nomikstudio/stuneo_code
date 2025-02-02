<?php
session_start();

// Überprüfen, ob der Benutzer bereits angemeldet ist
if (isset($_SESSION['user_data']) && !empty($_SESSION['user_data']['user_id'])) {
    $user_id = $_SESSION['user_data']['user_id'];

    
    // Wenn der Benutzer angemeldet ist, weiterleiten
    header("Location: index");
    exit;
}



include 'includes/db.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$is_electron = isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'Electron') !== false;

// Browser-Sprache erkennen
$browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$supportedLanguages = ['de', 'en'];
$language = in_array($browserLanguage, $supportedLanguages) ? $browserLanguage : 'en';

// Sprachdatei laden
$translations = [];
if ($language === 'de') {
    $translations = include 'languages/de_DE/login.php';
} else {
    $translations = include 'languages/en_US/login.php';
}

$errors = [];

// Prüfen, ob Remember Me-Cookie vorhanden ist
if (isset($_COOKIE['remember_me'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = :token LIMIT 1");
    $stmt->execute(['token' => $_COOKIE['remember_me']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Automatisch einloggen, wenn der Remember Me-Cookie gültig ist
        $_SESSION['user_data'] = [
            'user_id' => $user['user_id'],
            'username' => $user['username'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'email' => $user['email'],
            'system_language' => $user['system_language'],
            'country' => $user['country'],
            'plan_id' => $user['plan_id']

        ];
        header("Location: index.php");
        exit;
    }
}

// Verarbeiten des Login-Formulars
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    if (empty($username_or_email) || empty($password)) {
        $errors[] = 'User name/e-mail and password are required.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1");
        $stmt->execute([
            'username' => $username_or_email,
            'email' => $username_or_email,
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_2fa_enabled']) {
                // OTP generieren
                $otp = random_int(100000, 999999);
                $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                // OTP in der Datenbank speichern
                $stmt = $conn->prepare("UPDATE users SET 2fa_code = :otp, 2fa_expires_at = :expires_at WHERE user_id = :user_id");
                $stmt->execute([
                    'otp' => $otp,
                    'expires_at' => $expires_at,
                    'user_id' => $user['user_id']
                ]);

                // Sprachabhängige Übersetzungen
                    $translations = [];
                    if ($user['system_language'] === 'de_DE') {
                        $translations = [
                            'Dear' => 'Hallo',
                            'Your authentication code is:' => 'Dein Authentifizierungscode lautet:',
                            'This code will expire in 15 minutes.' => 'Dieser Code läuft in 10 Minuten ab.',
                            'If you did not request this, please ignore this email.' => 'Falls du das nicht angefordert hast, ignoriere diese E-Mail bitte.',
                            'Best regards,' => 'Viele Grüße,',
                            'All rights reserved.' => 'Alle Rechte vorbehalten.',
                            'Visit our website' => 'Website',
                        ];
                    } else {
                        $translations = [
                            'Dear' => 'Dear',
                            'Your authentication code is:' => 'Your authentication code is:',
                            'This code will expire in 15 minutes.' => 'This code will expire in 10 minutes.',
                            'If you did not request this, please ignore this email.' => 'If you did not request this, please ignore this email.',
                            'Best regards,' => 'Best regards,',
                            'All rights reserved.' => 'All rights reserved.',
                            'Visit our website' => 'Website',
                        ];
                    }

                    // E-Mail-HTML-Template
                    $emailBody = "
                    <html>
                        <body style='font-family: Arial, sans-serif; color: #333; background-color: #f9f9f9; padding: 0; margin: 0;'>
                            <div style='max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden;'>
                                <!-- Header -->
                                <img src=\"https://stuneo.com/stuneo_logo_color.png\" alt=\"stuneo\" style=\"height: auto; width: 150px; object-fit: contain; margin-top: 15px; margin-bottom: 15px; padding-left: 15px;\">


                                <!-- Content -->
                                <div style='padding: 20px;'>
                                    <p style='font-size: 16px; line-height: 1.6; color: #555;'>{$translations['Dear']} <strong>{$user['username']}</strong>,</p>
                                    <p style='font-size: 16px; line-height: 1.6; color: #555;'>
                                        {$translations['Your authentication code is:']} <strong>$otp</strong>
                                    </p>
                                    <p style='font-size: 14px; color: #777; margin-top: 20px;'>
                                        {$translations['This code will expire in 15 minutes.']}
                                    </p>
                                    <p style='font-size: 14px; color: #777; margin-top: 20px;'>
                                        {$translations['If you did not request this, please ignore this email.']}
                                    </p>
                                    <p style='font-size: 14px; color: #777; margin-top: 20px;'>
                                        {$translations['Best regards,']}<br>
                                        <strong>stuneo</strong>
                                    </p>
                                </div>

                                <!-- Footer -->
                                <div style='background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #999;'>
                                    <p style='margin: 0;'>&copy; 2024 stuneo. {$translations['All rights reserved.']}</p>
                                    <p style='margin: 0;'>
                                        <a href='https://stuneo.com' style='color: #0d3b4d; text-decoration: none;'>{$translations['Visit our website']}</a>
                                    </p>
                                </div>
                            </div>
                        </body>
                    </html>";

                    // OTP per E-Mail senden
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
                        $mail->addAddress($user['email'], $user['username']);
                        $mail->isHTML(true);

                        // Betreff mit OTP
                        if ($user['system_language'] === 'de_DE') {
                            $mail->Subject = "Dein Authentifizierungscode: $otp";
                        } else {
                            $mail->Subject = "Your Authentication Code: $otp";
                        }

                        // HTML-Body
                        $mail->Body = $emailBody;

                        $mail->send();
                    } catch (Exception $e) {
                        $errors[] = 'Failed to send OTP email. Please try again later.';
                    }

                // Benutzer-ID in die Session speichern
                $_SESSION['2fa_user_id'] = $user['user_id'];

                // Weiterleitung zur 2FA-Überprüfung
                header("Location: verify.php");
                exit;
            } else {
            // Remember Me-Option verarbeiten
            if ($remember_me) {
                try {
                    $rememberToken = bin2hex(random_bytes(16));
                    $stmt = $conn->prepare("UPDATE users SET remember_token = :token WHERE user_id = :user_id");
                    $stmt->execute([
                        'token' => $rememberToken,
                        'user_id' => $user['user_id']
                    ]);
                    setcookie('remember_me', $rememberToken, time() + (86400 * 30), "/", "", true, true); // Sicheres Cookie setzen für 30 Tage
                } catch (Exception $e) {
                    error_log("Fehler beim Setzen des Remember Me-Tokens: " . $e->getMessage());
                }
            } else {
                setcookie('remember_me', '', time() - 3600, "/");
            }

            // Benutzerdaten in die Session speichern
            $_SESSION['user_data'] = [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'email' => $user['email'],
                'language' => $user['language'],
                'system_language' => $user['system_language'],
                'country' => $user['country'],
                'plan_id' => $user['plan_id']
            ];

            // Weiterleitung zur Startseite
            header("Location: index.php");
            exit;

            }
        } else {
            $errors[] = $translations['error_invalid_login'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($translations['login_title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link rel="icon" href="icon.png" type="image/png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tunespace_cdn@latest/src/css/login.css">
    <!-- Toasts CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tunespace_cdn@latest/src/custom_toast.css">
</head>
<body>

<div class="login-container">
    <img src="src/img/stuneo_logo_light.svg" class="mb-3" width="160px" alt="stuneo" />
    <p style="font-size: 16px; font-family: 'Inter'; font-weight: 700;" class="mb-4"><?= htmlspecialchars($translations['login_heading']) ?></p>
        
        <!-- Login-Formular -->
        <form  method="POST">
            <div class="field">
                <div class="control has-icons-left">
                    <input class="input" type="text" name="username_or_email" placeholder="<?= htmlspecialchars($translations['username_or_email_placeholder']) ?>" required>
                    <span class="icon is-small is-left">
                        <i class="ri-user-line"></i>
                    </span>
                </div>
                <div class="control has-icons-left">
                    <input class="input" type="password" name="password" placeholder="<?= htmlspecialchars($translations['password_placeholder']) ?>" required>
                    <span class="icon is-small is-left">
                        <i class="ri-lock-line"></i>
                    </span>
                </div>
            </div>
            <div class="field">
                <label class="checkbox">
                    <input type="checkbox" name="remember_me">
                    <?= htmlspecialchars($translations['remember_me']) ?>
                </label>
            </div>
            <div class="field">
                <button class="button" type="submit"><?= htmlspecialchars($translations['login_button']) ?></button>
            </div>
        </form>
        <div class="links mt-5">
            <?php if (!$is_electron): // Links nur anzeigen, wenn keine Electron-App genutzt wird ?>
                <a href="register"><?= htmlspecialchars($translations['create_account']) ?></a> | 
                <a href="forgot-password"><?= htmlspecialchars($translations['forgot_password']) ?></a>
            <?php endif; ?>
        </div>
    </div>
    <div id="toast-container"></div>
    <!-- Dynamischer Gradient und Buttered Toast -->
    <script src="https://cdn.jsdelivr.net/npm/tunespace_cdn@latest/src/js/custom_toast.js"></script>    
    <script src="src/js/login.js"></script>    

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $error): ?>
                    showToast('Error', '<?= addslashes($error) ?>', 'error');
                <?php endforeach; ?>
            <?php endif; ?>
        });
    </script>

</body>
</html>
