<?php  
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../classes/User.php';

//verifica che sono admin -> funzione
requireAdmin();
$user = currentUser(); // mi dice chi sono se admin o client


$errors = [];
$success = '';


//---------------------------------------------------GESTIONE FORM-------------------------------------

if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create'){

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'client';

    //---------------------------------------------Validazioni input
    if ($name === ''){

        $errors[] = 'Name is required!';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
        $errors[] = 'Invalid email address';
    } 

    if(strlen($password) < 8){

        $errors[] = 'Password must be 8 char!';
    }

    if(!in_array($role, ['client', 'admin'], true)){

        $errors[] = 'Invalid Role';
    }


    //se non ho l array di errors popolato, 
    //quindi non ho errori di validazione, procedo con la creazione di un nuovo utente

    if(empty($errors)){

        $newId = User::create($name, $email, $password, $role);

        if($newId === null){

            $errors[] = 'This email is already registered!';
        }else {

            $success = "User created successfully!";

            $_POST = [];
        }
    }

}


//---------------------------------------------------DELETE USER-------------------------------------


if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete'){


    //prendiamo il target ID (cioè l id dell oggetto che varia)
    $tardgetId = (int) ($_POST['user_id'] ?? 0);

    if($tardgetId === $user['id']){

        $errors[] = "You can't delete yourself !";
    }elseif (User::deleteUser($tardgetId)){

        $success = "User deleted!";
    }else {

        $errors[] = "Failed delete user.";
    }

}


//---------------------------------------------------TOGGLE ROLE-------------------------------------


if($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_role'){


    //prendiamo il target ID (cioè l id dell oggetto che varia)
    $tardgetId = (int) ($_POST['user_id'] ?? 0);
    $newRole = $_POST['new_role'] ?? '';

    if($tardgetId === $user['id']){

        $errors[] = "You can't change your role !";
    
    }elseif (User::updateRole($tardgetId, $newRole)){

        $success = "User deleted!";
    }else {

        $errors[] = "Failed delete user.";
    }
}



    //recupero tutti gli users
    $users = User::findAll();

    //header dashboard
    include __DIR__ . '/../header_dashboard.php';
?>

<!-- MAIN CONTENT -->
    <main class="dashboard-content" >

        <header class="dashboard-header mb-4">
            <h2 class="text-dash fw-bold mb-1">
                User Management
            </h2>
            <p class="text-50 mb-0">Create a new user and manage exiisting account.</p>
        </header>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="dashboard-panel">
                    <h4 class="text-dash fw-bold mb-3">
                        <i class="fas fa-user-plus"></i> New User
                    </h4>


                    <form action="" method="post">

                        <input type="hidden" name="action" value="create">

                        <div class="mb-3">
                            <label for="name" class="form-label ">Name</label>
                            <input 
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                                    required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label ">Email</label>
                            <input 
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                    required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label ">Password</label>
                            <input 
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    minlength= "8"
                                    required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label ">Role</label>
                            <select name="role" id="role" class="form-select">
                                <option value="client" <?= ($_POST['role'] ?? 'client') === 'client' ? 'selected' : '' ?>>Client</option>
                                <option value="admin"  <?= ($_POST['role'] ?? 'client') === 'admin'  ? 'selected' : '' ?>>Admin</option>


                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-dash w-100 text-white">
                        <i class="fas fa-plus"></i>Create User
                        </button>

                    </form>

                </div>
            </div>

            <div class="col-lg-8">
                <div class="dashboard-panel">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="text-dash fw-bold">
                            <i class="fas fa-users"></i> All users
                        </h4>
                       
                    </div>

                    <div class="table-responsive">

                        <table class="table table-dash mb-0 table-light">
                            <thead style="background-color: rgba(0, 0, 0, 0);">
                                <tr class="text-dark">
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody style="background-color: rgba(0, 0, 0, 0);">

                                <?php foreach($users as $u): ?>

                                    <tr>
                                        <td>#<?= (int) $u['id'] ?></td>

                                        <td>
                                            <?= htmlspecialchars($u['name']) ?>
                                            <!--Se siamo noi gli utenti in tabella colora di warning-->
                                            <?php if($u['id'] === $user['id']): ?>
                                                <small style="color: #7e62c0;">(you)</small>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= htmlspecialchars($u['email']) ?></td>

                                        <td><?= htmlspecialchars($u['role']) ?></td>

                                        <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>

                                        <td class="text-end">

                                        <?php if ($u['id'] !== $user['id']): ?>

                                            <!-- EDIT -->
                                            <a href="edit-user.php?id=<?= (int) $u['id'] ?>"
                                                    class="btn btn-sm btn-dash"
                                                    title="Edit user">
                                                <i class="fas fa-edit text-white"></i>
                                            </a>

                                            <!-- TOGGLE ROLE ENT_QUOTES -> Converte entrambe le virgolette: doppie " E singole ':  gli apostrofi diventano &#039 e la stringa JS non si rompe-->
                                            <form action="" method="post" class="d-inline">
                                                <input type="hidden" name="action"   value="toggle_role">
                                                <input type="hidden" name="user_id"  value="<?= (int) $u['id'] ?>">
                                                <input type="hidden" name="new_role" value="<?= $u['role'] === 'admin' ? 'client' : 'admin' ?>">
                                                <button type="submit"
                                                        class="btn btn-sm btn-dash"
                                                        title="Make <?= $u['role'] === 'admin' ? 'client' : 'admin' ?>"
                                                        onclick="return confirm('Change role of <?= htmlspecialchars($u['name'], ENT_QUOTES) ?>?');">
                                                    <i class="fas fa-exchange-alt text-white"></i>
                                                </button>
                                            </form>

                                            <!-- DELETE -->
                                            <form action="" method="post" class="d-inline">
                                                <input type="hidden" name="action"  value="delete">
                                                <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                                                <button type="submit"
                                                        class="btn btn-sm btn-dash"
                                                        title="Delete user"
                                                        onclick="return confirm('Delete user <?= htmlspecialchars($u['name'], ENT_QUOTES) ?>? This cannot be undone.');">
                                                    <i class="fas fa-trash text-white"></i>
                                                </button>
                                            </form>

                                        <?php else: ?>
                                            <span class="text-50 small">— current user —</span>
                                        <?php endif; ?>

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