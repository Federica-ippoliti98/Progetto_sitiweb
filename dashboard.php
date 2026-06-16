<?php

require_once __DIR__ . '/helpers/auth.php';

requireLogin();

$user = currentUser();

include 'header.php';
?>

<p class="text-white">Vuoi uscire?
    <a href="logout.php" class="text-warning">Logout</a>
</p>