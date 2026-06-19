<?php

require_once __DIR__ . '/Db.php';

class Customization
{
    //---------------------------------FIND----------------------------------------------

    public static function findByIdCustom(int $id): ?array
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT id, name, description, price, is_active
             FROM customizations
             WHERE id = :id'
        );

        $stmt->execute([':id' => $id]);

        $customization = $stmt->fetch();

        return $customization ?: null;
    }


    public static function findAllCustom(): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT id, name, description, price, is_active
             FROM customizations
             ORDER BY id ASC'
        );

        return $stmt->fetchAll();
    }


    public static function findActiveCustom(): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT id, name, description, price, is_active
             FROM customizations
             WHERE is_active = 1
             ORDER BY id ASC'
        );

        return $stmt->fetchAll();
    }


    //---------------------------------COUNT----------------------------------------------

    public static function countAllCustom(): int
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT COUNT(*) AS total
             FROM customizations'
        );

        return (int) $stmt->fetch()['total'];
    }


    //---------------------------------CREATE----------------------------------------------

    public static function createCustom(
        string $name,
        string $description,
        float $price,
        bool $is_active = true
    ): int
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'INSERT INTO customizations
             (name, description, price, is_active)
             VALUES
             (:name, :description, :price, :is_active)'
        );

        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':is_active' => $is_active ? 1 : 0,
        ]);

        return (int) $pdo->lastInsertId();
    }


    //---------------------------------UPDATE----------------------------------------------

    public static function updateCustom(
        int $id,
        string $name,
        string $description,
        float $price,
        bool $is_active
    ): bool
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'UPDATE customizations
             SET name = :name,
                 description = :description,
                 price = :price,
                 is_active = :is_active
             WHERE id = :id'
        );

        return $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':is_active' => $is_active ? 1 : 0,
            ':id' => $id,
        ]);
    }


    public static function toggleActiveCustom(int $id): bool
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'UPDATE customizations
             SET is_active = NOT is_active
             WHERE id = :id'
        );

        return $stmt->execute([
            ':id' => $id
        ]);
    }


    //---------------------------------DELETE----------------------------------------------

    public static function deleteCustom(int $id): bool
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'DELETE FROM customizations
             WHERE id = :id'
        );

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}

?>