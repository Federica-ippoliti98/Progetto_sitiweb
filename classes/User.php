<?php    

require_once __DIR__ . '/Db.php';

class User 
{   
    //---------------------------------FIND----------------------------------------------

    //function della classe che cerca PER ID (INT ID) E RESTITUISCE UN ARRAY DELL ID
    public static function findById(int $id): ?array
    {
        //CONNETTERCI
        $pdo = Db::connect();
        //PREPARARE LA QUERY AL DB
        $stmt = $pdo->prepare(

            //query
            'SELECT id, name, email, role, created_at
             FROM users
             WHERE id = :id'

        );
        //EXECUTE
        $stmt->execute([':id' => $id]);
        //FETCH
        $user = $stmt->fetch();
        //RETURN
        return $user ?: null;
    }

    
    //function della classe che cerca PER EMAIL (STRING EMAIL) E RESTITUISCE UN ARRAY DELL ID
    public static function findByEmail(string $email): ?array
    {
        //CONNETTERCI
        $pdo = Db::connect();
        //PREPARARE LA QUERY AL DB
        $stmt = $pdo->prepare(

            //query
            'SELECT id, name, email, password, role, created_at
             FROM users
             WHERE email = :email'

        );
        //EXECUTE
        $stmt->execute([':email' => $email]);
        //FETCH
        $user = $stmt->fetch(); 
        //RETURN
        return $user ?: null;
    }


        //function di ricerca di tutti gli utenti
    public static function findAll(): array
    {
        //CONNETTERCI
        $pdo = Db::connect();

         //PREPARARE LA QUERY AL DB
        $stmt = $pdo->query(

            //query
            'SELECT id, name, email, role, created_at
            FROM users
            ORDER BY created_at ASC'
            

        );

        return $stmt->fetchAll();


    }







    //---------------------------------COUNT----------------------------------------------

    public static function countAll(): ?int
    {

        //CONNETTERCI
        $pdo = Db::connect();
        //PREPARARE LA QUERY AL DB
        $stmt = $pdo->query(

            //query
            'SELECT COUNT(*) AS total FROM users'
            

        );
            return (int) $stmt->fetch()['total'];

    }






    //---------------------------------CREATE----------------------------------------------

    //function della classe che CREA un UTENTE
    public static function create(string $name, string $email, string $password, string $role = 'client'): ?int
    {   
        //controllo se esiste già un utente che sto creando con l email gà presente
        if(self::findByEmail($email)){
            return null;
        }

        //CONNETTERCI
        $pdo = Db::connect();
        //PREPARARE LA QUERY AL DB
        $stmt = $pdo->prepare(

            //query
            'INSERT INTO users (name, email, password, role)
             VALUES (:name, :email, :password, :role)'

        );
        //EXECUTE
        $stmt->execute([
                            ':name' => $name, 
                            ':email' => $email,
                            ':password' => password_hash($password, PASSWORD_DEFAULT),
                            ':role' => $role,     
                       ]);
   
      
        //RETURN
        return (int) $pdo->lastInsertId();

    }






    //---------------------------------UPDATE ROLE----------------------------------------------

    //function di EDIT RUOLO USERS
    public static function updateRole(int $id, string $role): bool
    {   
        //se l utente non ha un ruolo return false
        if (!in_array($role, ['client', 'admin'], true)){

            return false;

        }

        //CONNETTERCI
        $pdo = Db::connect();
        //PREPARARE LA QUERY AL DB
        $stmt = $pdo->prepare(

            //query AGGIORNA nella tabella USERS, settando il ruolo prendendo l ID
            'UPDATE users SET role = :role WHERE id = :id'

        );

        return $stmt->execute([':role' => $role, ':id' => $id]);

    }

    //edit dell utente
    public static function updateUser(int $id, string $name, string $email, string $role): bool
    {
        //controllo se l email non sia già utilizzata da un altro utente
        $existing = self::findByEmail($email);

        if($existing && (int) $existing['id'] !== $id){

            return false; //email già presente da qualcun altro
        }


        //CONNETTERCI
        $pdo = Db::connect();

        $stmt = $pdo->prepare(

                    //query di update su nome, email, role dato l Id
                    'UPDATE users
                     SET name = :name, email= :email, role = :role
                     WHERE id = :id'
        );
        
        return $stmt->execute([

                ':name' => $name,
                ':email' => $email,
                ':role' => $role,
                ':id' => $id,
        ]);
    }


    //---------------------------------DELETE----------------------------------------------

    public static function deleteUser(int $id): bool
    
    {
        //CONNETTERCI
        $pdo = Db::connect();
        //PREPARARE LA QUERY AL DB
        $stmt = $pdo->prepare(

            //query CANCELLA nella tabella USERS, prendendo l ID
            'DELETE FROM users WHERE id = :id'

        );

        return $stmt->execute([':id' => $id]);
    }
}


?>