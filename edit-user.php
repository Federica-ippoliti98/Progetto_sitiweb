<?php


require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/classes/User.php';

//verifica che sono admin -> funzione
requireAdmin();
$user = currentUser(); // mi dice chi sono se admin o client


$errors = [];
$success = '';

include 'header_dashboard.php';
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

<!-- WRAPPER RESPONSIVE -->
<div class="d-flex dashboard-wrapper">

    <!-- SIDEBAR -->
    <nav class="sidebar d-flex flex-column flex-shrink-0 position-fixed">

        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-chevron-left"></i>
        </button>

        <div class="p-4">
            <a class="hide-on-collapse text-decoration-none text-white"
               href="index.php">

                <i class="fa-solid fa-earth-africa"></i>
                Orbita Web
            </a>
        </div>

        <div class="nav flex-column">

            <!-- DASHBOARD -->
            <a href="dashboard.php"
               class="sidebar-link  text-decoration-none p-3">

                <i class="fa-solid fa-house me-3"></i>
                <span class="hide-on-collapse">Dashboard</span>
            </a>

            <!-- ANALYTICS -->
            <a href="dashboard.php#analytics"
               class="sidebar-link text-decoration-none p-3 ">

                <i class="fa-solid fa-chart-bar me-3"></i>
                <span class="hide-on-collapse">Analytics</span>
            </a>

            <!--  EDIT USER (ADMIN ONLY) -->
            <a href="#"
               class="active-dash sidebar-link text-decoration-none p-3 ">

                <i class="fas fa-user-edit me-3"></i>
                <span class="hide-on-collapse">Edit User</span>
            </a>



            <!-- MANAGE PRODUCTS -->
            <a href="dashboard.php#manageProduct"
               class="sidebar-link text-decoration-none p-3 ">

                <i class="fa-solid fa-box me-3"></i>
                <span class="hide-on-collapse">Manage products</span>
            </a>



            <!-- MANAGE CUSTOM -->
            <a href="dashboard.php#manageCustom"
               class="sidebar-link text-decoration-none p-3 ">

                <i class="fa-solid fa-pen me-3"></i>
                <span class="hide-on-collapse">Manage customizations</span>
            </a>

            <!-- BOOKING -->
            <a href="dashboard.php#booking"
               class="sidebar-link text-decoration-none p-3 ">

                <i class="fa-regular fa-calendar me-3"></i>
                <span class="hide-on-collapse">Booking</span>
            </a>

        </div>

        <!-- PROFILE -->
        <div class="profile-section mt-auto p-4">

            <div class="d-flex align-items-center">

                <div class="ms-3 profile-info">

                    <h6 class="text-white mb-0 pt-3">
                        <?= htmlspecialchars($user['name'] ?? 'Utente') ?>
                    </h6>

                    <small class="text-muted">
                        <?= htmlspecialchars($user['role'] ?? 'client') ?>
                    </small>

                    <p class="text-white mt-2">
                        Vuoi uscire?
                        <a href="logout.php" class="text-dash">Logout</a>
                    </p>

                </div>

            </div>

        </div>

    </nav>

    <!-- MAIN -->
    <main class="main-content w-100" style="background-color: white;">

        <div class="container-fluid">

            <!-- EDIT USER -->
            <section id="Edituser">
                <?php




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

                        $updated = User::updateUser(
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



            </section>

            

        </div>

    </main>

</div>

