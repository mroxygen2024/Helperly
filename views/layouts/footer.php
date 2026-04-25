        </main>
    </div> <!-- .main-wrapper -->
</div> <!-- .app-container -->
<?php if (!authUser()): ?>
<footer class="footer" style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.875rem;">
    <div class="container">
        <p>&copy; <?= date('Y'); ?> Helperly Marketplace. Built with premium care.</p>
    </div>
</footer>
<?php endif; ?>
<script src="/assets/js/app.js"></script>
</body>
</html>
