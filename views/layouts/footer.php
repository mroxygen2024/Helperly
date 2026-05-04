<?php if (authUser()): ?>
        </main>
    </div> <!-- .main-wrapper -->
</div> <!-- .app-container -->

<script>
// Sidebar Toggle
document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('open');
});

// Modal Logic
document.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-open-modal]');
    if (openBtn) {
        const modalId = openBtn.getAttribute('data-open-modal');
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    const closeBtn = e.target.closest('[data-close-modal]');
    if (closeBtn || e.target.matches('.modal-overlay')) {
        const modal = e.target.closest('.modal-overlay') || document.getElementById(closeBtn.getAttribute('data-close-modal'));
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
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

if (window.location.hash) {
    window.dispatchEvent(new Event('hashchange'));
}
</script>
<?php else: ?>
    </main>
    <footer class="py-8 text-center text-muted text-sm border-t mt-12">
        <div class="max-w-7xl mx-auto px-6">
            <p>&copy; <?= date('Y'); ?> Helperly Marketplace. Built with premium care.</p>
        </div>
    </footer>
<?php endif; ?>



<script src="/assets/js/app.js"></script>
</body>
</html>
