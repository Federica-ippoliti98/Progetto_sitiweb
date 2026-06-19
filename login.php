<?php  

//partire la sessione
session_start();

//richiamo del singleton per attivare le PDO
require_once __DIR__ . '/classes/Db.php';

$errors = [];
$success = false;

//SE LA CHIAMATA è POST
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

//recupero degli input
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    //validazione di entrambi i campi 
    if($email === '' || $password === ''){
        $errors = 'Email or password are required!';

    } else {
        //connetto db
        $pdo = Db::connect();

        //controllo che l email non sia già registrata
        $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch();

        //verifico la password
        if($user && password_verify($password, $user['password'])){

            //rigenero la sessione
             //session_rigenerate_id(true);
           

            $_SESSION['user_id']= $user['id'];
            $_SESSION['user_name']= $user['name'];
            $_SESSION['user_role']= $user['role'];

            //login riuscito
            header('Location: dashboard.php');
            exit;

        }

        $errors = 'Invalid credential';



    }
}
?>


<?php include 'header.php'?>

<main class="mainProject py-5">

    <section class="bg_personal min-vh-100 py-5">

         <div class="container">

            <div class="row justify-content-center m-2">

                <div class="col-lg-5 card p-5 ">

                    
                    <div>
                        <h2 class="fw-bold text-center mb-4 text-white ">Login</h2>
                    </div>
    

                    <?php if($errors): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors)?>
                        </div>
                    <?php endif; ?>





                    <form action="" method="post">

                        <div class="mb-3">
                            <label for="email" class="form-label text-white">Email</label>
                            <input
                             type="email" 
                             class="form-control" 
                             id="email"
                             name="email"
                             value="<?= htmlspecialchars($_POST['email'] ??'')?>"
                             required>

                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label text-white">Password</label>
                            <input 
                            type="password" 
                            class="form-control" 
                            id="password"
                            name="password"
                            required>
                        </div>

                        <button type="submit" class="btn btn-dash w-100 text-white">Login</button>

                        <p class="text-white text-center mt-3 mb-0">
                            No account yet?
                            <a href="register.php" class="text-dash">Register</a>
                        </p>


                    </form>






                </div>

            </div>



         </div>






    </section>

</main>

<?php include 'footer.php'?>