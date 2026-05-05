// assets/ purpose: static scripts for small UI behaviors.
console.info('Servant Marketplace UI loaded.');

(() => {
	// --- Generic Modal Controller ---
	const setupModals = () => {
		const openButtons = document.querySelectorAll('[data-open-modal]');
		const closeButtons = document.querySelectorAll('[data-close-modal]');

		openButtons.forEach(btn => {
			btn.onclick = (e) => {
				e.preventDefault();
				const modalId = btn.dataset.openModal;
				const modal = document.getElementById(modalId);
				if (modal) {
					modal.classList.add('open');
					document.body.style.overflow = 'hidden'; // Prevent scroll
				}
			}
		});

		closeButtons.forEach(btn => {
			btn.onclick = (e) => {
				e.preventDefault();
				const modalId = btn.dataset.closeModal;
				const modal = document.getElementById(modalId);
				if (modal) {
					modal.classList.remove('open');
					document.body.style.overflow = '';
				}
			}
		});

		window.addEventListener('click', (event) => {
			if (event.target.classList.contains('modal-overlay')) {
				event.target.classList.remove('open');
				document.body.style.overflow = '';
			}
		});

		// Escape key to close
		window.addEventListener('keydown', (e) => {
			if (e.key === 'Escape') {
				document.querySelectorAll('.modal-overlay.open').forEach(modal => {
					modal.classList.remove('open');
					document.body.style.overflow = '';
				});
			}
		});
	};

	setupModals();

	// --- Provider Verification Form Logic ---
	const form = document.querySelector('[data-provider-verification-form]');
	if (!form) {
		return;
	}

	const setFieldError = (fieldName, message) => {
		const errorNode = form.querySelector(`[data-error-for="${fieldName}"]`);
		const fieldContainer = form.querySelector(`[data-field="${fieldName}"]`);
		const input = fieldContainer?.querySelector('input, select, textarea');

		if (errorNode) {
			errorNode.textContent = message || '';
			errorNode.classList.toggle('is-visible', Boolean(message));
		}

		if (fieldContainer) {
			fieldContainer.classList.toggle('has-error', Boolean(message));
		}

		if (input) {
			input.setAttribute('aria-invalid', message ? 'true' : 'false');
		}
	};

	const clearFieldErrors = () => {
		form.querySelectorAll('[data-error-for]').forEach((node) => {
			node.textContent = '';
			node.classList.remove('is-visible');
		});
		form.querySelectorAll('[data-field]').forEach((node) => node.classList.remove('has-error'));
		form.querySelectorAll('[aria-invalid="true"]').forEach((node) => node.setAttribute('aria-invalid', 'false'));
	};

	const setupChipComposer = () => {
		const composer = form.querySelector('[data-chip-composer]');
		if (!composer) return;

		const input = composer.querySelector('.chip-composer__input');
		const output = composer.querySelector('[data-chip-output]');
		const list = composer.querySelector('[data-chip-list]');

		const readSkills = () => (output?.value || '')
			.split(',')
			.map((skill) => skill.trim())
			.filter(Boolean);

		const writeSkills = (skills) => {
			if (output) output.value = skills.join(', ');
			if (list) {
				list.innerHTML = '';
				skills.forEach((skill) => {
					const chip = document.createElement('span');
					chip.className = 'chip';
					chip.dataset.chipItem = 'true';
					chip.innerHTML = `<span>${skill}</span><button type="button" class="chip__remove" aria-label="Remove skill">&times;</button>`;
					list.appendChild(chip);
				});
			}
		};

		const addSkill = (rawValue) => {
			const value = rawValue.replace(/[,]+$/g, '').trim();
			if (!value) return;

			const skills = readSkills();
			if (!skills.some((skill) => skill.toLowerCase() === value.toLowerCase())) {
				skills.push(value);
				writeSkills(skills);
			}
			if (input) input.value = '';
			setFieldError('skills', '');
		};

		if (input) {
			input.addEventListener('keydown', (event) => {
				if (event.key === 'Enter' || event.key === ',') {
					event.preventDefault();
					addSkill(input.value);
				}
			});

			input.addEventListener('blur', () => addSkill(input.value));
		}

		list?.addEventListener('click', (event) => {
			const removeButton = event.target.closest('.chip__remove');
			if (!removeButton) return;

			const chip = removeButton.closest('[data-chip-item]');
			if (!chip) return;

			const skillText = chip.querySelector('span')?.textContent?.trim() || '';
			const remaining = readSkills().filter((skill) => skill.toLowerCase() !== skillText.toLowerCase());
			writeSkills(remaining);
			setFieldError('skills', '');
		});

		writeSkills(readSkills());
	};

	const createImagePreview = (card, file) => {
		const preview = card.querySelector('[data-upload-preview]');
		const emptyState = card.querySelector('[data-upload-empty]');
		const image = card.querySelector('[data-upload-image]');
		const filename = card.querySelector('[data-upload-filename]');
		const status = card.querySelector('[data-upload-status]');
		const success = card.querySelector('[data-upload-success]');

		if (!preview) return;

		const url = URL.createObjectURL(file);
		if (image) {
			image.src = url;
			image.classList.remove('is-hidden');
		} else {
			const nextImage = document.createElement('img');
			nextImage.src = url;
			nextImage.alt = file.name;
			nextImage.dataset.uploadImage = 'true';
			preview.appendChild(nextImage);
		}

		emptyState?.classList.add('is-hidden');
		if (filename) filename.textContent = file.name;
		if (status) status.textContent = 'Ready to upload';
		if (success) success.classList.remove('is-hidden');
		card.dataset.previewUrl = url;
	};

	const resetUploadCard = (card) => {
		const input = card.querySelector('[data-upload-input]');
		const removeFlag = card.querySelector('[data-upload-remove-flag]');
		const preview = card.querySelector('[data-upload-preview]');
		const emptyState = card.querySelector('[data-upload-empty]');
		const image = card.querySelector('[data-upload-image]');
		const filename = card.querySelector('[data-upload-filename]');
		const status = card.querySelector('[data-upload-status]');
		const success = card.querySelector('[data-upload-success]');
		const existingUrl = card.dataset.existingUrl || '';

		if (input) input.value = '';
		if (removeFlag) removeFlag.value = '1';

		if (image && preview) {
			if (existingUrl) {
				image.src = existingUrl;
				image.classList.remove('is-hidden');
			} else {
				image.remove();
				if (emptyState) emptyState.classList.remove('is-hidden');
			}
		} else if (preview && !existingUrl) {
			preview.innerHTML = '<div class="upload-empty-state"><span class="material-symbols-outlined">add_a_photo</span><p>Drag and drop a photo here or choose a file</p></div>';
		}

		if (filename) filename.textContent = existingUrl ? existingUrl.split('/').pop() || 'Current file' : 'Awaiting upload';
		if (status) status.textContent = existingUrl ? 'Current upload will be replaced or removed.' : 'No file selected yet.';
		if (success) success.classList.toggle('is-hidden', !existingUrl);

		card.dataset.previewUrl = existingUrl;
	};

	const setupUploadCard = (card) => {
		const input = card.querySelector('[data-upload-input]');
		const dropzone = card.querySelector('[data-upload-dropzone]') || card;
		const triggerButton = card.querySelector('[data-upload-trigger]');
		const clearButton = card.querySelector('[data-upload-clear]');
		const removeFlag = card.querySelector('[data-upload-remove-flag]');

		if (input) {
			input.addEventListener('change', () => {
				const file = input.files?.[0];
				if (!file) return;
				if (removeFlag) removeFlag.value = '0';
				createImagePreview(card, file);
				setFieldError(input.name, '');
			});
		}

		triggerButton?.addEventListener('click', () => input?.click());
		clearButton?.addEventListener('click', () => resetUploadCard(card));

		['dragenter', 'dragover'].forEach((eventName) => {
			dropzone.addEventListener(eventName, (event) => {
				event.preventDefault();
				dropzone.classList.add('is-dragover');
			});
		});

		['dragleave', 'drop'].forEach((eventName) => {
			dropzone.addEventListener(eventName, (event) => {
				event.preventDefault();
				dropzone.classList.remove('is-dragover');
			});
		});

		dropzone.addEventListener('drop', (event) => {
			const file = event.dataTransfer?.files?.[0];
			if (!file || !input) return;
			const dataTransfer = new DataTransfer();
			dataTransfer.items.add(file);
			input.files = dataTransfer.files;
			input.dispatchEvent(new Event('change', { bubbles: true }));
		});

		if (!card.querySelector('[data-upload-image]')) {
			resetUploadCard(card);
		}
	};

	const setupUploads = () => {
		form.querySelectorAll('[data-upload-card]').forEach((card) => setupUploadCard(card));
	};

	const setupSelfieFlow = () => {
		const flow = form.querySelector('[data-selfie-flow]');
		if (!flow) return;

		const video = form.querySelector('#selfie_video');
		const canvas = form.querySelector('#selfie_canvas');
		const output = form.querySelector('[data-selfie-output]');
		const preview = form.querySelector('[data-selfie-preview]');
		const placeholder = form.querySelector('[data-selfie-placeholder]');
		const status = form.querySelector('#selfie_capture_status');
		const success = form.querySelector('[data-selfie-success]');
		const startButton = form.querySelector('#selfie_start_camera');
		const captureButton = form.querySelector('#selfie_capture_button');
		const retakeButton = form.querySelector('#selfie_retake_button');
		const stopButton = form.querySelector('#selfie_stop_button');

		let stream = null;
		let objectUrl = '';

		const setStatus = (message, tone = 'default') => {
			if (!status) return;
			status.textContent = message;
			status.dataset.tone = tone;
		};

		const stopCamera = () => {
			if (stream) {
				stream.getTracks().forEach((track) => track.stop());
				stream = null;
			}
			if (video) {
				video.srcObject = null;
			}
			captureButton && (captureButton.disabled = true);
			stopButton && (stopButton.disabled = true);
			startButton && (startButton.disabled = false);
		};

		const activateCapturedState = () => {
			captureButton && (captureButton.disabled = true);
			retakeButton && (retakeButton.disabled = false);
			stopButton && (stopButton.disabled = true);
			startButton && (startButton.disabled = false);
			success?.classList.remove('is-hidden');
			placeholder?.classList.add('is-hidden');
			preview?.classList.remove('is-hidden');
		};

		const startCamera = async () => {
			if (!navigator.mediaDevices?.getUserMedia) {
				setStatus('Camera access is not supported in this browser.', 'error');
				return;
			}

			try {
				stopCamera();
				setStatus('Starting camera...', 'loading');
				stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
				if (video) {
					video.srcObject = stream;
					await video.play();
				}
				captureButton && (captureButton.disabled = false);
				stopButton && (stopButton.disabled = false);
				retakeButton && (retakeButton.disabled = true);
				startButton && (startButton.disabled = true);
				setStatus('Camera active', 'success');
			} catch (error) {
				stopCamera();
				setStatus('Camera not started. Please allow camera permissions and try again.', 'error');
			}
		};

		const captureSelfie = () => {
			if (!video || !canvas || !output) return;
			if (!video.videoWidth || !video.videoHeight) {
				setStatus('Camera is still loading. Try again in a moment.', 'error');
				return;
			}

			const context = canvas.getContext('2d');
			if (!context) return;

			canvas.width = video.videoWidth;
			canvas.height = video.videoHeight;
			context.drawImage(video, 0, 0, canvas.width, canvas.height);

			const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
			output.value = dataUrl;

			if (objectUrl) {
				URL.revokeObjectURL(objectUrl);
				objectUrl = '';
			}

			preview && (preview.src = dataUrl);
			preview?.classList.remove('is-hidden');
			placeholder?.classList.add('is-hidden');
			stopCamera();
			activateCapturedState();
			setStatus('✅ Selfie captured successfully', 'success');
		};

		const retakeSelfie = () => {
			if (output) output.value = '';
			if (preview) preview.classList.add('is-hidden');
			placeholder?.classList.remove('is-hidden');
			setStatus('Retake ready. Start the camera again.', 'default');
			success?.classList.add('is-hidden');
			startCamera();
		};

		startButton?.addEventListener('click', startCamera);
		captureButton?.addEventListener('click', captureSelfie);
		retakeButton?.addEventListener('click', retakeSelfie);
		stopButton?.addEventListener('click', () => {
			stopCamera();
			setStatus('Camera stopped.', 'default');
		});

		if ((output?.value || '').trim() === '') {
			retakeButton && (retakeButton.disabled = true);
		} else {
			activateCapturedState();
		}

		window.addEventListener('beforeunload', stopCamera);
	};

	const validateAvailability = () => {
		const checked = form.querySelector('input[name="availability"]:checked');
		return Boolean(checked?.value);
	};

	const validateForm = (event) => {
		clearFieldErrors();

		const requiredFields = ['full_name', 'age', 'gender', 'national_id', 'location', 'skills', 'experience', 'hourly_rate'];
		const hasExistingFaydaFront = (form.querySelector('[data-upload-card="fayda_id_front"]')?.dataset.existingUrl || '') !== '';
		const hasExistingFaydaBack = (form.querySelector('[data-upload-card="fayda_id_back"]')?.dataset.existingUrl || '') !== '';
		const hasExistingSelfie = (form.querySelector('[data-selfie-flow]')?.dataset.existingSelfieUrl || '') !== '';

		let firstInvalidField = '';

		requiredFields.forEach((fieldName) => {
			const field = form.querySelector(`[name="${fieldName}"]`);
			const value = (field?.value || '').trim();
			if (!value) {
				setFieldError(fieldName, 'This field is required.');
				firstInvalidField ||= fieldName;
			}
		});

		if (!validateAvailability()) {
			setFieldError('availability', 'Please choose your availability.');
			firstInvalidField ||= 'availability';
		}

		const chipOutput = form.querySelector('[data-chip-output]');
		if (!chipOutput || !chipOutput.value.trim()) {
			setFieldError('skills', 'Please add at least one skill.');
			firstInvalidField ||= 'skills';
		}

		const profileFront = form.querySelector('[data-upload-card="fayda_id_front"]');
		const profileBack = form.querySelector('[data-upload-card="fayda_id_back"]');
		const selfieOutput = form.querySelector('[data-selfie-output]');

		const frontSelected = Boolean(profileFront?.querySelector('[data-upload-input]')?.files?.length);
		const backSelected = Boolean(profileBack?.querySelector('[data-upload-input]')?.files?.length);
		const selfieCaptured = Boolean((selfieOutput?.value || '').trim());

		if (!hasExistingFaydaFront && !frontSelected) {
			setFieldError('fayda_id_front', 'Please upload Fayda Front ID.');
			firstInvalidField ||= 'fayda_id_front';
		}

		if (!hasExistingFaydaBack && !backSelected) {
			setFieldError('fayda_id_back', 'Please upload Fayda Back ID.');
			firstInvalidField ||= 'fayda_id_back';
		}

		if (!hasExistingSelfie && !selfieCaptured) {
			setFieldError('selfie_capture_data', 'Please capture a selfie before submitting.');
			firstInvalidField ||= 'selfie_capture_data';
		}

		if (firstInvalidField) {
			event.preventDefault();
			const target = form.querySelector(`[name="${firstInvalidField}"]`) || form.querySelector(`[data-field="${firstInvalidField}"]`);
			target?.scrollIntoView({ behavior: 'smooth', block: 'center' });
			if ('focus' in (target || {})) {
				target.focus({ preventScroll: true });
			}
		}
	};

	setupChipComposer();
	setupUploads();
	setupSelfieFlow();
	form.addEventListener('submit', validateForm);
})();

(() => {
	// --- Auth Page Interactions ---
	const setupAuthInteractions = () => {
		// Password visibility toggle
		document.querySelectorAll('[data-password-toggle]').forEach(btn => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				const inputId = btn.dataset.passwordToggle;
				const input = document.getElementById(inputId);
				const icon = btn.querySelector('.material-symbols-outlined');

				if (input.type === 'password') {
					input.type = 'text';
					icon.textContent = 'visibility_off';
				} else {
					input.type = 'password';
					icon.textContent = 'visibility';
				}
			});
		});

		// Loading state on form submission
		document.querySelectorAll('[data-auth-form]').forEach(form => {
			form.addEventListener('submit', () => {
				const submitBtn = form.querySelector('button[type="submit"]');
				if (submitBtn) {
					submitBtn.classList.add('btn-loading');
					// We don't disable here to allow the browser to submit the form, 
					// but the class handles the visual part.
				}
			});
		});
	};

	setupAuthInteractions();
})();
