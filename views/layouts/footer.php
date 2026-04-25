<?php if (authUser()): ?>
        </main>
    </div> <!-- .main-wrapper -->
</div> <!-- .app-container -->

<script>
document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('open');
});

// Anchor-based active state fallback
window.addEventListener('hashchange', function() {
    const hash = window.location.hash;
    if (!hash) return;
    
    document.querySelectorAll('.nav-item').forEach(item => {
        if (item.getAttribute('href').endsWith(hash)) {
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        }
    });
});

// Initial check for hash
if (window.location.hash) {
    window.dispatchEvent(new Event('hashchange'));
}
</script>
<?php else: ?>
    </main>
    <footer class="footer" style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.875rem;">
        <div class="container">
            <p>&copy; <?= date('Y'); ?> Helperly Marketplace. Built with premium care.</p>
        </div>
    </footer>
<?php endif; ?>

<script src="/assets/js/app.js"></script>
</body>
</html>
