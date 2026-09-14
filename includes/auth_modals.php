<?php
$reopenModal = flash_get('reopen_modal');
$loginError = flash_get('login_error');
$loginSuccess = flash_get('login_success');
$loginOldIdentifier = flash_get('login_old_identifier');
$loginReturnTo = flash_get('login_return_to');
$registerErrors = flash_get('register_errors');
$registerOld = flash_get('register_old');
$registerReturnTo = flash_get('register_return_to');
$here = current_path();
?>
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered auth-modal-dialog">
        <div class="modal-content auth-modal-content">
            <button type="button" class="btn-close auth-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="auth-visual">
                <div class="brand-mark">MarkMe</div>
                <blockquote>"Every page you've loved deserves a bookmark that's just as personal."</blockquote>
                <div class="auth-shapes">
                    <div class="bookmark-shape" style="width:50px;height:120px;background:var(--gold);"></div>
                    <div class="bookmark-shape" style="width:50px;height:140px;background:var(--accent);"></div>
                    <div class="bookmark-shape" style="width:50px;height:105px;background:var(--cream);"></div>
                </div>
            </div>
            <div class="auth-form-panel">
                <h1 id="loginModalLabel">Welcome back</h1>
                <p class="subtitle">Log in to keep designing and tracking your bookmarks.</p>

                <?php if ($loginSuccess): ?><div class="alert alert-success"><?= e($loginSuccess) ?></div><?php endif; ?>
                <?php if ($loginError): ?><div class="alert alert-danger"><?= e($loginError) ?></div><?php endif; ?>

                <form method="post" action="<?= BASE_URL ?>/customer/login.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_to" value="<?= e($loginReturnTo ?? $here) ?>">
                    <div class="mb-3">
                        <label class="form-label">Username or Email</label>
                        <input type="text" name="identifier" class="form-control" value="<?= e($loginOldIdentifier ?? '') ?>" required <?= $reopenModal === 'login' ? 'autofocus' : '' ?>>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg">Log In</button>
                </form>
                <p class="auth-switch">No account yet?
                    <button type="button" class="btn btn-link p-0 align-baseline" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered auth-modal-dialog auth-modal-dialog-wide">
        <div class="modal-content auth-modal-content">
            <button type="button" class="btn-close auth-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="auth-visual">
                <div class="brand-mark">MarkMe</div>
                <blockquote>"Join MarkMe and start designing a bookmark that tells your story."</blockquote>
                <div class="auth-shapes">
                    <div class="bookmark-shape" style="width:50px;height:120px;background:var(--gold);"></div>
                    <div class="bookmark-shape" style="width:50px;height:140px;background:var(--accent);"></div>
                    <div class="bookmark-shape" style="width:50px;height:105px;background:var(--cream);"></div>
                </div>
            </div>
            <div class="auth-form-panel">
                <h1 id="registerModalLabel">Create an account</h1>
                <p class="subtitle">It only takes a minute to start personalizing.</p>

                <?php if ($registerErrors): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0"><?php foreach ($registerErrors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= BASE_URL ?>/customer/register.php" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_to" value="<?= e($registerReturnTo ?? $here) ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= e($registerOld['full_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= e($registerOld['username'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= e($registerOld['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" value="<?= e($registerOld['contact_number'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Delivery Address</label>
                            <textarea name="address" class="form-control" rows="2"><?= e($registerOld['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg mt-4">Register</button>
                </form>
                <p class="auth-switch">Already have an account?
                    <button type="button" class="btn btn-link p-0 align-baseline" data-bs-toggle="modal" data-bs-target="#loginModal">Log in</button>
                </p>
            </div>
        </div>
    </div>
</div>

<?php if ($reopenModal === 'login' || $reopenModal === 'register'): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('<?= $reopenModal === 'register' ? 'registerModal' : 'loginModal' ?>');
    if (modalEl && window.bootstrap) {
        new bootstrap.Modal(modalEl).show();
    }
});
</script>
<?php endif; ?>
