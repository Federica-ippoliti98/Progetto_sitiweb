<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap-icons/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">

    <title>Orbita Web</title>
</head>

<body class="bg_personal">

<header>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-gradient fixed-top menu">

        <div class="container-fluid px-4 px-md-5">

            <!-- Logo -->
            <a class="navbar-brand logo" href="<?= BASE_URL ?>/index.php">
                <i class="fa-solid fa-earth-africa"></i>
                Orbita Web
            </a>

            <!-- Burger -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto align-items-lg-center gap-2">

                    <li class="nav-item">
                        <a class="nav-link active"  href="<?= BASE_URL ?>/index.php">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link"  href="<?= BASE_URL ?>/index.php">Chi siamo</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/ui/page/buyproject.php">Prodotti</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php">Contatti</a>
                    </li>

                    <!-- Login -->
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-light btn-sm w-100 w-lg-auto mb-2 mb-lg-0" href="<?= BASE_URL ?>/ui/page/login.php">
                            Login
                        </a>
                    </li>

                    <!-- Register -->
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm w-100 w-lg-auto" href="<?= BASE_URL ?>/ui/page/register.php">
                            Register
                        </a>
                    </li>

                </ul>

            </div>

        </div>

    </nav>


<!-- Bootstrap JS -->
<script src="<?= BASE_URL ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>



    </header>
