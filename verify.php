<?php
session_start();
include 'includes/db.php';

// Browser-Sprache erkennen
$browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$supportedLanguages = ['de', 'en'];
$language = in_array($browserLanguage, $supportedLanguages) ? $browserLanguage : 'en';

// Sprachdatei laden
$translations = [];
if ($language === 'de') {
    $translations = include 'languages/de_DE/verify.php';
} else {
    $translations = include 'languages/en_US/verify.php';
}

$errors = [];

if (!isset($_SESSION['2fa_user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['2fa_user_id'];

// Verarbeiten des OTP-Formulars
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp']);

    if (empty($otp)) {
        $errors[] = $translations['error_enter_otp'];
    } else {
        // OTP und Ablaufzeit überprüfen
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id AND 2fa_code = :otp AND 2fa_expires_at > NOW()");
        $stmt->execute([
            'user_id' => $user_id,
            'otp' => $otp,
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // OTP erfolgreich verifiziert
            $_SESSION['user_data'] = [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'language' => $user['language'],
                'system_language' => $user['system_language'],
                'country' => $user['country']
            ];

            // OTP aus der Datenbank entfernen
            $stmt = $conn->prepare("UPDATE users SET 2fa_code = NULL, 2fa_expires_at = NULL WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);

            unset($_SESSION['2fa_user_id']);

            // Erfolgsmeldung setzen
            $_SESSION['success'] = $translations['success_2fa_login'];

            header("Location: index.php");
            exit;
        } else {
            $errors[] = $translations['error_invalid_otp'];
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
    <link rel="icon" href="icon.png" type="image/png">

    <link rel="stylesheet" href="src/css/login.css?v=<?= time(); ?>">
    <!-- Toasts CSS -->
    <link rel="stylesheet" href="src/custom_toast.css?v=<?= time(); ?>">
</head>
<body>

    <div class="login-container">
        <img src="src/img/loudma_logo_light.svg" class="mb-3" width="160px" alt="loudma" />
        <p style="font-size: 16px; font-family: 'Inter'; font-weight: 700;" class="mb-4"><?= htmlspecialchars($translations['title']) ?></p>

        <!-- Login-Formular -->
        <form action="" method="POST">
            <div class="field">
                <div class="control has-icons-left">
                    <input class="input" type="text" name="otp" id="otp" placeholder="<?= htmlspecialchars($translations['otp_placeholder']) ?>" required>
                    <span class="icon is-small is-left">
                        <i class="ri-shield-line"></i>
                    </span>
                </div>
            </div>
            <div class="field">
                <button class="button" type="submit"><?= htmlspecialchars($translations['verify_button']) ?></button>
            </div>
        </form>
    </div>
    <div id="toast-container"></div>
    <!-- Dynamischer Gradient und Buttered Toast -->
    <script src="src/js/custom_toast.js?v=<?= time(); ?>"></script>    
    <script src="src/js/login.js?v=<?= time(); ?>"></script>    

<!-- Rückmeldungen -->
<?php if (!empty($errors)): ?>
    <script>
        <?php foreach ($errors as $error): ?>
            showToast('Error', '<?= addslashes($error) ?>', 'error');
        <?php endforeach; ?>
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
    <script>
        showToast('Success', '<?= addslashes($_SESSION['success']) ?>', 'success');
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php
include "../security/config.php";
include "../security/project-security.php";
?>
</body>
</html>
