<?php
$selectedGender = old('gender', (string) ($profile['gender'] ?? ''));
$selectedCurrency = strtoupper(old('currency', (string) ($profile['currency'] ?? 'ETB')));
$selectedAvailability = old('availability', (string) ($profile['availability'] ?? ''));
$skillsValue = old('skills', (string) ($skillsText ?? ''));
$profilePhotoUrl = (string) ($profile['profile_photo'] ?? '');
$frontIdUrl = (string) ($profile['fayda_id_front_url'] ?? '');
$backIdUrl = (string) ($profile['fayda_id_back_url'] ?? '');
$selfieUrl = (string) ($profile['selfie_url'] ?? '');
$statusLabel = ServantProfile::verificationStatusLabel((string) ($profile['verification_status'] ?? 'pending'));
$statusTone = normalizeRole((string) ($profile['verification_status'] ?? '')) === 'verified' ? 'badge-success' : 'badge-warning';
$availabilityOptions = [
    'Full-time',
    'Part-time',
    'Weekdays',
    'Weekends',
    'Flexible',
];
$currencyOptions = ['ETB'];
?>

<section class="profile-onboarding-shell">
    <div class="profile-onboarding-hero card">
        <div class="profile-onboarding-hero__copy">
            <div class="profile-onboarding-kicker">Provider onboarding</div>
            <h2 class="profile-onboarding-title">Complete your professional profile and verification details</h2>
            <p class="profile-onboarding-subtitle">Upload your identity documents, capture a selfie, and submit a profile that feels ready for a real marketplace.</p>
        </div>
        <div class="profile-onboarding-hero__status">
            <div class="badge <?= escape($statusTone); ?>"><?= escape($statusLabel); ?></div>
            <p class="profile-onboarding-note">Your details are reviewed before you appear in search and booking flows.</p>
        </div>
    </div>

    <?php if (!empty($profile['verification_notes'])): ?>
        <div class="alert alert-info profile-alert">
            <span class="material-symbols-outlined">info</span>
            <div>
                <p class="font-700">Verification feedback</p>
                <p class="text-sm"><?= escape((string) $profile['verification_notes']); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?= escape(appUrl('/profile/servant')); ?>" method="POST" enctype="multipart/form-data" class="profile-verification-form" data-provider-verification-form novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <div class="profile-section card">
            <div class="profile-section__header">
                <div>
                    <p class="profile-section__eyebrow">Section 1</p>
                    <h3>Basic Information</h3>
                </div>
                <p class="profile-section__hint">Keep this aligned with your legal identity.</p>
            </div>

            <div class="profile-grid profile-grid--two">
                <div class="form-group" data-field="full_name">
                    <label for="full_name" class="label">Full Name <span class="required-mark">*</span></label>
                    <input id="full_name" name="full_name" type="text" class="input-field" value="<?= escape(old('full_name', (string) ($profile['full_name'] ?? ''))); ?>" placeholder="Enter your full name" required>
                    <p class="field-error" data-error-for="full_name"></p>
                </div>
                <div class="form-group" data-field="age">
                    <label for="age" class="label">Age <span class="required-mark">*</span></label>
                    <input id="age" name="age" type="number" min="18" max="80" class="input-field" value="<?= escape(old('age', (string) ($profile['age'] ?? ''))); ?>" placeholder="18+" required>
                    <p class="field-hint">Must be between 18 and 80.</p>
                    <p class="field-error" data-error-for="age"></p>
                </div>
                <div class="form-group" data-field="gender">
                    <label for="gender" class="label">Gender <span class="required-mark">*</span></label>
                    <select id="gender" name="gender" class="select" required>
                        <option value="">Select gender</option>
                        <option value="male" <?= $selectedGender === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?= $selectedGender === 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?= $selectedGender === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <p class="field-error" data-error-for="gender"></p>
                </div>
                <div class="form-group" data-field="location">
                    <label for="location" class="label">Location <span class="required-mark">*</span></label>
                    <input id="location" name="location" type="text" class="input-field" value="<?= escape(old('location', (string) ($profile['location'] ?? ''))); ?>" placeholder="Dhaka, Chattogram, Narayanganj..." required>
                    <p class="field-error" data-error-for="location"></p>
                </div>
            </div>

            <div class="identity-note">
                <span class="material-symbols-outlined">verified_user</span>
                <div>
                    <p class="font-700">Identity details stay private</p>
                    <p class="text-sm">National ID or passport details are handled only for verification and are not shown publicly.</p>
                </div>
                <div class="identity-note__field form-group" data-field="national_id">
                    <label for="national_id" class="label">National ID / Passport Number <span class="required-mark">*</span></label>
                    <input id="national_id" name="national_id" type="text" class="input-field" value="<?= escape(old('national_id', (string) ($profile['national_id'] ?? ''))); ?>" placeholder="Enter ID number" required>
                    <p class="field-error" data-error-for="national_id"></p>
                </div>
            </div>
        </div>

        <div class="profile-section card">
            <div class="profile-section__header">
                <div>
                    <p class="profile-section__eyebrow">Section 2</p>
                    <h3>Professional Details</h3>
                </div>
                <p class="profile-section__hint">Help families understand what you offer and when.</p>
            </div>

            <div class="profile-grid profile-grid--two">
                <div class="form-group profile-skills" data-field="skills">
                    <label for="skills_input" class="label">Skills <span class="required-mark">*</span></label>
                    <div class="chip-composer" data-chip-composer>
                        <input id="skills_input" type="text" class="input-field chip-composer__input" placeholder="Type a skill and press Enter" autocomplete="off">
                        <input type="hidden" name="skills" value="<?= escape($skillsValue); ?>" data-chip-output>
                        <div class="chip-list" data-chip-list>
                            <?php foreach (array_filter(array_map('trim', explode(',', (string) $skillsValue))) as $skill): ?>
                                <span class="chip" data-chip-item>
                                    <span><?= escape($skill); ?></span>
                                    <button type="button" class="chip__remove" aria-label="Remove skill">&times;</button>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <p class="field-hint">Press Enter or comma to add a skill.</p>
                    </div>
                    <p class="field-error" data-error-for="skills"></p>
                </div>

                <div class="form-group" data-field="experience">
                    <label for="experience" class="label">Experience <span class="required-mark">*</span></label>
                    <input id="experience" name="experience" type="text" class="input-field" value="<?= escape(old('experience', (string) ($profile['experience'] ?? ''))); ?>" placeholder="e.g. 5 years in childcare" required>
                    <p class="field-error" data-error-for="experience"></p>
                </div>

                <div class="form-group" data-field="hourly_rate">
                    <label for="hourly_rate" class="label">Hourly Rate <span class="required-mark">*</span></label>
                    <div class="rate-field">
                        <input id="hourly_rate" name="hourly_rate" type="number" min="0" step="1" class="input-field rate-field__amount" value="<?= escape(old('hourly_rate', (string) ($profile['hourly_rate'] ?? $profile['rate'] ?? ''))); ?>" placeholder="500" required>
                        <select id="currency" name="currency" class="select rate-field__currency">
                            <?php foreach ($currencyOptions as $currency): ?>
                                <option value="<?= escape($currency); ?>" <?= $selectedCurrency === $currency ? 'selected' : ''; ?>><?= escape($currency); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p class="field-hint">Select the currency your rate should display in.</p>
                    <p class="field-error" data-error-for="hourly_rate"></p>
                </div>

                <div class="form-group" data-field="availability">
                    <label class="label">Availability <span class="required-mark">*</span></label>
                    <div class="segmented-control" role="radiogroup" aria-label="Availability options">
                        <?php foreach ($availabilityOptions as $option): ?>
                            <label class="segmented-control__item">
                                <input type="radio" name="availability" value="<?= escape($option); ?>" <?= $selectedAvailability === $option ? 'checked' : ''; ?> required>
                                <span><?= escape($option); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="field-hint">Choose the option closest to your working pattern.</p>
                    <p class="field-error" data-error-for="availability"></p>
                </div>
            </div>
        </div>

        <div class="profile-section card" data-upload-card="profile-photo" data-existing-url="<?= escape($profilePhotoUrl); ?>" data-required="false">
            <div class="profile-section__header">
                <div>
                    <p class="profile-section__eyebrow">Section 3</p>
                    <h3>Profile Photo</h3>
                </div>
                <p class="profile-section__hint">Optional, but helps families recognize you faster.</p>
            </div>

            <input type="hidden" name="profile_photo_remove" value="0" data-upload-remove-flag>
            <input type="file" id="profile_photo_upload" name="profile_photo_upload" accept="image/jpeg,image/png,image/webp" class="sr-only" data-upload-input>

            <div class="upload-card upload-card--wide" data-upload-dropzone>
                <div class="upload-card__preview" data-upload-preview>
                    <?php if (!empty($profilePhotoUrl)): ?>
                        <img src="<?= escape($profilePhotoUrl); ?>" alt="Current profile photo" data-upload-image>
                    <?php else: ?>
                        <div class="upload-empty-state" data-upload-empty>
                            <span class="material-symbols-outlined">add_a_photo</span>
                            <p>Drag and drop a photo here or choose a file</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="upload-card__body">
                    <div class="upload-card__topline">
                        <div>
                            <p class="upload-card__title">Professional headshot</p>
                            <p class="upload-card__meta">JPG, PNG, or WebP up to 5MB</p>
                        </div>
                        <span class="upload-success-badge <?= empty($profilePhotoUrl) ? 'is-hidden' : ''; ?>" data-upload-success>✅ Profile photo uploaded</span>
                    </div>

                    <div class="upload-card__actions">
                        <button type="button" class="btn btn-primary" data-upload-trigger>
                            <span class="material-symbols-outlined">upload</span>
                            Upload photo
                        </button>
                        <button type="button" class="btn btn-outline" data-upload-clear>
                            <span class="material-symbols-outlined">close</span>
                            Remove photo
                        </button>
                    </div>

                    <div class="upload-card__status-row">
                        <span class="upload-status-text" data-upload-status><?= empty($profilePhotoUrl) ? 'No profile photo selected yet.' : '✅ Profile photo uploaded'; ?></span>
                        <span class="upload-filename" data-upload-filename><?= empty($profilePhotoUrl) ? 'Awaiting upload' : basename((string) $profilePhotoUrl); ?></span>
                    </div>
                </div>
            </div>
            <p class="field-error" data-error-for="profile_photo_upload"></p>
        </div>

        <div class="profile-section card">
            <div class="profile-section__header">
                <div>
                    <p class="profile-section__eyebrow">Section 4</p>
                    <h3>Identity Verification</h3>
                </div>
                <p class="profile-section__hint">Upload both sides of your Fayda ID and capture a live selfie.</p>
            </div>

            <div class="verification-grid">
                <div class="upload-card" data-upload-card="fayda_id_front" data-existing-url="<?= escape($frontIdUrl); ?>" data-required="true">
                    <input type="hidden" name="fayda_id_front_remove" value="0" data-upload-remove-flag>
                    <input type="file" id="fayda_id_front" name="fayda_id_front" accept="image/jpeg,image/png,image/webp" class="sr-only" data-upload-input required>
                    <div class="upload-card__preview" data-upload-preview>
                        <?php if (!empty($frontIdUrl)): ?>
                            <img src="<?= escape($frontIdUrl); ?>" alt="Fayda ID front preview" data-upload-image>
                        <?php else: ?>
                            <div class="upload-empty-state" data-upload-empty>
                                <span class="material-symbols-outlined">badges</span>
                                <p>Upload the front of your Fayda ID</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="upload-card__body">
                        <div class="upload-card__topline">
                            <div>
                                <p class="upload-card__title">Fayda Front</p>
                                <p class="upload-card__meta">Required for identity verification</p>
                            </div>
                            <span class="upload-success-badge <?= empty($frontIdUrl) ? 'is-hidden' : ''; ?>" data-upload-success>✅ Front ID uploaded successfully</span>
                        </div>
                        <div class="upload-card__actions">
                            <button type="button" class="btn btn-primary" data-upload-trigger>
                                <span class="material-symbols-outlined">upload</span>
                                Upload front
                            </button>
                            <button type="button" class="btn btn-outline" data-upload-clear>
                                <span class="material-symbols-outlined">refresh</span>
                                Remove/re-upload
                            </button>
                        </div>
                        <div class="upload-card__status-row">
                            <span class="upload-status-text" data-upload-status><?= empty($frontIdUrl) ? 'Front ID not uploaded yet.' : '✅ Front ID uploaded successfully'; ?></span>
                            <span class="upload-filename" data-upload-filename><?= empty($frontIdUrl) ? 'Awaiting upload' : basename((string) $frontIdUrl); ?></span>
                        </div>
                    </div>
                    <p class="field-error" data-error-for="fayda_id_front"></p>
                </div>

                <div class="upload-card" data-upload-card="fayda_id_back" data-existing-url="<?= escape($backIdUrl); ?>" data-required="true">
                    <input type="hidden" name="fayda_id_back_remove" value="0" data-upload-remove-flag>
                    <input type="file" id="fayda_id_back" name="fayda_id_back" accept="image/jpeg,image/png,image/webp" class="sr-only" data-upload-input required>
                    <div class="upload-card__preview" data-upload-preview>
                        <?php if (!empty($backIdUrl)): ?>
                            <img src="<?= escape($backIdUrl); ?>" alt="Fayda ID back preview" data-upload-image>
                        <?php else: ?>
                            <div class="upload-empty-state" data-upload-empty>
                                <span class="material-symbols-outlined">payment_card</span>
                                <p>Upload the back of your Fayda ID</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="upload-card__body">
                        <div class="upload-card__topline">
                            <div>
                                <p class="upload-card__title">Fayda Back</p>
                                <p class="upload-card__meta">Required for identity verification</p>
                            </div>
                            <span class="upload-success-badge <?= empty($backIdUrl) ? 'is-hidden' : ''; ?>" data-upload-success>✅ Back ID uploaded successfully</span>
                        </div>
                        <div class="upload-card__actions">
                            <button type="button" class="btn btn-primary" data-upload-trigger>
                                <span class="material-symbols-outlined">upload</span>
                                Upload back
                            </button>
                            <button type="button" class="btn btn-outline" data-upload-clear>
                                <span class="material-symbols-outlined">refresh</span>
                                Remove/re-upload
                            </button>
                        </div>
                        <div class="upload-card__status-row">
                            <span class="upload-status-text" data-upload-status><?= empty($backIdUrl) ? 'Back ID not uploaded yet.' : '✅ Back ID uploaded successfully'; ?></span>
                            <span class="upload-filename" data-upload-filename><?= empty($backIdUrl) ? 'Awaiting upload' : basename((string) $backIdUrl); ?></span>
                        </div>
                    </div>
                    <p class="field-error" data-error-for="fayda_id_back"></p>
                </div>
            </div>

            <div class="selfie-card" data-selfie-flow data-existing-selfie-url="<?= escape($selfieUrl); ?>">
                <div class="selfie-card__media">
                    <video id="selfie_video" autoplay playsinline muted class="selfie-video"></video>
                    <canvas id="selfie_canvas" width="720" height="540" hidden></canvas>
                    <input id="selfie_capture_data" name="selfie_capture_data" type="hidden" value="<?= escape(old('selfie_capture_data', '')); ?>" data-selfie-output>
                    <img id="selfie_preview" alt="Captured selfie preview" class="selfie-preview <?= empty($selfieUrl) ? 'is-hidden' : ''; ?>" src="<?= escape($selfieUrl); ?>" data-selfie-preview>
                    <div class="selfie-placeholder <?= empty($selfieUrl) ? '' : 'is-hidden'; ?>" data-selfie-placeholder>
                        <span class="material-symbols-outlined">photo_camera_front</span>
                        <p>Start the camera to capture a selfie</p>
                    </div>
                </div>

                <div class="selfie-card__body">
                    <div class="upload-card__topline">
                        <div>
                            <p class="upload-card__title">Selfie verification</p>
                            <p class="upload-card__meta">Capture a live photo for your verification check</p>
                        </div>
                        <span class="upload-success-badge <?= empty($selfieUrl) ? 'is-hidden' : ''; ?>" data-selfie-success>✅ Selfie captured successfully</span>
                    </div>

                    <div class="selfie-actions">
                        <button type="button" id="selfie_start_camera" class="btn btn-primary">
                            <span class="material-symbols-outlined">videocam</span>
                            Start Camera
                        </button>
                        <button type="button" id="selfie_capture_button" class="btn btn-secondary" disabled>
                            <span class="material-symbols-outlined">photo_camera</span>
                            Capture Selfie
                        </button>
                        <button type="button" id="selfie_retake_button" class="btn btn-outline" disabled>
                            <span class="material-symbols-outlined">refresh</span>
                            Retake
                        </button>
                        <button type="button" id="selfie_stop_button" class="btn btn-outline" disabled>
                            <span class="material-symbols-outlined">stop_circle</span>
                            Stop Camera
                        </button>
                    </div>

                    <div class="selfie-status-row">
                        <p id="selfie_capture_status" class="selfie-status-text"><?= empty($selfieUrl) ? 'Camera not started.' : '✅ Selfie captured successfully'; ?></p>
                        <p class="selfie-status-subtext">Use a bright, front-facing shot. The camera will stop automatically after capture.</p>
                    </div>
                </div>
            </div>
            <p class="field-error" data-error-for="selfie_capture_data"></p>
        </div>

        <div class="profile-submit-bar">
            <div class="profile-submit-bar__copy">
                <strong>Ready to submit?</strong>
                <span>Your profile, Fayda uploads, and selfie will be sent for review.</span>
            </div>
            <button type="submit" class="btn btn-primary profile-submit-button">
                <span class="material-symbols-outlined">verified_user</span>
                Submit for Verification
            </button>
        </div>
    </form>
</section>

