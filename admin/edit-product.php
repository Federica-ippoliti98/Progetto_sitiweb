<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Product.php';

requireAdmin();
$user = currentUser();

$errors = [];
$success = '';

// ---------------------- RECUPERO PRODOTTO ----------------------

$targetId = (int) ($_GET['id'] ?? 0);
$target = Product::findById($targetId);

if (!$target) {
    header('Location: dashboard.php#manageProducts');
    exit;
}

// ---------------------- UPDATE PRODUCT ----------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;

    $file_path   = trim($_POST['file_path'] ?? '');
    $image_path  = trim($_POST['image_path'] ?? '');

    // ---------------- VALIDAZIONE ----------------

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($description === '') {
        $errors[] = 'Description is required.';
    }

    if ($price <= 0) {
        $errors[] = 'Price must be greater than 0.';
    }

    // ---------------- UPDATE ----------------

    if (empty($errors)) {

        $updated = Product::update(
            $targetId,
            $name,
            $description,
            $price,
            $is_active,
            $file_path,
            $image_path
        );

        if ($updated) {

            $success = 'Product updated successfully!';

            // 🔁 ricarica dati aggiornati (come edit-user)
            $target = Product::findById($targetId);

        } else {
            $errors[] = 'Unable to update product.';
        }
    }
}

include __DIR__ . '/../header_dashboard.php';
?>


<div class="d-flex dashboard-wrapper">

    <!-- SIDEBAR -->
    <nav class="sidebar d-flex flex-column flex-shrink-0 position-fixed">

        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-chevron-left"></i>
        </button>

        <div class="p-4">
            <a class="hide-on-collapse text-decoration-none text-white" href="index.php">
                <i class="fa-solid fa-earth-africa"></i>
                Orbita Web
            </a>
        </div>

        <div class="nav flex-column">

            <a href="dashboard.php" class="sidebar-link text-decoration-none p-3">
                <i class="fa-solid fa-house me-3"></i>
                <span class="hide-on-collapse">Dashboard</span>
            </a>

            <a href="dashboard.php#analytics" class="sidebar-link text-decoration-none p-3">
                <i class="fa-solid fa-chart-bar me-3"></i>
                <span class="hide-on-collapse">Analytics</span>
            </a>

            <a href="dashboard.php#client" class="sidebar-link text-decoration-none p-3">
                <i class="fas fa-users me-3"></i>
                <span class="hide-on-collapse">Client</span>
            </a>

            <a href="#" class="active-dash sidebar-link text-decoration-none p-3">
                <i class="fa-solid fa-boxes-packing me-3"></i>
                <span class="hide-on-collapse">Edit product</span>
            </a>

            <a href="dashboard.php#manageCustom" class="sidebar-link text-decoration-none p-3">
                <i class="fa-solid fa-pen me-3"></i>
                <span class="hide-on-collapse">Manage customizations</span>
            </a>

            <a href="dashboard.php#booking" class="sidebar-link text-decoration-none p-3">
                <i class="fa-regular fa-calendar me-3"></i>
                <span class="hide-on-collapse">Booking</span>
            </a>

        </div>

        <div class="profile-section mt-auto p-4">

            <h6 class="text-white mb-0 pt-3">
                <?= htmlspecialchars($user['name']) ?>
            </h6>

            <small class="text-muted">
                <?= htmlspecialchars($user['role']) ?>
            </small>

        </div>

    </nav>

    <!-- MAIN -->
    <main class="main-content w-100" style="background-color: white;">

        <div class="container-fluid">

            <section class="dashboard-content">

                <header class="dashboard-header mb-4">

                    <h2 class="fw-bold text-dash">
                        Edit Product #<?= (int)$target['id'] ?>
                    </h2>

                    <a href="dashboard.php#manageProducts" class="text-dash text-decoration-none">
                        <i class="fas fa-arrow-left"></i> Back to products
                    </a>

                </header>

                <!-- ERRORI -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- SUCCESSO -->
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <div class="dashboard-panel">

                    <form method="post">

                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name"
                                   class="form-control"
                                   value="<?= htmlspecialchars($target['name']) ?>">
                        </div>

                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description"
                                      class="form-control"><?= htmlspecialchars($target['description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price"
                                   class="form-control"
                                   value="<?= htmlspecialchars($target['price']) ?>">
                        </div>

                        <div class="mb-3">
                            <label>Image path</label>
                            <input type="text" name="image_path"
                                   class="form-control"
                                   value="<?= htmlspecialchars($target['image_path']) ?>">
                        </div>

                        <div class="mb-3">
                            <label>File path</label>
                            <input type="text" name="file_path"
                                   class="form-control"
                                   value="<?= htmlspecialchars($target['file_path']) ?>">
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active"
                                   class="form-check-input"
                                   <?= $target['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label">Active</label>
                        </div>

                        <button type="submit" class="btn btn-dash text-white w-100">
                            <i class="fas fa-save"></i> Save Changes
                        </button>

                    </form>

                </div>

            </section>

        </div>

    </main>

</div>