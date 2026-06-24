<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../classes/Product.php';

requireAdmin();

$user = currentUser();

$errors = [];
$success = '';


// -------------------------------- CREATE PRODUCT --------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $is_active = isset($_POST['is_active']);

    $file_path = trim($_POST['file_path'] ?? '');
    $image_path = trim($_POST['image_path'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required';
    }

    if ($price <= 0) {
        $errors[] = 'Price must be greater than zero';
    }

    if (empty($errors)) {

        Product::create(
            $name,
            $description,
            $price,
            $is_active,
            $file_path ?: null,
            $image_path ?: null
        );

        $success = 'Product created successfully';

        $_POST = [];
    }
}


// -------------------------------- DELETE PRODUCT --------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {

    $productId = (int) ($_POST['product_id'] ?? 0);

    if (Product::delete($productId)) {

        $success = 'Product deleted successfully';

    } else {

        $errors[] = 'Failed to delete product';
    }
}


// -------------------------------- TOGGLE STATUS --------------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_active') {

    $productId = (int) ($_POST['product_id'] ?? 0);

    if (Product::toggleActive($productId)) {

        $success = 'Product status updated';

    } else {

        $errors[] = 'Failed to update product status';
    }
}


$products = Product::findAll();

include __DIR__ . '/../header_dashboard.php';
?>

<main class="dashboard-content">

    <header class="dashboard-header mb-4">
        <h2 class="text-dash fw-bold mb-1">
            Product Management
        </h2>
        <p class="text-50 mb-0">
            Create and manage products.
        </p>
    </header>

    <div class="row g-4">

        <!-- FORM -->

        <div class="col-lg-4">
            <div class="dashboard-panel">

                <h4 class="text-dash fw-bold mb-3">
                    <i class="fas fa-box"></i> New Product
                </h4>

                <form method="post">

                    <input type="hidden" name="action" value="create">

                    <div class="mb-3">
                        <label class="form-label">Name</label>

                        <input
                            type="text"
                            class="form-control"
                            name="name"
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>

                        <textarea
                            class="form-control"
                            name="description"
                            rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price (€)</label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control"
                            name="price"
                            value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File Path</label>

                        <input
                            type="text"
                            class="form-control"
                            name="file_path"
                            value="<?= htmlspecialchars($_POST['file_path'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image Path</label>

                        <input
                            type="text"
                            class="form-control"
                            name="image_path"
                            value="<?= htmlspecialchars($_POST['image_path'] ?? '') ?>">
                    </div>

                    <div class="form-check mb-3">

                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="is_active"
                            name="is_active"
                            <?= isset($_POST['is_active']) || !$_POST ? 'checked' : '' ?>>

                        <label class="form-check-label" for="is_active">
                            Active
                        </label>

                    </div>

                    <button type="submit" class="btn btn-dash w-100 text-white">
                        <i class="fas fa-plus"></i>
                        Create Product
                    </button>

                </form>

            </div>
        </div>


        <!-- TABLE -->

        <div class="col-lg-8">
            <div class="dashboard-panel">

                <h4 class="text-dash fw-bold mb-3">
                    <i class="fas fa-boxes"></i> All Products
                </h4>

                <div class="table-responsive">

                    <table class="table table-dash table-light text-dark">

                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($products as $product): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($product['name']) ?>
                                    </td>

                                    <td>
                                        € <?= number_format($product['price'], 2) ?>
                                    </td>

                                    <td>

                                        <?php if ($product['is_active']): ?>

                                            <span class="badge bg-success">
                                                Active
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-secondary">
                                                Inactive
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>
                                        <?= date('d M Y', strtotime($product['created_at'])) ?>
                                    </td>

                                    <td class="text-end">

                                        <!-- EDIT -->

                                        <a href="edit-product.php?id=<?= (int) $product['id'] ?>"
                                           class="btn btn-sm btn-dash">

                                            <i class="fas fa-edit text-white"></i>

                                        </a>

                                        <!-- TOGGLE -->

                                        <form method="post" class="d-inline">

                                            <input type="hidden" name="action" value="toggle_active">

                                            <input
                                                type="hidden"
                                                name="product_id"
                                                value="<?= (int) $product['id'] ?>">

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-dash"
                                                onclick="return confirm('Change product status?')">

                                                <i class="fas fa-power-off text-white"></i>

                                            </button>

                                        </form>

                                        <!-- DELETE -->

                                        <form method="post" class="d-inline">

                                            <input type="hidden" name="action" value="delete">

                                            <input
                                                type="hidden"
                                                name="product_id"
                                                value="<?= (int) $product['id'] ?>">

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-dash"
                                                onclick="return confirm('Delete <?= htmlspecialchars($product['name'], ENT_QUOTES) ?>?')">

                                                <i class="fas fa-trash text-white"></i>

                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

    </div>

</main>