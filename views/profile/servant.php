<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header" style="border-bottom: 2px solid var(--border-light); padding-bottom: 1.5rem;">
        <div class="flex justify-between items-center w-100">
            <div>
                <h2 class="card-title">Service Provider Profile</h2>
                <p class="text-sm text-muted">Complete your profile to start receiving job invitations</p>
            </div>
            <?php if (!empty($profile)): ?>
                <div class="badge <?= normalizeRole((string)($profile['verification_status'] ?? '')) === 'verified' ? 'badge-success' : 'badge-warning'; ?>">
                    <?= escape(ServantProfile::verificationStatusLabel((string) ($profile['verification_status'] ?? 'pending'))); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body" style="padding-top: 2rem;">
        <?php if (!empty($profile['verification_notes'])): ?>
            <div class="alert alert-info" style="margin-bottom: 2rem;">
                <span class="material-symbols-outlined">info</span>
                <div>
                    <p class="font-700">Verification Feedback</p>
                    <p class="text-sm"><?= escape((string) $profile['verification_notes']); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form action="/profile/servant" method="POST" enctype="multipart/form-data" class="flex flex-col gap-8" novalidate>
            <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

            <!-- Section: Basic Information -->
            <div class="form-section">
                <h3 class="text-sm font-800 uppercase letter-spacing-lg mb-4" style="color: var(--primary);">Basic Information</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="full_name" class="label">Legal Full Name</label>
                        <input id="full_name" name="full_name" type="text" class="input-field" 
                               value="<?= escape(old('full_name', (string) ($profile['full_name'] ?? ''))); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="national_id" class="label">National ID / Passport Number</label>
                        <input id="national_id" name="national_id" type="text" class="input-field" 
                               value="<?= escape(old('national_id', (string) ($profile['national_id'] ?? ''))); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="age" class="label">Age</label>
                        <input id="age" name="age" type="number" min="18" max="80" class="input-field" 
                               value="<?= escape(old('age', (string) ($profile['age'] ?? ''))); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gender" class="label">Gender Identity</label>
                        <?php $selectedGender = old('gender', (string) ($profile['gender'] ?? '')); ?>
                        <select id="gender" name="gender" class="select" required>
                            <option value="">Select gender</option>
                            <option value="male" <?= $selectedGender === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?= $selectedGender === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?= $selectedGender === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section: Professional Details -->
            <div class="form-section">
                <h3 class="text-sm font-800 uppercase letter-spacing-lg mb-4" style="color: var(--primary);">Professional Details</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="skills" class="label">Skills</label>
                        <input id="skills" name="skills" type="text" class="input-field" 
                               value="<?= escape(old('skills', (string) ($skillsText ?? ''))); ?>" 
                               placeholder="e.g. Cooking, Childcare, Cleaning" required>
                        <p class="text-sm text-muted mt-2">Comma separated list of skills.</p>
                    </div>
                    <div class="form-group">
                        <label for="experience" class="label">Years of Experience</label>
                        <input id="experience" name="experience" type="text" class="input-field" 
                               value="<?= escape(old('experience', (string) ($profile['experience'] ?? ''))); ?>" 
                               placeholder="e.g. 5 years" required>
                    </div>
                    <div class="form-group">
                        <label for="rate" class="label">Expected Hourly Rate</label>
                        <div class="input-wrapper" style="position: relative;">
                            <input id="rate" name="rate" type="text" class="input-field" 
                                   value="<?= escape(old('rate', (string) ($profile['rate'] ?? ''))); ?>" 
                                   placeholder="500" required style="padding-right: 4rem;">
                            <span style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); font-weight: 700; color: var(--text-muted);">BDT</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="availability" class="label">General Availability</label>
                        <input id="availability" name="availability" type="text" class="input-field" 
                               value="<?= escape(old('availability', (string) ($profile['availability'] ?? ''))); ?>" 
                               placeholder="e.g. Weekdays 9am-5pm" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="location" class="label">Service Area / Location</label>
                    <input id="location" name="location" type="text" class="input-field" 
                           value="<?= escape(old('location', (string) ($profile['location'] ?? ''))); ?>" required>
                </div>
                <div class="form-group">
                    <label for="profile_photo" class="label">Profile Picture URL</label>
                    <input id="profile_photo" name="profile_photo" type="url" class="input-field" 
                           value="<?= escape(old('profile_photo', (string) ($profile['profile_photo'] ?? ''))); ?>" 
                           placeholder="https://example.com/photo.jpg" required>
                </div>
            </div>

            <!-- Section: Identity Verification -->
            <div class="form-section">
                <h3 class="text-sm font-800 uppercase letter-spacing-lg mb-4" style="color: var(--primary);">Identity Verification</h3>
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="form-group">
                        <label for="fayda_id_front" class="label">Fayda ID (Front)</label>
                        <div class="file-upload-wrapper" style="padding: 1.5rem; border: 2px dashed var(--border-base); border-radius: var(--radius-md); text-align: center; background: var(--background);">
                            <span class="material-symbols-outlined" style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 0.5rem;">upload_file</span>
                            <input id="fayda_id_front" name="fayda_id_front" type="file" accept="image/jpeg,image/png" style="display: block; width: 100%; margin-top: 0.5rem;" <?= empty($profile['fayda_id_front_url']) ? 'required' : ''; ?>>
                            <?php if (!empty($profile['fayda_id_front_url'])): ?>
                                <div class="mt-2 text-sm">
                                    <a href="<?= escape((string) $profile['fayda_id_front_url']); ?>" target="_blank" class="flex items-center justify-center gap-1" style="color: var(--primary); font-weight: 600;">
                                        <span class="material-symbols-outlined" style="font-size: 1rem;">visibility</span> View Current
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fayda_id_back" class="label">Fayda ID (Back)</label>
                        <div class="file-upload-wrapper" style="padding: 1.5rem; border: 2px dashed var(--border-base); border-radius: var(--radius-md); text-align: center; background: var(--background);">
                            <span class="material-symbols-outlined" style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 0.5rem;">upload_file</span>
                            <input id="fayda_id_back" name="fayda_id_back" type="file" accept="image/jpeg,image/png" style="display: block; width: 100%; margin-top: 0.5rem;" <?= empty($profile['fayda_id_back_url']) ? 'required' : ''; ?>>
                            <?php if (!empty($profile['fayda_id_back_url'])): ?>
                                <div class="mt-2 text-sm">
                                    <a href="<?= escape((string) $profile['fayda_id_back_url']); ?>" target="_blank" class="flex items-center justify-center gap-1" style="color: var(--primary); font-weight: 600;">
                                        <span class="material-symbols-outlined" style="font-size: 1rem;">visibility</span> View Current
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="label">Live Selfie Identity Check</label>
                    <div class="liveness-check-container card" style="background: var(--background); padding: 2rem; border-style: dashed; border-width: 2px;">
                        <div class="grid grid-cols-2 gap-8 items-center" data-selfie-capture>
                            <div class="camera-preview-area">
                                <video id="selfie_video" autoplay playsinline muted style="width:100%; border-radius: var(--radius-lg); background:#000; box-shadow: var(--shadow-lg); aspect-ratio: 4/3; object-fit: cover;"></video>
                                <canvas id="selfie_canvas" width="480" height="360" hidden></canvas>
                                <input id="selfie_capture_data" name="selfie_capture_data" type="hidden" value="">
                                
                                <div class="flex gap-3 mt-4">
                                    <button type="button" id="selfie_start_camera" class="btn btn-outline" style="flex: 1;">
                                        <span class="material-symbols-outlined">videocam</span> Start
                                    </button>
                                    <button type="button" id="selfie_capture_button" class="btn btn-secondary" disabled style="flex: 1;">
                                        <span class="material-symbols-outlined">photo_camera</span> Capture
                                    </button>
                                </div>
                            </div>
                            <div class="preview-area text-center">
                                <div id="preview_placeholder" style="aspect-ratio: 4/3; display: flex; align-items: center; justify-content: center; border: 2px dashed var(--border-base); border-radius: var(--radius-lg); color: var(--text-muted);">
                                    <div class="flex flex-col items-center">
                                        <span class="material-symbols-outlined" style="font-size: 3rem;">portrait</span>
                                        <p class="text-sm px-4">Captured selfie will appear here</p>
                                    </div>
                                </div>
                                <img id="selfie_preview" alt="Captured selfie preview" style="display:none; width:100%; aspect-ratio: 4/3; object-fit: cover; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
                                <p id="selfie_capture_status" class="text-sm font-700 mt-3" style="color: var(--primary);">Camera not started.</p>
                                <?php if (!empty($profile['selfie_url'])): ?>
                                    <div class="mt-2 text-sm">
                                        <a href="<?= escape((string) $profile['selfie_url']); ?>" target="_blank" class="flex items-center justify-center gap-1" style="color: var(--primary); font-weight: 600;">
                                            <span class="material-symbols-outlined" style="font-size: 1rem;">image</span> View Last Selfie
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-8" style="border-top: 1px solid var(--border-light);">
                <button type="submit" class="btn btn-primary" style="padding-inline: 4rem; height: 56px; font-size: 1.1rem;">
                    <span class="material-symbols-outlined">verified_user</span>
                    Submit Profile for Verification
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
    const wrapper = document.querySelector('[data-selfie-capture]');
    if (!wrapper) return;

    const video = document.getElementById('selfie_video');
    const canvas = document.getElementById('selfie_canvas');
    const hiddenInput = document.getElementById('selfie_capture_data');
    const startButton = document.getElementById('selfie_start_camera');
    const captureButton = document.getElementById('selfie_capture_button');
    const preview = document.getElementById('selfie_preview');
    const placeholder = document.getElementById('preview_placeholder');
    const status = document.getElementById('selfie_capture_status');
    const form = wrapper.closest('form');
    let stream = null;

    const stopStream = () => {
        if (!stream) return;
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    };

    startButton.addEventListener('click', async () => {
        try {
            stopStream();
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = stream;
            captureButton.disabled = false;
            status.textContent = 'Camera is active';
            status.style.color = 'var(--secondary)';
        } catch (error) {
            captureButton.disabled = true;
            status.textContent = 'Camera access denied';
            status.style.color = 'var(--danger)';
        }
    });

    captureButton.addEventListener('click', () => {
        const ctx = canvas.getContext('2d');
        if (!ctx || !video.videoWidth) {
            status.textContent = 'Wait for camera...';
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        hiddenInput.value = dataUrl;
        preview.src = dataUrl;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
        status.textContent = 'Selfie captured!';
        status.style.color = 'var(--secondary)';
    });

    form.addEventListener('submit', (e) => {
        const hasExisting = <?= !empty($profile['selfie_url']) ? 'true' : 'false'; ?>;
        if (!hiddenInput.value && !hasExisting) {
            e.preventDefault();
            status.textContent = 'Action Required: Capture a selfie first';
            status.style.color = 'var(--danger)';
            status.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    window.addEventListener('beforeunload', stopStream);
})();
</script>

