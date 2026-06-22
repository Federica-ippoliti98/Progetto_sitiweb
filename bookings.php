<?php

require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Booking.php';

// verifica admin
requireAdmin();

$user = currentUser();

$errors  = [];
$success = '';


//---------------------------------------------------UPDATE STATUS-------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {

    $bookingId = (int) ($_POST['booking_id'] ?? 0);
    $newStatus = $_POST['new_status'] ?? '';

    if (Booking::updateStatus($bookingId, $newStatus)) {
        $success = "Booking updated to '{$newStatus}'.";
    } else {
        $errors[] = "Failed to update booking.";
    }
}


//---------------------------------------------------DELETE BOOKING-------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {

    $targetId = (int) ($_POST['booking_id'] ?? 0);

    if (Booking::deleteBooking($targetId)) {
        $success = "Booking deleted!";
    } else {
        $errors[] = "Failed to delete booking.";
    }
}


// recupero booking
$allBookings = Booking::findAll();


// conteggi aggiornati
$countPending   = Booking::countByStatus('pending');
$countCompleted = Booking::countByStatus('completed');
$countCancelled = Booking::countByStatus('cancelled');


// header
include 'header_dashboard.php';

?>


<!-- MAIN CONTENT -->
<main class="dashboard-content">

    <header class="dashboard-header mb-4">
        <h2 class="text-dash fw-bold mb-1">Booking Management</h2>
        <p class="text-white-50 mb-0">Review and manage booking requests.</p>
    </header>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>


    <!-- STAT CARDS -->
    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="stat-value"><?= $countPending ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <div class="stat-value"><?= $countCompleted ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-ban"></i>
                </div>
                <div>
                    <div class="stat-value"><?= $countCancelled ?></div>
                    <div class="stat-label">Cancelled</div>
                </div>
            </div>
        </div>

    </div>


    <!-- TABLE -->
    <div class="dashboard-panel">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-dash fw-bold mb-0">
                <i class="fas fa-calendar-alt"></i> All Bookings
            </h4>
            <span class="badge active-dash ">
                <?= count($allBookings) ?> total
            </span>
        </div>

        <div class="table-responsive">

            <table class="table table-light mb-0 text-dark">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Created at</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($allBookings as $b): ?>

                        <tr>
                            <td>#<?= (int) $b['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($b['user_name']) ?>
                                <br>
                                <small class="text-white-50">
                                    <?= htmlspecialchars($b['user_email']) ?>
                                </small>
                            </td>

                            <td><?= date('d M Y', strtotime($b['scheduled_date'])) ?></td>
                            <td><?= substr($b['scheduled_time'], 0, 5) ?></td>

                            <td>
                                <?php if (!empty($b['notes'])): ?>
                                    <small><?= htmlspecialchars($b['notes']) ?></small>
                                <?php else: ?>
                                    <small class="text-white-50">—</small>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="badge badge-status badge-<?= htmlspecialchars($b['status']) ?>">
                                    <?= htmlspecialchars($b['status']) ?>
                                </span>
                            </td>

                            <td><?= date('d M Y', strtotime($b['created_at'])) ?></td>

                            <td class="text-end">

                                <?php if ($b['status'] === 'pending'): ?>

                                    <!-- COMPLETE -->
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="booking_id" value="<?= (int) $b['id'] ?>">
                                        <input type="hidden" name="new_status" value="completed">

                                        <button type="submit"
                                                class="btn btn-sm btn-dash text-white"
                                                title="Mark as completed">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>

                                    <!-- CANCEL -->
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="booking_id" value="<?= (int) $b['id'] ?>">
                                        <input type="hidden" name="new_status" value="cancelled">

                                        <button type="submit"
                                                class="btn btn-sm btn-dash text-white"
                                                title="Cancel booking">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>

                                <?php endif; ?>

                                <!-- DELETE -->
                                <form action="" method="post" class="d-inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="booking_id" value="<?= (int) $b['id'] ?>">

                                    <button type="submit"
                                            class="btn btn-sm btn-dash text-white"
                                            title="Delete booking"
                                            onclick="return confirm('Delete booking from <?= htmlspecialchars($b['user_name'], ENT_QUOTES) ?>?');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>

                    <?php endforeach; ?>

                    <?php if (empty($allBookings)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-white-50">
                                No bookings yet.
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>

</main>

