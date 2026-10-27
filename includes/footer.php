    </main>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <a class="brand footer-brand" href="<?= url('index.php') ?>">
                    <span class="brand-mark">A</span>
                    <span class="brand-text">Arts.</span>
                </a>
                <p>Premium online marketplace for modern, traditional, and hand-drawn artworks.</p>
            </div>
            <div>
                <h4>Products</h4>
                <a href="<?= url('gallery.php') ?>">Artworks</a>
                <a href="<?= url('artists.php') ?>">Artists</a>
                <a href="<?= url('exhibitions.php') ?>">Exhibitions</a>
                <a href="<?= url('custom-order.php') ?>">Custom Orders</a>
            </div>
            <div>
                <h4>Company</h4>
                <a href="<?= url('articles.php') ?>">Articles</a>
                <a href="<?= url('contact.php') ?>">Contact</a>
                <a href="<?= url('login.php') ?>">Login</a>
                <a href="<?= url('register.php') ?>">Register</a>
            </div>
            <div>
                <h4>Support</h4>
                <a href="<?= url('messages.php') ?>">Messages</a>
                <a href="<?= url('dashboard.php') ?>">Order Tracking</a>
                <a href="<?= url('cart.php') ?>">Cart</a>
                <a href="<?= url('admin/dashboard.php') ?>">Admin Panel</a>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>&copy; <?= date('Y') ?> Arts Gallery. All rights reserved.</span>
            <span>Designed as a complete HNDIT project.</span>
        </div>
    </footer>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
