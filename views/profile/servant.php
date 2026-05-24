<?php
$selectedGender = old('gender', (string) ($profile['gender'] ?? ''));
$selectedCurrency = strtoupper(old('currency', (string) ($profile['currency'] ?? 'ETB')));
$selectedAvailability = old('availability', (string) ($profile['availability'] ?? ''));
$skillsValue = old('skills', (string) ($skillsText ?? ''));
$profilePhotoUrl = (string) ($profile['profile_photo'] ?? '');
$frontIdUrl = (string) ($profile['fayda_id_front_url'] ?? '');
$backIdUrl = (string) ($profile['fayda_id_back_url'] ?? '');
$selfieUrl = (string) ($profile['selfie_url'] ?? '');
$resumeFilename = (string) ($resumeFilename ?? ($profile['resume_filename'] ?? ''));
$resumeAvailable = (bool) ($resumeAvailable ?? (!empty($profile['resume_storage_name']) && !empty($profile['resume_filename'])));
$statusLabel = ServantProfile::verificationStatusLabel((string) ($profile['verification_status'] ?? 'pending'));
$statusTone = normalizeRole((string) ($profile['verification_status'] ?? '')) === 'verified' ? 'badge-success' : 'badge-warning';
$availabilityOptions = ['Full-time', 'Part-time', 'Weekdays', 'Weekends', 'Flexible'];
$currencyOptions = ['ETB'];
$allCategories = require 'config/categories.php';
?>

