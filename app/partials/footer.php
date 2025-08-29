<footer class="footer">
    <div class="footer-section">
        <div class="footer-title">Type d'utilisateur:</div>
        <div class="footer-content">
            <?php echo ucfirst(htmlspecialchars(\App\Services\SessionManager::getUserType())); ?>
        </div>
    </div>
    <div class="footer-section">
        <div class="footer-title">DATE</div>
        <div class="footer-content"><?php echo date('d/m/Y'); ?></div>
        <div class="extra-info">⏱ <span id="current-time"><?php echo date('H:i:s'); ?></span></div>
    </div>
    <div class="footer-section footer-right">
        <div class="footer-title">Design by Nicolas Désirliste</div>
        <div class="progress-dots" id="progress-dots">
            <?php 
            // AFPT: Les lumières chronomètre de la session. 
            for ($i = 0; $i < 11; $i++): ?>
                <div class="dot"></div>
            <?php endfor; ?>
        </div>
    </div>
</footer>
<script src="/LVDPA/public/js/homepagejs/footer.js"></script>
<script src="/LVDPA/public/js/homepagejs/session-indicator.js"></script>