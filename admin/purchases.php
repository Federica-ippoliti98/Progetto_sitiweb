<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../classes/Purchase.php';

requireAdmin();
$user = currentUser();

$errors = [];
$success = '';

/*
|--------------------------------------------------------------------------
| REFUND PURCHASE
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'refund'
) {
    $purchaseId = (int) ($_POST['purchase_id'] ?? 0);

    if (Purchase::setRefunded($purchaseId)) {
        $success = "Purchase #{$purchaseId} refunded successfully.";
    } else {
        $errors[] = "Unable to refund purchase #{$purchaseId}.";
    }
}

/*
|--------------------------------------------------------------------------
| LOAD DATA
|--------------------------------------------------------------------------
*/
$allPurchases = Purchase::findAll();

$totalCount = Purchase::countAll();
$totalRevenue = Purchase::totalRevenue();

include __DIR__ . '/../header_dashboard.php';
?>

<!-- MAIN -->
<main class="dashboard-content">

    <header class="dashboard-header mb-4">
        <h2 class="text-dash fw-bold mb-1">Purchases Management</h2>
        <p class="text-white-50 mb-0">
            All transactions in the system.
        </p>
    </header>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
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

    <!-- STATS -->
    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>

                <div>
                    <div class="stat-value">
                        <?= $totalCount ?>
                    </div>

                    <div class="stat-label">
                        Total Purchases
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>

                <div>
                    <div class="stat-value">
                        €<?= number_format($totalRevenue, 2) ?>
                    </div>

                    <div class="stat-label">
                        Total Revenue
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- PURCHASES TABLE -->
    <div class="dashboard-panel">

        <h4 class="text-dash fw-bold mb-3">
            <i class="fas fa-list me-2"></i>
            All Transactions
        </h4>

        <div class="table-responsive">

            <table class="table table-light text-dark align-middle mb-0">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Reference</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($allPurchases)): ?>

                        <?php foreach ($allPurchases as $pu): ?>

                            <tr>

                                <td>
                                    #<?= (int) $pu['id'] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($pu['user_name']) ?>

                                    <br>

                                    <small class="text-dark-50">
                                        <?= htmlspecialchars($pu['user_email']) ?>
                                    </small>
                                </td>

                                <td>
                                    <?= htmlspecialchars($pu['product_name']) ?>
                                </td>

                                <td>
                                    €<?= number_format((float) $pu['total_price_paid'], 2) ?>
                                </td>

                                <td>
                                    <span class="active-dash badge text-white badge-<?= htmlspecialchars($pu['status']) ?>">
                                        <?= htmlspecialchars($pu['status']) ?>
                                    </span>
                                </td>

                                <td>
                                    <small class="text-dark-50">
                                        <?= htmlspecialchars($pu['payment_ref'] ?? '—') ?>
                                    </small>
                                </td>

                                <td>
                                    <?= date('d M Y, H:i', strtotime($pu['created_at'])) ?>
                                </td>

                                <td class="text-end">

                                    <?php if (in_array($pu['status'], ['paid', 'completed'])): ?>

                                        <form method="post" class="d-inline">

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="refund">

                                            <input
                                                type="hidden"
                                                name="purchase_id"
                                                value="<?= (int) $pu['id'] ?>">

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-dash text-white"
                                                title="Refund purchase"
                                                onclick="return confirm('Refund purchase #<?= (int) $pu['id'] ?>?');">

                                                <i class="fas fa-undo"></i>

                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <span class="text-dark-50 small">
                                            —
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="text-center text-dark-50">
                                No purchases found.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>