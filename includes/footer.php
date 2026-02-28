</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col brand-col">
                <h3 class="footer-logo">NextGen<span class="text-accent">Homes</span></h3>
                <p>Your path to affordable, high-quality homeownership. We provide full-service delivery, setup, and
                    flexible financing.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook">F</a>
                    <a href="#" aria-label="Instagram">I</a>
                    <a href="#" aria-label="Twitter">T</a>
                </div>
            </div>

            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="inventory.php">Current Inventory</a></li>
                    <li><a href="financing.php">Financing Programs</a></li>
                    <li><a href="sell.php">Sell Your Mobile Home</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Contact Us</h3>
                <ul>
                    <li>📞 <a href="tel:5551234567">(555) 123-4567</a></li>
                    <li>✉️ <a href="mailto:info@nextgenhomes.demo">info@nextgenhomes.demo</a></li>
                    <li>📍 123 Home Blvd, Anytown, USA</li>
                    <li>🕒 Mon - Sat: 9am - 6pm</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy;
                <?php echo date('Y'); ?> NextGen Homes. All rights reserved.
            </p>
            <p class="disclaimer">Prices, floor plans, and features are subject to change without notice. Financing
                available to qualified buyers.</p>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('mobile-menu-btn');
        const navLinks = document.getElementById('nav-links');

        if (toggleBtn && navLinks) {
            toggleBtn.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }
    });
</script>
</body>

</html>