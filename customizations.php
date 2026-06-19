<?php

require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/classes/Customization.php';

requireAdmin();

$user = currentUser();

$errors = [];
$success = '';


// -------------------------------- CREATE CUSTOMIZATION --------------------------------

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'create_customization'
) {

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $is_active = isset($_POST['is_active']);

    if ($name === '') {
        $errors[] = 'Name is required';
    }

    if ($price <= 0) {
        $errors[] = 'Price must be greater than zero';
    }

    if (empty($errors)) {

        Customization::createCustom(
            $name,
            $description,
            $price,
            $is_active
        );

        $success = 'Customization created successfully';

        $_POST = [];
    }
}


// -------------------------------- DELETE CUSTOMIZATION --------------------------------

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'delete_customization'
) {

    $customizationId = (int) ($_POST['customization_id'] ?? 0);

    if (Customization::deleteCustom($customizationId)) {

        $success = 'Customization deleted successfully';

    } else {

        $errors[] = 'Failed to delete customization';
    }
}


// -------------------------------- TOGGLE STATUS --------------------------------

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'toggle_customization'
) {

    $customizationId = (int) ($_POST['customization_id'] ?? 0);

    if (Customization::toggleActiveCustom($customizationId)) {

        $success = 'Customization status updated';

    } else {

        $errors[] = 'Failed to update customization status';
    }
}


$customizations = Customization::findAllCustom();
?>

<main class="dashboard-content">

    <header class="dashboard-header mb-4">

        <h2 class="text-dash fw-bold mb-1">
            Customizations Management
        </h2>

        <p class="text-50 mb-0">
            Create and manage customizations.
        </p>

    </header>

    <?php if (!empty($success)): ?>

        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>

    <?php endif; ?>

    <?php if (!empty($errors)): ?>

        <div class="alert alert-danger">

            <?php foreach ($errors as $error): ?>

                <div><?= htmlspecialchars($error) ?></div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <div class="row g-4">

        <!-- FORM -->

        <div class="col-lg-4">

            <div class="dashboard-panel">

                <h4 class="text-dash fw-bold mb-3">

                    <i class="fas fa-sliders-h"></i>

                    New Customization

                </h4>

                <form method="post">

                    <input
                        type="hidden"
                        name="action"
                        value="create_customization">

                    <div class="mb-3">

                        <label class="form-label">
                            Name
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="name"
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea
                            class="form-control"
                            name="description"
                            rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Price (€)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control"
                            name="price"
                            value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
                            required>

                    </div>

                    <div class="form-check mb-3">

                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="custom_is_active"
                            name="is_active"
                            <?= isset($_POST['is_active']) || !$_POST ? 'checked' : '' ?>>

                        <label
                            class="form-check-label"
                            for="custom_is_active">

                            Active

                        </label>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-dash w-100 text-white">

                        <i class="fas fa-plus"></i>

                        Create Customization

                    </button>

                </form>

            </div>

        </div>

        <!-- TABLE -->

        <div class="col-lg-8">

            <div class="dashboard-panel">

                <h4 class="text-dash fw-bold mb-3">

                    <i class="fas fa-list"></i>

                    All Customizations

                </h4>

                <div class="table-responsive">

                    <table class="table table-dash table-light text-dark">

                        <thead>

                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($customizations as $customization): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($customization['name']) ?>
                                    </td>

                                    <td>
                                        € <?= number_format($customization['price'], 2) ?>
                                    </td>

                                    <td>

                                        <?php if ($customization['is_active']): ?>

                                            <span class="badge bg-success">
                                                Active
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-secondary">
                                                Inactive
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="text-end">

                                        <a
                                            href="edit-customization.php?id=<?= (int)$customization['id'] ?>"
                                            class="btn btn-sm btn-dash">

                                            <i class="fas fa-edit text-white"></i>

                                        </a>

                                        <form
                                            method="post"
                                            class="d-inline">

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="toggle_customization">

                                            <input
                                                type="hidden"
                                                name="customization_id"
                                                value="<?= (int)$customization['id'] ?>">

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-dash"
                                                onclick="return confirm('Change customization status?')">

                                                <i class="fas fa-power-off text-white"></i>

                                            </button>

                                        </form>

                                        <form
                                            method="post"
                                            class="d-inline">

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="delete_customization">

                                            <input
                                                type="hidden"
                                                name="customization_id"
                                                value="<?= (int)$customization['id'] ?>">

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-dash"
                                                onclick="return confirm('Delete <?= htmlspecialchars($customization['name'], ENT_QUOTES) ?>?')">

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