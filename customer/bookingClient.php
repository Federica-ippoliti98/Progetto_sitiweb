<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php'; // path aggiornato dopo riorganizzazione cartelle
require_once __DIR__ . '/../classes/Booking.php';

//chi può accedere: chiunque sia loggato (client o admin)
requireLogin();
$user = currentUser();

//salvo errori o success
$errors  = [];
$success = '';


//---------------------------------------------------CREA RICHIESTA-------------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {

    $date  = $_POST['scheduled_date'] ?? '';
    $time  = $_POST['scheduled_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    //---------- Validazione data
    if ($date === '') {
        $errors[] = 'Date is required.';
    } elseif ($date < date('Y-m-d')) {
        $errors[] = 'Date cannot be in the past.';
    }

    //---------- Validazione orario (deve essere uno della lista)
    if (!in_array($time, Booking::AVAILABLE_TIMES, true)) {
        $errors[] = 'Please select a valid time slot.';
    }

    //---------- Note opzionali, ma limitate in lunghezza
    if (strlen($notes) > 500) {
        $errors[] = 'Notes too long (max 500 characters).';
    }

    //creazione se nessun errore
    if (empty($errors)) {
        $newId = Booking::create($user['id'], $date, $time, $notes ?: null);
        if ($newId) {
            $success = "Request sent! The admin will review it soon.";
            $_POST = [];
        } else {
            $errors[] = "Something went wrong, please try again.";
        }
    }
}


//---------------------------------------------------CANCELLA RICHIESTA (solo pending, solo proprie)----

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel') {

    $bookingId = (int) ($_POST['booking_id'] ?? 0);

    if (Booking::deleteIfOwnedAndPending($bookingId, $user['id'])) {
        $success = "Request cancelled.";
    } else {
        $errors[] = "Cannot cancel this request (already processed?).";
    }
}


//recupero le booking dell'utente loggato
$myBookings = Booking::findByUserId($user['id']);

//header dashboard
include '/../header_dashboard.php';
?>




    <!-- MAIN -->
    <main class="dashboard-content">

        <header class="dashboard-header mb-4">
            <h2 class="text-dashfw-bold mb-1">Book a mentoring session</h2>
            <p class="text-dark-50 mb-0">Pick a date and time, the admin will confirm your request.</p>
        </header>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
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


        <div class="row g-4">

            <!-- FORM NUOVA RICHIESTA -->
            <div class="col-lg-4">
                <div class="dashboard-panel">
                    <h4 class="text-dash fw-bold mb-3">
                        <i class="fas fa-plus"></i> New request
                    </h4>

                    <form action="" method="post">

                        <input type="hidden" name="action" value="create">

                        <div class="mb-3">
                            <label for="scheduled_date" class="form-label text-dark">Date</label>
                            <input type="date"
                                   class="form-control"
                                   id="scheduled_date"
                                   name="scheduled_date"
                                   min="<?= date('Y-m-d') ?>"
                                   value="<?= htmlspecialchars($_POST['scheduled_date'] ?? '') ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="scheduled_time" class="form-label text-dark">Time</label>
                            <select class="form-select"
                                    id="scheduled_time"
                                    name="scheduled_time"
                                    required>
                                <option value="">-- choose a slot --</option>
                                <?php foreach (Booking::AVAILABLE_TIMES as $slot): ?>
                                    <option value="<?= $slot ?>"
                                        <?= ($_POST['scheduled_time'] ?? '') === $slot ? 'selected' : '' ?>>
                                        <?= $slot ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label text-dark">
                                Notes <small class="text-white-50">(optional)</small>
                            </label>
                            <textarea class="form-control"
                                      id="notes"
                                      name="notes"
                                      rows="3"
                                      maxlength="500"
                                      placeholder="Topics you want to cover..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-dash text-white w-100">
                            <i class="fas fa-paper-plane"></i> Send request
                        </button>

                    </form>
                </div>
            </div>


            <!-- LISTA MIE RICHIESTE -->
            <div class="col-lg-8">
                <div class="dashboard-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-dash fw-bold mb-0">
                            <i class="fas fa-history"></i> My requests
                        </h4>
                        <span class="badge active-dash text-white">
                            <?= count($myBookings) ?> total
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-light text-dark mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Notes</th>
                                    <th>Status</th>
                                    <th>Admin note</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($myBookings as $b): ?>
                                    <tr>
                                        <td>#<?= (int) $b['id'] ?></td>
                                        <td><?= date('d M Y', strtotime($b['scheduled_date'])) ?></td>
                                        <td><?= substr($b['scheduled_time'], 0, 5) ?></td>
                                        <td>
                                            <?php if (!empty($b['notes'])): ?>
                                                <small><?= htmlspecialchars($b['notes']) ?></small>
                                            <?php else: ?>
                                                <small class="text-dark">—</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class=" text-dark badge badge-status badge-<?= htmlspecialchars($b['status']) ?>">
                                                <?= htmlspecialchars($b['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($b['admin_note'])): ?>
                                                <small class="text-dark">
                                                    <?= htmlspecialchars($b['admin_note']) ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-dark">—</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">

                                            <!-- Solo se è ancora pending il cliente può cancellare -->
                                            <?php if ($b['status'] === 'pending'): ?>
                                                <form action="" method="post" class="d-inline">
                                                    <input type="hidden" name="action"     value="cancel">
                                                    <input type="hidden" name="booking_id" value="<?= (int) $b['id'] ?>">
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Cancel request"
                                                            onclick="return confirm('Cancel this request?');">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-dark-50 small">—</span>
                                            <?php endif; ?>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($myBookings)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-dark-50">
                                            No requests yet. Make your first one!
                                        </td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </main>




