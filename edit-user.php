
<?php

require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/classes/User.php';

requireAdmin();

$user = currentUser();

$errors = [];
$success = '';


//---------------------------------------------------RECUPERO UTENTE

$targetId = (int) ($_GET['id'] ?? 0);
$target = User::findById($targetId);

if (!$target) {
    header('Location: users.php');
    exit;
}


//---------------------------------------------------UPDATE USER

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role  = $_POST['role'] ?? $target['role'];

    // Validazioni

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }

    if (!in_array($role, ['client', 'admin'], true)) {
        $errors[] = 'Invalid role.';
    }

    // Non può modificare il proprio ruolo
    if ($targetId === $user['id']) {
        $role = $target['role'];
    }

    if (empty($errors)) {

        $updated = User::update(
            $targetId,
            $name,
            $email,
            $role
        );

        if ($updated) {

            $success = 'User updated successfully!';

            $target = User::findById($targetId);

        } else {

            $errors[] = 'Unable to update user.';
        }
    }
}

include 'header_dashboard.php';
?>

<main class="dashboard-content">

    <header class="dashboard-header mb-4">

        <h2 class="text-dash fw-bold mb-1">
            Edit User #<?= (int) $target['id'] ?>
        </h2>

        <p class="text-50 mb-0">
            Update user information.
        </p>

        <a href="dashboard.php#client"
           class="text-dash text-decoration-none mt-3 d-inline-block">
            <i class="fas fa-arrow-left"></i>
            Back to List
        </a>

    </header>


    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>


    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>


    <div class="row g-4">

        <div class="col-lg-6">

            <div class="dashboard-panel">

                <h4 class="text-dash fw-bold mb-3">
                    <i class="fas fa-user-edit"></i>
                    Edit User
                </h4>

                <form method="post">

                    <div class="mb-3">

                        <label for="name" class="form-label">
                            Name
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            value="<?= htmlspecialchars($target['name']) ?>"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label for="email" class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($target['email']) ?>"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label for="role" class="form-label">
                            Role
                        </label>

                        <select
                            id="role"
                            name="role"
                            class="form-select"
                            <?= $targetId === $user['id'] ? 'disabled' : '' ?>
                        >

                            <option value="client"
                                <?= $target['role'] === 'client' ? 'selected' : '' ?>>
                                Client
                            </option>

                            <option value="admin"
                                <?= $target['role'] === 'admin' ? 'selected' : '' ?>>
                                Admin
                            </option>

                        </select>

                        <?php if ($targetId === $user['id']): ?>

                            <small class="text-50">
                                You can't change your own role.
                            </small>

                            <input
                                type="hidden"
                                name="role"
                                value="<?= htmlspecialchars($target['role']) ?>"
                            >

                        <?php endif; ?>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-dash text-white w-100">

                        <i class="fas fa-save"></i>
                        Save Changes

                    </button>

                </form>

            </div>

        </div>

    </div>

</main>



