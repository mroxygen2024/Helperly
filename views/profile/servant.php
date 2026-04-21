<section class="card">
    <h1>Servant Profile</h1>
    <p class="muted">Create or update your service provider profile details.</p>

    <?php if (!empty($profile)): ?>
        <p><strong>Verification status:</strong> <?= escape(ServantProfile::verificationStatusLabel((string) ($profile['verification_status'] ?? 'pending'))); ?></p>
        <?php if (!empty($profile['verification_notes'])): ?>
            <p class="muted"><strong>Verification notes:</strong> <?= escape((string) $profile['verification_notes']); ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <form action="/profile/servant" method="POST" enctype="multipart/form-data" class="form-grid" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <label for="full_name">Full Name</label>
        <input id="full_name" name="full_name" type="text" value="<?= escape(old('full_name', (string) ($profile['full_name'] ?? ''))); ?>" required>

        <label for="national_id">National ID</label>
        <input id="national_id" name="national_id" type="text" value="<?= escape(old('national_id', (string) ($profile['national_id'] ?? ''))); ?>" required>

        <label for="age">Age</label>
        <input id="age" name="age" type="number" min="18" max="80" value="<?= escape(old('age', (string) ($profile['age'] ?? ''))); ?>" required>

        <label for="gender">Gender</label>
        <?php $selectedGender = old('gender', (string) ($profile['gender'] ?? '')); ?>
        <select id="gender" name="gender" required>
            <option value="">Select gender</option>
            <option value="male" <?= $selectedGender === 'male' ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?= $selectedGender === 'female' ? 'selected' : ''; ?>>Female</option>
            <option value="other" <?= $selectedGender === 'other' ? 'selected' : ''; ?>>Other</option>
        </select>

        <label for="skills">Skills (comma separated)</label>
        <input id="skills" name="skills" type="text" value="<?= escape(old('skills', (string) ($skillsText ?? ''))); ?>" required>

        <label for="experience">Experience</label>
        <input id="experience" name="experience" type="text" value="<?= escape(old('experience', (string) ($profile['experience'] ?? ''))); ?>" required>

        <label for="location">Location</label>
        <input id="location" name="location" type="text" value="<?= escape(old('location', (string) ($profile['location'] ?? ''))); ?>" required>

        <label for="availability">Availability</label>
        <input id="availability" name="availability" type="text" value="<?= escape(old('availability', (string) ($profile['availability'] ?? ''))); ?>" required>

        <label for="hourly_rate">Hourly Rate</label>
        <input id="hourly_rate" name="hourly_rate" type="text" value="<?= escape(old('hourly_rate', (string) ($profile['hourly_rate'] ?? ''))); ?>" placeholder="e.g. 500 BDT/hour" required>

        <label for="profile_photo">Profile Photo URL</label>
        <input id="profile_photo" name="profile_photo" type="url" value="<?= escape(old('profile_photo', (string) ($profile['profile_photo'] ?? ''))); ?>" placeholder="https://..." required>

        <label for="fayda_id_front">Fayda ID Front (JPG/PNG, max 5MB)</label>
        <input id="fayda_id_front" name="fayda_id_front" type="file" accept="image/jpeg,image/png" required>
        <?php if (!empty($profile['fayda_id_front_url'])): ?>
            <p class="muted">Current: <a href="<?= escape((string) $profile['fayda_id_front_url']); ?>" target="_blank" rel="noopener noreferrer">View uploaded front image</a></p>
        <?php endif; ?>

        <label for="fayda_id_back">Fayda ID Back (JPG/PNG, max 5MB)</label>
        <input id="fayda_id_back" name="fayda_id_back" type="file" accept="image/jpeg,image/png" required>
        <?php if (!empty($profile['fayda_id_back_url'])): ?>
            <p class="muted">Current: <a href="<?= escape((string) $profile['fayda_id_back_url']); ?>" target="_blank" rel="noopener noreferrer">View uploaded back image</a></p>
        <?php endif; ?>

        <label>Selfie Live Check (camera capture only)</label>
        <p class="muted">Use your webcam to capture a live selfie. Gallery/file upload is disabled for this basic liveness step.</p>
        <div class="camera-capture" data-selfie-capture>
            <video id="selfie_video" autoplay playsinline muted style="width:100%;max-width:360px;border-radius:8px;background:#111;"></video>
            <canvas id="selfie_canvas" width="480" height="360" hidden></canvas>
            <input id="selfie_capture_data" name="selfie_capture_data" type="hidden" value="">
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                <button type="button" id="selfie_start_camera" class="btn">Start Camera</button>
                <button type="button" id="selfie_capture_button" class="btn" disabled>Capture Selfie</button>
            </div>
            <img id="selfie_preview" alt="Captured selfie preview" style="display:none;width:100%;max-width:360px;border-radius:8px;">
            <p id="selfie_capture_status" class="muted">Camera not started.</p>
        </div>
        <?php if (!empty($profile['selfie_url'])): ?>
            <p class="muted">Current: <a href="<?= escape((string) $profile['selfie_url']); ?>" target="_blank" rel="noopener noreferrer">View uploaded selfie</a></p>
        <?php endif; ?>

        <button type="submit" class="btn">Save Profile</button>
    </form>
</section>

<script>
(() => {
    const wrapper = document.querySelector('[data-selfie-capture]');
    if (!wrapper) {
        return;
    }

    const video = document.getElementById('selfie_video');
    const canvas = document.getElementById('selfie_canvas');
    const hiddenInput = document.getElementById('selfie_capture_data');
    const startButton = document.getElementById('selfie_start_camera');
    const captureButton = document.getElementById('selfie_capture_button');
    const preview = document.getElementById('selfie_preview');
    const status = document.getElementById('selfie_capture_status');
    const form = wrapper.closest('form');
    let stream = null;

    if (!video || !canvas || !hiddenInput || !startButton || !captureButton || !preview || !status || !form) {
        return;
    }

    const stopStream = () => {
        if (!stream) {
            return;
        }
        stream.getTracks().forEach((track) => track.stop());
        stream = null;
    };

    startButton.addEventListener('click', async () => {
        try {
            stopStream();
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = stream;
            captureButton.disabled = false;
            status.textContent = 'Camera ready. Capture your selfie now.';
        } catch (error) {
            captureButton.disabled = true;
            status.textContent = 'Could not access camera. Please allow camera permission.';
        }
    });

    captureButton.addEventListener('click', () => {
        const ctx = canvas.getContext('2d');
        if (!ctx || !video.videoWidth || !video.videoHeight) {
            status.textContent = 'Camera frame is not ready yet.';
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        hiddenInput.value = dataUrl;
        preview.src = dataUrl;
        preview.style.display = 'block';
        status.textContent = 'Selfie captured successfully.';
    });

    form.addEventListener('submit', (event) => {
        const hasExisting = <?= !empty($profile['selfie_url']) ? 'true' : 'false'; ?>;
        if (hiddenInput.value === '' && !hasExisting) {
            event.preventDefault();
            status.textContent = 'Please start camera and capture a selfie before saving.';
            status.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    window.addEventListener('beforeunload', stopStream);
})();
</script>