<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="card mb-8">
        <div class="flex flex-wrap justify-between items-center gap-6">
            <div class="flex-1">
                <h2 class="text-3xl font-extrabold mb-2">Provider Profile</h2>
                <p class="text-muted">Complete your details to start receiving job requests.</p>
            </div>
            <div class="text-right">
                <div class="badge <?= escape($statusTone); ?> mb-2"><?= escape($statusLabel); ?></div>
                <p class="text-xs text-muted">Verification Status</p>
            </div>
        </div>
    </div>

    <?php if (!empty($profile['verification_notes'])): ?>
        <div class="alert alert-error mb-8">
            <span class="material-symbols-outlined">feedback</span>
            <div>
                <p class="font-bold">Action Required: Verification Feedback</p>
                <p class="text-sm opacity-90"><?= escape((string) $profile['verification_notes']); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?= escape(appUrl('/profile/servant')); ?>" method="POST" enctype="multipart/form-data" data-provider-verification-form novalidate class="flex flex-col gap-8">
        <input type="hidden" name="csrf_token" value="<?= escape($csrfToken ?? csrfToken()); ?>">

        <!-- Section 1: Identity -->
        <div class="card">
            <div class="card-header border-b mb-6 pb-4">
                <h3 class="card-title">Personal Identity</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="input-group" data-field="full_name">
                    <label class="label">Full Name <span class="required-mark">*</span></label>
                    <input name="full_name" type="text" class="input" value="<?= escape(old('full_name', (string) ($profile['full_name'] ?? ''))); ?>" placeholder="Legal name as on ID" required>
                    <p class="field-error" data-error-for="full_name"></p>
                </div>
                
                <div class="input-group" data-field="location">
                    <label class="label">Location <span class="required-mark">*</span></label>
                    <input name="location" type="text" class="input" value="<?= escape(old('location', (string) ($profile['location'] ?? ''))); ?>" placeholder="City, Area" required>
                    <p class="field-error" data-error-for="location"></p>
                </div>

                <div class="input-group" data-field="age">
                    <label class="label">Age <span class="required-mark">*</span></label>
                    <input name="age" type="number" min="18" max="80" class="input" value="<?= escape(old('age', (string) ($profile['age'] ?? ''))); ?>" placeholder="18+" required>
                    <p class="field-hint">Must be 18 or older.</p>
                    <p class="field-error" data-error-for="age"></p>
                </div>

                <div class="input-group" data-field="gender">
                    <label class="label">Gender <span class="required-mark">*</span></label>
                    <select name="gender" class="select" required>
                        <option value="">Select gender</option>
                        <option value="male" <?= $selectedGender === 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?= $selectedGender === 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?= $selectedGender === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <p class="field-error" data-error-for="gender"></p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t">
                <div class="input-group" data-field="national_id">
                    <label class="label">National ID / Passport Number <span class="required-mark">*</span></label>
                    <input name="national_id" type="text" class="input" value="<?= escape(old('national_id', (string) ($profile['national_id'] ?? ''))); ?>" placeholder="Enter ID number" required>
                    <p class="text-xs text-muted mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size: 14px;">lock</span>
                        This information is encrypted and never shown publicly.
                    </p>
                    <p class="field-error" data-error-for="national_id"></p>
                </div>
            </div>
        </div>

        <!-- Section 2: Professional -->
        <div class="card">
            <div class="card-header border-b mb-6 pb-4">
                <h3 class="card-title">Professional Profile</h3>
            </div>

            <div class="flex flex-col gap-6">
                <div class="input-group" data-field="skills">
                    <label class="label">Skills <span class="required-mark">*</span></label>
                    <div class="chip-composer" data-chip-composer>
                        <input type="text" class="input chip-composer__input" placeholder="Type a skill and press Enter (e.g. Cooking, Cleaning)" autocomplete="off">
                        <input type="hidden" name="skills" value="<?= escape($skillsValue); ?>" data-chip-output>
                        <div class="chip-list" data-chip-list>
                            <?php foreach (array_filter(array_map('trim', explode(',', (string) $skillsValue))) as $skill): ?>
                                <span class="chip" data-chip-item>
                                    <span><?= escape($skill); ?></span>
                                    <button type="button" class="chip__remove">&times;</button>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <p class="field-error" data-error-for="skills"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="input-group" data-field="experience">
                        <label class="label">Experience <span class="required-mark">*</span></label>
                        <input name="experience" type="text" class="input" value="<?= escape(old('experience', (string) ($profile['experience'] ?? ''))); ?>" placeholder="e.g. 5 years in childcare" required>
                        <p class="field-error" data-error-for="experience"></p>
                    </div>

                    <div class="input-group" data-field="hourly_rate">
                        <label class="label">Hourly Rate <span class="required-mark">*</span></label>
                        <div class="rate-field">
                            <input name="hourly_rate" type="number" min="0" step="1" class="input rate-field__amount" value="<?= escape(old('hourly_rate', (string) ($profile['hourly_rate'] ?? $profile['rate'] ?? ''))); ?>" placeholder="500" required>
                            <select name="currency" class="select rate-field__currency">
                                <?php foreach ($currencyOptions as $currency): ?>
                                    <option value="<?= escape($currency); ?>" <?= $selectedCurrency === $currency ? 'selected' : ''; ?>><?= escape($currency); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <p class="field-error" data-error-for="hourly_rate"></p>
                    </div>
                </div>

                <div class="input-group" data-field="category">
                    <label class="label">Service Category <span class="required-mark">*</span></label>
                    <select name="category" class="select" required>
                        <option value="">Select a category</option>
                        <?php foreach ($allCategories as $slug => $label): ?>
                            <option value="<?= escape($slug); ?>" <?= old('category', (string) ($profile['category'] ?? '')) === $slug ? 'selected' : ''; ?>><?= escape($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="field-error" data-error-for="category"></p>
                </div>

                <div class="input-group" data-field="bio">
                    <label class="label">Professional Overview (Bio)</label>
                    <textarea name="bio" class="input" rows="4" placeholder="Tell parents about yourself, your experience, and why they should hire you..."><?= escape(old('bio', (string) ($profile['bio'] ?? ''))); ?></textarea>
                    <p class="field-error" data-error-for="bio"></p>
                </div>

                <div class="input-group" data-field="availability">
                    <label class="label">Availability <span class="required-mark">*</span></label>
                    <div class="segmented-control" role="radiogroup">
                        <?php foreach ($availabilityOptions as $option): ?>
                            <label class="segmented-control__item">
                                <input type="radio" name="availability" value="<?= escape($option); ?>" <?= $selectedAvailability === $option ? 'checked' : ''; ?> required>
                                <span><?= escape($option); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="field-error" data-error-for="availability"></p>
                </div>
            </div>
        </div>

        <!-- Section 3: Resume -->
        <div class="card">
            <div class="card-header border-b mb-6 pb-4">
                <h3 class="card-title">Resume / CV</h3>
            </div>

            <div class="flex flex-col gap-4">
                <div class="input-group" data-field="resume_upload">
                    <label class="label" for="resume_upload">Resume / CV</label>
                    <input id="resume_upload" name="resume_upload" type="file" class="input" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    <p class="field-hint">PDF, DOC, or DOCX only. Maximum 5MB.</p>
                    <p class="field-error" data-error-for="resume_upload"></p>
                </div>

                <?php if ($resumeAvailable): ?>
                    <div class="rounded-xl border p-4 flex flex-wrap items-center justify-between gap-4 bg-slate-50">
                        <div>
                            <p class="font-semibold mb-1"><?= escape($resumeFilename); ?></p>
                            <p class="text-xs text-muted m-0">Uploaded resume file</p>
                        </div>
                        <a href="<?= escape(appUrl('/profile/servant/resume')); ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                            View / Download
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section 4: Guarantor Information -->
        <div class="card">
            <div class="card-header border-b mb-6 pb-4">
                <h3 class="card-title">Guarantor Information</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="input-group" data-field="guarantor_full_name">
                    <label class="label">Guarantor Full Name <span class="required-mark">*</span></label>
                    <input name="guarantor_full_name" type="text" class="input" value="<?= escape(old('guarantor_full_name', (string) ($profile['guarantor']['full_name'] ?? ''))); ?>" required>
                    <p class="field-error" data-error-for="guarantor_full_name"></p>
                </div>
                <div class="input-group" data-field="guarantor_phone">
                    <label class="label">Guarantor Phone <span class="required-mark">*</span></label>
                    <input name="guarantor_phone" type="text" class="input" value="<?= escape(old('guarantor_phone', (string) ($profile['guarantor']['phone'] ?? ''))); ?>" required>
                    <p class="field-error" data-error-for="guarantor_phone"></p>
                </div>
                <div class="input-group" data-field="guarantor_alt_phone">
                    <label class="label">Guarantor Alternative Phone</label>
                    <input name="guarantor_alt_phone" type="text" class="input" value="<?= escape(old('guarantor_alt_phone', (string) ($profile['guarantor']['alt_phone'] ?? ''))); ?>">
                    <p class="field-error" data-error-for="guarantor_alt_phone"></p>
                </div>
                <div class="input-group" data-field="guarantor_relationship">
                    <label class="label">Relationship to Servant <span class="required-mark">*</span></label>
                    <input name="guarantor_relationship" type="text" class="input" value="<?= escape(old('guarantor_relationship', (string) ($profile['guarantor']['relationship'] ?? ''))); ?>" placeholder="e.g. Uncle, Parent" required>
                    <p class="field-error" data-error-for="guarantor_relationship"></p>
                </div>
                <div class="input-group" data-field="guarantor_address">
                    <label class="label">Guarantor Address <span class="required-mark">*</span></label>
                    <input name="guarantor_address" type="text" class="input" value="<?= escape(old('guarantor_address', (string) ($profile['guarantor']['address'] ?? ''))); ?>" required>
                    <p class="field-error" data-error-for="guarantor_address"></p>
                </div>
                <div class="input-group" data-field="guarantor_occupation">
                    <label class="label">Guarantor Occupation <span class="required-mark">*</span></label>
                    <input name="guarantor_occupation" type="text" class="input" value="<?= escape(old('guarantor_occupation', (string) ($profile['guarantor']['occupation'] ?? ''))); ?>" required>
                    <p class="field-error" data-error-for="guarantor_occupation"></p>
                </div>
                <div class="input-group" data-field="guarantor_national_id">
                    <label class="label">Guarantor National ID</label>
                    <input name="guarantor_national_id" type="text" class="input" value="<?= escape(old('guarantor_national_id', (string) ($profile['guarantor']['national_id'] ?? ''))); ?>">
                    <p class="field-error" data-error-for="guarantor_national_id"></p>
                </div>
                <div class="input-group" data-field="guarantor_id_upload">
                    <label class="label">Guarantor ID Upload</label>
                    <input type="hidden" name="guarantor_id_remove" value="0" data-upload-remove-flag>
                    <input name="guarantor_id_upload" type="file" class="input" accept="image/*">
                    <?php if (!empty($profile['guarantor']['id_upload_url'])): ?>
                        <p class="text-xs text-muted mt-2">Current ID uploaded: <a href="<?= escape($profile['guarantor']['id_upload_url']); ?>" target="_blank">View</a></p>
                    <?php endif; ?>
                    <p class="field-error" data-error-for="guarantor_id_upload"></p>
                </div>
            </div>
        </div>

        <!-- Section 5: Photos -->
        <div class="card" data-upload-card="profile-photo" data-existing-url="<?= escape($profilePhotoUrl); ?>">
            <div class="card-header border-b mb-6 pb-4">
                <h3 class="card-title">Profile Photo</h3>
            </div>
            
            <input type="hidden" name="profile_photo_remove" value="0" data-upload-remove-flag>
            <input type="file" name="profile_photo_upload" accept="image/*" class="is-hidden" data-upload-input>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="upload-card" data-upload-dropzone>
                    <div class="upload-card__preview" data-upload-preview>
                        <?php if (!empty($profilePhotoUrl)): ?>
                            <img src="<?= escape($profilePhotoUrl); ?>" data-upload-image>
                        <?php else: ?>
                            <div class="upload-empty-state" data-upload-empty>
                                <span class="material-symbols-outlined">add_a_photo</span>
                                <p class="text-sm">Drag photo here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <p class="text-sm text-muted">Use a professional headshot. This is the first thing clients will see.</p>
                    <div class="flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm flex-1" data-upload-trigger>
                            <span class="material-symbols-outlined">upload</span> Upload
                        </button>
                        <button type="button" class="btn btn-outline btn-sm" data-upload-clear>
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <div class="badge badge-success is-hidden" data-upload-success>Ready to upload</div>
                </div>
            </div>
            <p class="field-error" data-error-for="profile_photo_upload"></p>
        </div>

        <!-- Section 6: ID Verification -->
        <div class="card">
            <div class="card-header border-b mb-6 pb-4">
                <h3 class="card-title">Identity Verification</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Front ID -->
                <div class="flex flex-col gap-4" data-upload-card="fayda_id_front" data-existing-url="<?= escape($frontIdUrl); ?>">
                    <label class="label">ID Front <span class="required-mark">*</span></label>
                    <input type="hidden" name="fayda_id_front_remove" value="0" data-upload-remove-flag>
                    <input type="file" name="fayda_id_front" accept="image/*" class="is-hidden" data-upload-input required>
                    <div class="upload-card" data-upload-dropzone>
                        <div class="upload-card__preview" data-upload-preview>
                            <?php if (!empty($frontIdUrl)): ?>
                                <img src="<?= escape($frontIdUrl); ?>" data-upload-image>
                            <?php else: ?>
                                <div class="upload-empty-state" data-upload-empty>
                                    <span class="material-symbols-outlined">badge</span>
                                    <p class="text-xs">Front View</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" data-upload-trigger>Choose File</button>
                    <p class="field-error" data-error-for="fayda_id_front"></p>
                </div>

                <!-- Back ID -->
                <div class="flex flex-col gap-4" data-upload-card="fayda_id_back" data-existing-url="<?= escape($backIdUrl); ?>">
                    <label class="label">ID Back <span class="required-mark">*</span></label>
                    <input type="hidden" name="fayda_id_back_remove" value="0" data-upload-remove-flag>
                    <input type="file" name="fayda_id_back" accept="image/*" class="is-hidden" data-upload-input required>
                    <div class="upload-card" data-upload-dropzone>
                        <div class="upload-card__preview" data-upload-preview>
                            <?php if (!empty($backIdUrl)): ?>
                                <img src="<?= escape($backIdUrl); ?>" data-upload-image>
                            <?php else: ?>
                                <div class="upload-empty-state" data-upload-empty>
                                    <span class="material-symbols-outlined">credit_card</span>
                                    <p class="text-xs">Back View</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" data-upload-trigger>Choose File</button>
                    <p class="field-error" data-error-for="fayda_id_back"></p>
                </div>
            </div>

            <!-- Selfie -->
            <div class="mt-8 pt-8 border-t" data-selfie-flow data-existing-selfie-url="<?= escape($selfieUrl); ?>">
                <label class="label mb-4">Live Selfie Verification <span class="required-mark">*</span></label>
                <div class="selfie-card">
                    <div class="selfie-card__media">
                        <video id="selfie_video" autoplay playsinline muted class="selfie-video"></video>
                        <canvas id="selfie_canvas" width="720" height="540" hidden></canvas>
                        <input name="selfie_capture_data" type="hidden" value="<?= escape(old('selfie_capture_data', '')); ?>" data-selfie-output>
                        <img id="selfie_preview" class="selfie-preview <?= empty($selfieUrl) ? 'is-hidden' : ''; ?>" src="<?= escape($selfieUrl); ?>" data-selfie-preview>
                        <div class="selfie-placeholder <?= empty($selfieUrl) ? '' : 'is-hidden'; ?>" data-selfie-placeholder>
                            <span class="material-symbols-outlined">camera_front</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4">
                        <p class="text-sm text-muted">Please capture a clear, front-facing selfie. Your face must be fully visible.</p>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" id="selfie_start_camera" class="btn btn-primary btn-sm">Start</button>
                            <button type="button" id="selfie_capture_button" class="btn btn-secondary btn-sm" disabled>Capture</button>
                            <button type="button" id="selfie_retake_button" class="btn btn-outline btn-sm" disabled>Retake</button>
                            <button type="button" id="selfie_stop_button" class="btn btn-outline btn-sm" disabled>Stop</button>
                        </div>
                        <p id="selfie_capture_status" class="text-xs font-bold text-primary"></p>
                    </div>
                </div>
                <p class="field-error" data-error-for="selfie_capture_data"></p>
            </div>
        </div>

        <!-- Submit -->
        <div class="card bg-primary-50 border-primary-200">
            <div class="flex flex-wrap justify-between items-center gap-6">
                <div>
                    <h4 class="font-bold">Ready to submit?</h4>
                    <p class="text-sm text-muted m-0">Your profile will be reviewed by our team within 24 hours.</p>
                </div>
                <button type="submit" class="btn btn-primary px-10">
                    Submit for Review
                </button>
            </div>
        </div>
    </form>
</div>
