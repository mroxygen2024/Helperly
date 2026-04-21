<?php

declare(strict_types=1);

/*
 |--------------------------------------------------------------------------
 | controllers/ purpose
 |--------------------------------------------------------------------------
 | Handle HTTP input, coordinate models, then choose the view to render.
 */

class MarketplaceController
{
    private Listing $listings;

    public function __construct()
    {
        $this->listings = new Listing();
    }

    public function index(): void
    {
        $this->listings->seedIfEmpty();

        renderView('marketplace/index', [
            'title' => 'Marketplace',
            'listings' => $this->listings->getLatest(20),
            'user' => authUser(),
        ]);
    }

    public function employerDashboard(): void
    {
        requireRole('parent');

        renderView('marketplace/index', [
            'title' => 'Employer Dashboard',
            'listings' => $this->listings->getLatest(20),
            'user' => authUser(),
        ]);
    }

    public function servantDashboard(): void
    {
        requireRole('service_provider');

        renderView('marketplace/index', [
            'title' => 'Servant Dashboard',
            'listings' => $this->listings->getLatest(20),
            'user' => authUser(),
        ]);
    }

    public function adminDashboard(): void
    {
        requireRole('administrator');

        renderView('marketplace/index', [
            'title' => 'Administrator Dashboard',
            'listings' => $this->listings->getLatest(20),
            'user' => authUser(),
        ]);
    }
}
