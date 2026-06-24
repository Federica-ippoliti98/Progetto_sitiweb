<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../../classes/Purchase.php';

session_start();

$errors  = [];
$success = '';


//---------------------------------------------------ACQUISTO SIMULATO

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'buy') {

    //l'utente DEVE essere loggato per comprare
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/ui/page/login.php?redirect=' . urlencode(BASE_URL . '/ui/page/buyproject.php'));
        exit;
    }

    $productId = (int) ($_POST['product_id'] ?? 0);

    //prendiamo il prodotto dal DB per leggere il prezzo
    //(NON ci fidiamo del prezzo nel form: l'utente potrebbe modificarlo!)
    $product = Product::findById($productId);

    if (!$product) {
        $errors[] = "Product not found.";
    } elseif (!$product['is_active']) {
        $errors[] = "This product is no longer available.";
    } else {
        //pagamento simulato: creiamo subito la purchase con status='paid'
        $newId = Purchase::simulatePurchase(
            (int) $_SESSION['user_id'],
            (int) $product['id'],
            (float) $product['price']
        );

        if ($newId) {
            $success = "Purchase completed! Reference #{$newId}. Check your purchases page.";
        } else {
            $errors[] = "Something went wrong with the purchase.";
        }
    }
}


//recupero i prodotti attivi dal DB
$products = Product::findActive();

include '../components/header.php';

?>


<main class="mainProject">

    <section class="bg_personal min-vh-100 py-5">
        <div class="container">

            <h2 class="display-3 fw-bold text-dash text-center mb-3">
                Software & Resources
            </h2>

            <p class="text-white text-center mb-5">
                Discover all available products and resources.
            </p>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE_URL ?>/customer/purchases_client.php" class="alert-link ms-2">View my purchases</a>
                    <?php endif; ?>
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

            <?php if (empty($products)): ?>

                <div class="text-center text-white-50 py-5">
                    No products available right now. Come back soon!
                </div>

            <?php else: ?>

                <div class="row g-4">

                    <?php foreach ($products as $p): ?>

                        <div class="col-lg-4 col-md-6">
                            <div class="card software-card  text-white h-100">

                                <?php
                                    //decidiamo che immagine mostrare:
                                    //se il prodotto ha image_path popolato lo usiamo,
                                    //altrimenti usiamo software.png come default
                                    $img = !empty($p['image_path'])
                                        ? $p['image_path']
                                        : 'software.png';
                                ?>
                                <img src="<?= BASE_URL ?>/images/<?= htmlspecialchars($img) ?>"
                                     class="card-img-top"
                                     alt="<?= htmlspecialchars($p['name']) ?>">

                                <div class="card-body d-flex flex-column">

                                    <h5 class="card-title text-dash">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </h5>

                                    <p class="card-text text-light">
                                        <?= htmlspecialchars($p['description']) ?>
                                    </p>

                                    <div class="mt-auto d-flex justify-content-between align-items-center">

                                        <span class="software-price">
                                            €<?= number_format((float) $p['price'], 2) ?>
                                        </span>

                                        <form action="" method="post" class="d-inline m-0">
                                            <input type="hidden" name="action"     value="buy">
                                            <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                                            <button type="submit"
                                                    class="btn btn-dash text-white"
                                                    onclick="return confirm('Buy <?= htmlspecialchars($p['name'], ENT_QUOTES) ?> for €<?= number_format((float) $p['price'], 2) ?>?');">
                                                Buy Now
                                            </button>
                                        </form>

                                    </div>

                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php include __DIR__ . '/../components/footer.php' ?>