<?php

require_once __DIR__ . '/helpers/auth.php';

requireLogin();

$user = currentUser() ?? [];
$isAdmin = ($user['role'] ?? '') === 'admin';

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
            <a href="#"
               class="sidebar-link active-dash text-decoration-none p-3"
               onclick="openSection('dashboard'); return false;">

                <i class="fa-solid fa-house me-3"></i>

                <span class="hide-on-collapse">
                    Dashboard
                </span>
            </a>

            <!-- ANALYTICS -->
            <a href="#"
               class="sidebar-link text-decoration-none p-3 <?= $isAdmin ? '' : 'd-none' ?>"
               onclick="openSection('analytics'); return false;">

                <i class="fa-solid fa-chart-bar me-3"></i>

                <span class="hide-on-collapse">
                    Analytics
                </span>
            </a>

            <!-- CLIENT -->
            <a href="#"
               class="sidebar-link text-decoration-none p-3 <?= $isAdmin ? '' : 'd-none' ?>"
               onclick="openSection('client'); return false;">

                <i class="fas fa-users me-3"></i>

                <span class="hide-on-collapse">
                    Client
                </span>
            </a>

            <!-- PRODUCTS -->
            <a href="#"
               class="sidebar-link text-decoration-none p-3 <?= $isAdmin ? 'd-none' : '' ?>"
               onclick="openSection('products'); return false;">

                <i class="fa-solid fa-box me-3"></i>

                <span class="hide-on-collapse">
                    Products
                </span>
            </a>

            <!-- MANAGE PRODUCTS -->
            <a href="#"
               class="sidebar-link text-decoration-none p-3 <?= $isAdmin ? '' : 'd-none' ?>"
               onclick="openSection('manageProducts'); return false;">

                <i class="fa-solid fa-box me-3"></i>

                <span class="hide-on-collapse">
                    Manage products
                </span>
            </a>

            <!-- CUSTOM -->
            <a href="#"
               class="sidebar-link text-decoration-none p-3 <?= $isAdmin ? 'd-none' : '' ?>"
               onclick="openSection('custom'); return false;">

                <i class="fa-solid fa-pen me-3"></i>

                <span class="hide-on-collapse">
                    Custom
                </span>
            </a>

            <!-- MANAGE CUSTOM -->
            <a href="#"
               class="sidebar-link text-decoration-none p-3 <?= $isAdmin ? '' : 'd-none' ?>"
               onclick="openSection('manageCustom'); return false;">

                <i class="fa-solid fa-pen me-3"></i>

                <span class="hide-on-collapse">
                    Manage customizations
                </span>
            </a>

            <!-- BOOKING -->
            <a href="#"
               class="sidebar-link text-decoration-none p-3 <?= $isAdmin ? '' : 'd-none' ?>"
               onclick="openSection('booking'); return false;">

                <i class="fa-regular fa-calendar me-3"></i>

                <span class="hide-on-collapse">
                    Booking
                </span>
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
                        <a href="logout.php" class="text-dash">
                            Logout
                        </a>
                    </p>

                </div>

            </div>

        </div>

    </nav>

    <!-- MAIN -->
    <main class="main-content w-100" style="background-color: white;">

        <div class="container-fluid">

            <!-- DASHBOARD -->
            <section id="dashboard">

                <h2>
                    Ciao <?= htmlspecialchars($user['name'] ?? 'Utente') ?>!
                    <br>
                    Benvenuto nella tua Dashboard
                </h2>

                <p class="text-muted">
                    Streamline your workflow with our intuitive dashboard.
                </p>

            </section>

            <!-- ANALYTICS -->
            <section id="analytics" class="d-none">

                <h2>Analytics</h2>

                <p class="text-muted">
                    Streamline your workflow with our intuitive dashboard.
                </p>

            </section>

            <!-- CLIENT -->
            <section id="client" class="d-none">

                <?php include 'users.php'; ?>

            

            </section>

            <!-- PRODUCTS -->
            <section id="products" class="d-none">

                <h2>Products</h2>

                <p class="text-muted">
                    Streamline your workflow with our intuitive dashboard.
                </p>

            </section>

            <!-- MANAGE PRODUCTS -->
            <section id="manageProducts" class="d-none">

                <?php include 'products.php'; ?>
                </p>

            </section>

            <!-- CUSTOM -->
            <section id="custom" class="d-none">

                <h2>Custom</h2>

                <p class="text-muted">
                    Streamline your workflow with our intuitive dashboard.
                </p>

            </section>

            <!-- MANAGE CUSTOM -->
            <section id="manageCustom" class="d-none">

                <h2>Manage Customizations</h2>

                <p class="text-muted">
                    Streamline your workflow with our intuitive dashboard.
                </p>

            </section>

            <!-- BOOKING -->
            <section id="booking" class="d-none">

                <h2>Booking</h2>

                <p class="text-muted">
                    Streamline your workflow with our intuitive dashboard.
                </p>

            </section>

        </div>

    </main>

</div>

<script>

function toggleSidebar() {

    const sidebar = document.querySelector('.sidebar');
    const wrapper = document.querySelector('.dashboard-wrapper');

    sidebar.classList.toggle('collapsed');

    /* FIX RESPONSIVE: su mobile apre/chiude completamente */
    wrapper.classList.toggle('sidebar-open');
}

function openSection(section) {

    // lista sezioni
    const sections = [
        'dashboard',
        'analytics',
        'client',
        'products',
        'manageProducts',
        'custom',
        'manageCustom',
        'booking'
    ];

    // nasconde tutte
    sections.forEach(id => {

        const element = document.getElementById(id);

        if (element) {
            element.classList.add('d-none');
        }
    });

    // mostra quella selezionata
    const activeSection = document.getElementById(section);

    if (activeSection) {
        activeSection.classList.remove('d-none');
    }

    // rimuove active da tutti i link
    document.querySelectorAll('.sidebar-link').forEach(link => {

        link.classList.remove('active-dash');
    });

    // aggiunge active al link corretto
    const activeLink = document.querySelector(
        `.sidebar-link[onclick*="${section}"]`
    );

    if (activeLink) {
        activeLink.classList.add('active-dash');
    }
}

</script>
<script>
window.addEventListener("load", () => {

    const hash = window.location.hash.replace('#', '');

    const valid = [
        'dashboard',
        'analytics',
        'client',
        'products',
        'manageProducts',
        'custom',
        'manageCustom',
        'booking'
    ];

    function init() {

        if (hash && valid.includes(hash)) {
            openSection(hash);
        } else {
            openSection('dashboard');
        }
    }

    // doppio safe check (evita race condition browser)
    requestAnimationFrame(init);

});
</script>