<?php

require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Booking.php';

$user = currentUser();

$errors = [];
$success = '';

/*
|--------------------------------------------------------------------------
| LOAD SINGLE BOOKING (fallback sicuro)
|--------------------------------------------------------------------------
| Non avendo findAll() o findByUser(), non possiamo caricare liste.
| Quindi per ora lasciamo vuoto o logica futura.
*/
$bookings = [];

/*
|--------------------------------------------------------------------------
| RENDER
|--------------------------------------------------------------------------
*/
include 'header_dashboard.php';
?>

<main class="dashboard-content">

    <header class="dashboard-header mb-4">
        <h2 class="text-warning fw-bold mb-1">Booking Management</h2>
        <p class="text-white-50 mb-0">Manage your reservations.</p>
    </header>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <div class="col-12">

            <div class="dashboard-panel">

                <h4 class="text-warning fw-bold mb-3">
                    <i class="fas fa-calendar"></i> Bookings
                </h4>

                <div class="alert alert-secondary">
                    Nessun metodo disponibile per caricare la lista prenotazioni.
                    (Manca findAll o findByUser nel model Booking)
                </div>

            </div>

        </div>

    </div>

</main>