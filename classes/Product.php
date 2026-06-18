<?php    

require_once __DIR__ . '/Db.php';

class Product
{   
    //---------------------------------FIND----------------------------------------------

    public static function findById(int $id): ?array
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT id, name, description,  price, is_active, file_path, image_path, created_at
             FROM products
             WHERE id = :id'
        );

        $stmt->execute([':id' => $id]);

        $product = $stmt->fetch();

        return $product ?: null;
    }


    public static function findAll(): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT id, name, description,  price, is_active, file_path, image_path, created_at
             FROM products
             ORDER BY created_at ASC'
        );

        return $stmt->fetchAll();
    }


    public static function findActive(): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT id, name, description,  price, is_active, file_path, image_path, created_at
             FROM products
             WHERE is_active = 1
             ORDER BY created_at ASC'
        );

        return $stmt->fetchAll();
    }


    //---------------------------------COUNT----------------------------------------------

    public static function countAll(): int
    {
        $pdo = Db::connect();

        $stmt = $pdo->query('SELECT COUNT(*) AS total FROM products');

        return (int) $stmt->fetch()['total'];
    }


    //---------------------------------CREATE----------------------------------------------

    public static function create(
        string $name,
        string $description,
        float $price,
        bool $is_active = true,
        ?string $file_path = null,
        ?string $image_path = null
    ): int
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'INSERT INTO products 
            (name, description,  price, is_active, file_path, image_path)
            VALUES
            (:name, :description, :price, :is_active, :file_path, :image_path)'
        );

        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':is_active' => $is_active ? 1 : 0,
            ':file_path' => $file_path,
            ':image_path' => $image_path,
        ]);

        return (int) $pdo->lastInsertId();
    }


    //---------------------------------UPDATE----------------------------------------------

    public static function update(
        int $id,
        string $name,
        string $description,
        float $price,
        bool $is_active,
        ?string $file_path = null,
        ?string $image_path = null
    ): bool
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'UPDATE products
             SET name = :name,
                 description = :description,
                 price = :price,
                 is_active = :is_active,
                 file_path = :file_path,
                 image_path = :image_path
             WHERE id = :id'
        );

        return $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':is_active' => $is_active ? 1 : 0,
            ':file_path' => $file_path,
            ':image_path' => $image_path,
            ':id' => $id,
        ]);
    }


    public static function toggleActive(int $id): bool
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'UPDATE products
             SET is_active = NOT is_active
             WHERE id = :id'
        );

        return $stmt->execute([':id' => $id]);
    }


    //---------------------------------DELETE----------------------------------------------

    public static function delete(int $id): bool
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'DELETE FROM products WHERE id = :id'
        );

        return $stmt->execute([':id' => $id]);
    }
}
?>