<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/ purpose
 |--------------------------------------------------------------------------
 | Handle HTTP input, coordinate models, then choose the view to render.
 */

class ServiceController
{
    private Service $services;

    public function __construct()
    {
        $this->services = new Service();
    }

    public function showForm(): void
    {
        requireRole('service_provider');

        $userId = (string) ($_SESSION['user_id'] ?? '');
        if ($userId === '') {
            setFlash('error', 'User session is invalid. Please login again.');
            redirect('/login');
        }

        $myServices = $this->services->getServicesByProvider($userId);

        renderView('services/index', [
            'title'      => 'My Services',
            'csrfToken'  => csrfToken(),
            'myServices' => $myServices,
        ]);
    }

    public function create(array $payload): void
    {
        requireRole('service_provider');

        if (!verifyCsrfToken($payload['csrf_token'] ?? null)) {
            setFlash('error', 'Invalid request token. Please try again.');
            redirect('/services');
        }

        $providerId   = (string) ($_SESSION['user_id'] ?? '');
        $serviceType  = sanitizeInput($payload['service_type']  ?? null);
        $description  = sanitizeInput($payload['description']   ?? null);
        $price        = sanitizeInput($payload['price']         ?? null);
        $availability = sanitizeInput($payload['availability']  ?? null);

        $errors = [];

        if (!validateRequired($serviceType)) {
            $errors[] = 'Service type is required.';
        }
        if (!validateRequired($description)) {
            $errors[] = 'Description is required.';
        }
        if (!validateRequired($price)) {
            $errors[] = 'Price is required.';
        }
        if (!validateRequired($availability)) {
            $errors[] = 'Availability is required.';
        }

        if (!empty($errors)) {
            rememberOldInput([
                'service_type'  => $serviceType,
                'description'   => $description,
                'price'         => $price,
                'availability'  => $availability,
            ]);
            setFlash('error', implode(' ', $errors));
            redirect('/services');
        }

        try {
            $created = $this->services->createService(
                $providerId,
                $serviceType,
                $description,
                $price,
                $availability
            );

            if ($created) {
                clearOldInput();
                setFlash('success', 'Service offering posted successfully.');
            } else {
                setFlash('error', 'Service could not be saved. Please try again.');
            }
        } catch (InvalidArgumentException $exception) {
            rememberOldInput([
                'service_type'  => $serviceType,
                'description'   => $description,
                'price'         => $price,
                'availability'  => $availability,
            ]);
            setFlash('error', $exception->getMessage());
        } catch (Throwable $exception) {
            error_log('Service creation failed: ' . $exception->getMessage());
            setFlash('error', 'Could not save service right now. Please try again.');
        }

        redirect('/services');
    }
}
