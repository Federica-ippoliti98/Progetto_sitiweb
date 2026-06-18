<?php

require_once __DIR__ . '/Db.php';

class Customization
{
    // Cerca personalizzazione per ID
    public static function findById(int $id): ?array
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT *
             FROM customizations
             WHERE id = :id'
        );

        $stmt->execute([
            ':id' => $id
        ]);

        $customization = $stmt->fetch(PDO::FETCH_ASSOC);

        return $customization ?: null;
    }

    // Tutte le personalizzazioni di un prodotto
    public static function findByProduct(int $productId): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT *
             FROM customizations
             WHERE product_id = :product_id
             ORDER BY id DESC'
        );

        $stmt->execute([
            ':product_id' => $productId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Solo personalizzazioni attive di un prodotto
    public static function activeByProduct(int $productId): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT *
             FROM customizations
             WHERE product_id = :product_id
             AND is_active = 1
             ORDER BY id DESC'
        );

        $stmt->execute([
            ':product_id' => $productId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crea personalizzazione
    public static function create(
        int $productId,
        string $name,
        string $description,
        float $price,
        bool $isActive = true
    ): ?int {

        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'INSERT INTO customizations
            (
                product_id,
                name,
                description,
                price,
                is_active
            )
            VALUES
            (
                :product_id,
                :name,
                :description,
                :price,
                :is_active
            )'
        );

        $stmt->execute([
            ':product_id' => $productId,
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':is_active' => $isActive
        ]);

        return (int) $pdo->lastInsertId();
    }

    // Conta tutte le personalizzazioni
    public static function countAll(): int
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT COUNT(*) AS total
             FROM customizations'
        );

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['total'];
    }

    // Tutte le personalizzazioni
    public static function all(): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT *
             FROM customizations
             ORDER BY id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Attiva/disattiva
    public static function updateStatus(
        int $id,
        bool $isActive
    ): bool {

        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'UPDATE customizations
             SET is_active = :is_active
             WHERE id = :id'
        );

        return $stmt->execute([
            ':is_active' => $isActive,
            ':id' => $id
        ]);
    }

    // Aggiorna campo singolo
    public static function updateField(
        int $id,
        string $field,
        mixed $value
    ): bool {

        $allowed = [
            'product_id',
            'name',
            'description',
            'price',
            'is_active'
        ];

        if (!in_array($field, $allowed, true)) {
            return false;
        }

        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            "UPDATE customizations
             SET $field = :value
             WHERE id = :id"
        );

        return $stmt->execute([
            ':value' => $value,
            ':id' => $id
        ]);
    }

    // Aggiornamento completo
    public static function update(
        int $id,
        int $productId,
        string $name,
        string $description,
        float $price,
        bool $isActive
    ): bool {

        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'UPDATE customizations
             SET
                product_id = :product_id,
                name = :name,
                description = :description,
                price = :price,
                is_active = :is_active
             WHERE id = :id'
        );

        return $stmt->execute([
            ':product_id' => $productId,
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':is_active' => $isActive,
            ':id' => $id
        ]);
    }

    // Elimina personalizzazione
    public static function delete(int $id): bool
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