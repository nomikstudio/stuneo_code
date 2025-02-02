<div id="cookie-banner" class="cookie-banner">
    <div class="cookie-text">
        <h4 class="cookie-title"><img src="https://stuneo.com/wp-content/uploads/2024/12/stuneo_icon_light.png" alt="" class="mr-2" width="15px"> <?= htmlspecialchars($translations['cookie_policy']); ?></h4>
        <p>
        <?= htmlspecialchars($translations['cookie_description']); ?>
            <a href="privacy" class="cookie-link"><?= htmlspecialchars($translations['policy']); ?></a>.
        </p>
    </div>
    <div class="cookie-buttons">
        <button id="accept-all" class="button"><?= htmlspecialchars($translations['accept']); ?></button>
        <button id="reject-all" class="button"><?= htmlspecialchars($translations['decline']); ?></button>
    </div>
</div>

<script src="cookies/cookie.js"></script>
