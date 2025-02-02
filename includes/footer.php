
<!-- Player nur für angemeldete Benutzer -->
<?php if ($is_logged_in): ?>
    <!-- Player bleibt konstant -->
    <?php include 'includes/player.php'; ?>
<?php else: ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const playerElement = document.querySelector('.player');
            if (playerElement) {
                playerElement.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.querySelector('#loginModal').classList.add('is-active');
                });
            }

            // Animation für den Download-Button
            const downloadButton = document.getElementById('downloadButton');
            if (downloadButton) {
                downloadButton.addEventListener('click', function () {
                    const icon = document.getElementById('downloadIcon');
                    if (icon) {
                        icon.style.transform = 'rotate(360deg)';
                        setTimeout(() => {
                            icon.style.transform = 'rotate(0deg)';
                        }, 300);
                    }
                });
            }
        });

    </script>
<?php endif; ?>
<?php include_once('includes/bottom-bar.php'); ?>
<?php include_once('cookies/cookie.php'); ?>

<?php if ($showModal): ?>
    <div id="<?php echo $modalId; ?>" class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-content">
            <div class="box modal-box-custom">
                <h3 class="title is-4"><?php echo __('Willkommen bei stuneo!'); ?></h3>

                <!-- Lottie Animation -->
                <div id="lottie-animation-<?php echo $user_id; ?>" style="width: 200px; height: 200px; margin: 0 auto;"></div>

                <p><?php echo __('Vielen Dank, dass du stuneo benutzt. Wir freuen uns, dich dabei zu haben!'); ?></p>
                <div class="buttons mt-4">
                    <button id="modal-close-btn-<?php echo $user_id; ?>" class="button is-primary"><?php echo __('Alles klar'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('<?php echo $modalId; ?>');

            if (modal) {
                console.log('Modal wird geöffnet.');
                modal.classList.add('is-active');

                // Lottie-Animation einfügen
                lottie.loadAnimation({
                    container: document.getElementById('lottie-animation-<?php echo $user_id; ?>'),
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: 'https://open.stuneo.com/thank-u.json' // Ersetze mit dem korrekten Pfad
                });

                // Funktion zum Schließen des Modals mit AJAX-Anfrage zum Speichern in der Datenbank
                function closeModal() {
                    modal.classList.remove('is-active');
                    
                    // AJAX-Anfrage an den Server senden, um first_login auf 0 zu setzen
                    fetch('update_first_login.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ user_id: <?php echo $user_id; ?> })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('first_login wurde erfolgreich aktualisiert.');
                        } else {
                            console.error('Fehler beim Aktualisieren von first_login:', data.error);
                        }
                    })
                    .catch(error => console.error('Fehler:', error));
                }

                // Event-Listener für das Schließen des Modals
                document.querySelector('.modal-close-custom').addEventListener('click', closeModal);
                document.querySelector('#modal-close-btn-<?php echo $user_id; ?>').addEventListener('click', closeModal);
            }
        });
    </script>
<?php endif; ?>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="src/js/footer.js?v=<?= time(); ?>"></script>
<?php
include "../security/config.php";
include "../security/project-security.php";
?>
</body>

</html>

