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
    $translations = include 'languages/de_DE/reset-password.php';
} else {
    $translations = include 'languages/en_US/reset-password.php';
}

$error = '';
$success = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Überprüfen, ob das Token gültig und nicht abgelaufen ist
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry > NOW() LIMIT 1");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);

            if (empty($new_password) || empty($confirm_password)) {
                $error = $translations['error_fill_fields'];
            } elseif ($new_password !== $confirm_password) {
                $error = $translations['error_password_mismatch'];
            } else {
                // Neues Passwort speichern und Token löschen
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE user_id = :user_id");
                $stmt->execute([
                    'password' => $hashed_password,
                    'user_id' => $user['user_id']
                ]);

                $success = $translations['success_password_reset'];
                echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 3000);</script>";
            }
        }
    } else {
        $error = $translations['error_invalid_token'];
    }
} else {
    $error = $translations['error_no_token'];
}
?>

<!DOCTYPE html>
<html lang="<?= $language ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($translations['reset_password_title']) ?></title>
    <link rel="icon" href="icon.png" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="src/css/login.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="src/custom_toast.css?v=<?= time(); ?>">
</head>
<body>
    <div class="login-container">
        <img src="src/img/stuneo_logo_light.svg" class="mb-3" width="160px" alt="stuneo" />
        <p class="mb-4"><?= htmlspecialchars($translations['reset_password_prompt']) ?></p>
        <form action="" method="POST">
            <div class="field">
                <div class="control has-icons-left">
                    <input class="input" type="password" name="new_password" placeholder="<?= htmlspecialchars($translations['placeholder_new_password']) ?>" required>
                    <span class="icon is-small is-left">
                        <i class="ri-lock-line"></i>
                    </span>
                </div>
                <div class="control has-icons-left">
                    <input class="input" type="password" name="confirm_password" placeholder="<?= htmlspecialchars($translations['placeholder_confirm_password']) ?>" required>
                    <span class="icon is-small is-left">
                        <i class="ri-lock-line"></i>
                    </span>
                </div>
            </div>
            <div class="field">
                <button class="button is-primary" type="submit"><?= htmlspecialchars($translations['button_reset_password']) ?></button>
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