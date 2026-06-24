<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Purchase.php';

$myPurchases = Purchase::findByUserId($user['id'] ?? 0);

?>

<header class="dashboard-header mb-4">
    <h2 class="text-dash fw-bold mb-1">My purchases</h2>
    <p class="text-dark-50 mb-0">All the software you have bought.</p>
</header>

<div class="dashboard-panel">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-dash fw-bold mb-0">
            <i class="fas fa-receipt"></i> History
        </h4>
        <span class="badge active-dash text-white">
            <?= count($myPurchases) ?> total
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-light text-dark mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Reference</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($myPurchases as $pu): ?>
                    <tr>
                        <td>#<?= (int) $pu['id'] ?></td>
                        <td>
                            <?= htmlspecialchars($pu['product_name']) ?>
                            <br>
                            <small class="text-dark-50">
                                <?= htmlspecialchars(mb_strimwidth($pu['product_description'], 0, 60, '...')) ?>
                            </small>
                        </td>
                        <td>€<?= number_format((float) $pu['total_price_paid'], 2) ?></td>
                        <td>
                            <span class="badge active-dash text-white badge-<?= htmlspecialchars($pu['status']) ?>">
                                <?= htmlspecialchars($pu['status']) ?>
                            </span>
                        </td>
                        <td><small class="text-dark-50"><?= htmlspecialchars($pu['payment_ref'] ?? '—') ?></small></td>
                        <td><?= date('d M Y, H:i', strtotime($pu['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($myPurchases)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-dark-50">
                            No purchases yet.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>