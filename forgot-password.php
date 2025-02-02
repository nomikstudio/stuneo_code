<?php
session_start();
include 'includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Browser-Sprache erkennen
$browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$supportedLanguages = ['de', 'en'];
$language = in_array($browserLanguage, $supportedLanguages) ? $browserLanguage : 'en';

// Sprachdatei laden
$translations = [];
if ($language === 'de') {
    $translations = include 'languages/de_DE/forgot-password.php';
} else {
    $translations = include 'languages/en_US/forgot-password.php';
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = $translations['error_enter_email'];
    } else {
        // Prüfen, ob die E-Mail-Adresse existiert
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Token für Passwort-Wiederherstellung erstellen
            $token = bin2hex(random_bytes(16));
            $stmt = $conn->prepare("UPDATE users SET reset_token = :token, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = :email");
            $stmt->execute(['token' => $token, 'email' => $email]);

            // Passwort-Wiederherstellungslink erstellen
            $resetLink = "https://open.stuneo.com/reset-password?token=$token";

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
                $mail->addAddress($email, $user['username']);
                
                // Zeichensatz auf UTF-8 setzen
                $mail->CharSet = 'UTF-8';
            
                // E-Mail-Überschrift und Inhalt aus Sprachdatei
                $mail->isHTML(true);
                $mail->Subject = htmlspecialchars($translations['email_subject'], ENT_QUOTES, 'UTF-8');
                $mail->Body = "
                <html>
                    <body style=\"font-family: Arial, sans-serif; color: #333; background-color: #f9f9f9; padding: 0; margin: 0;\">
                        <div style=\"max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden;\">
                            <img src=\"https://stuneo.com/stuneo_logo_color.png\" alt=\"stuneo\" style=\"height: auto; width: 150px; object-fit: contain; margin-top: 15px; margin-bottom: 15px; padding-left: 15px;\">
                            </div>
                            <div style=\"padding: 20px;\">
                                <h2 style=\"color: #333; font-size: 24px; margin-bottom: 10px;\">" . htmlspecialchars($translations['email_title'], ENT_QUOTES, 'UTF-8') . "</h2>
                                <p>" . htmlspecialchars($translations['email_body'], ENT_QUOTES, 'UTF-8') . "</p>
                                <p style=\"text-align: center; margin: 20px 0;\">
                                    <a href=\"$resetLink\" style=\"display: inline-block; padding: 10px 20px; color: #ffffff; background-color: #000; border-radius: 4px; text-decoration: none; font-size: 16px;\">" . htmlspecialchars($translations['reset_button'], ENT_QUOTES, 'UTF-8') . "</a>
                                </p>
                                <p>" . htmlspecialchars($translations['email_disclaimer'], ENT_QUOTES, 'UTF-8') . "</p>
                                <p>" . htmlspecialchars($translations['best_regards'], ENT_QUOTES, 'UTF-8') . "<br>stuneo</p>
                            </div>
                            <div style=\"background-color: #f1f1f1; padding: 15px; text-align: left; font-size: 12px; color: #999;\">
                                <p style=\"margin: 0;\">&copy; 2024 stuneo. " . htmlspecialchars($translations['all_rights_reserved'], ENT_QUOTES, 'UTF-8') . "</p>
                                <p><a href=\"https://stuneo.com\">" . htmlspecialchars($translations['visit_website'], ENT_QUOTES, 'UTF-8') . "</a></p>
                            </div>
                        </div>
                    </body>
                </html>";
            

                if ($mail->send()) {
                    $success = $translations['success_email_sent'];
                } else {
                    $error = $translations['error_email_not_sent'];
                }
            } catch (Exception $e) {
                $error = str_replace('{reason}', $mail->ErrorInfo, $translations['error_email_not_sent_with_reason']);
            }
        } else {
            $error = $translations['error_email_not_found'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($translations['page_title']) ?> - stuneo</title>
    <link rel="icon" href="icon.png" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="src/css/login.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="src/custom_toast.css?v=<?= time(); ?>">
</head>
<body>
    <div class="login-container">
        <img src="src/img/stuneo_logo_light.svg" class="mb-3" width="160px" alt="stuneo" />
        <p class="mb-4"><?= htmlspecialchars($translations['instruction_text']) ?></p>
        
        <form action="" method="POST">
            <div class="field">
                <div class="control has-icons-left">
                    <input class="input" type="email" name="email" placeholder="<?= htmlspecialchars($translations['email_placeholder']) ?>" required>
                    <span class="icon is-small is-left">
                        <i class="ri-mail-line"></i>
                    </span>
                </div>
            </div>
            <div class="field">
                <button class="button is-primary" type="submit"><?= htmlspecialchars($translations['submit_button']) ?></button>
            </div>
        </form>
    </div>

    <div id="toast-container"></div>
    <script src="src/js/login.js?v=<?= time(); ?>"></script>

    <script src="src/js/custom_toast.js?v=<?= time(); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!empty($error)): ?>
                showToast('Error', '<?= addslashes($error) ?>', 'error');
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                showToast('Success', '<?= addslashes($success) ?>', 'success');
            <?php endif; ?>
        });
    </script>
    <?php
include "../security/config.php";
include "../security/project-security.php";
?>
</body>
</html>