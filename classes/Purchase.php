<?php

require_once __DIR__ . '/Db.php';

class Purchase
{
    // Crea un acquisto simulato (status = paid)
    public static function simulatePurchase(int $userId, int $productId, float $price): ?int
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'INSERT INTO purchases (
                user_id,
                product_id,
                total_price_paid,
                status,
                payment_ref
            )
            VALUES (
                :uid,
                :pid,
                :price,
                "paid",
                :ref
            )'
        );

        $ref = 'SIM-' . date('Y') . '-' .
            str_pad((string)$productId, 5, '0', STR_PAD_LEFT) .
            '-' .
            strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

        $ok = $stmt->execute([
            ':uid'   => $userId,
            ':pid'   => $productId,
            ':price' => $price,
            ':ref'   => $ref,
        ]);

        return $ok ? (int)$pdo->lastInsertId() : null;
    }

    // Trova un acquisto per ID
    public static function findById(int $id): ?array
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT
                pu.id,
                pu.user_id,
                pu.product_id,
                pu.total_price_paid,
                pu.status,
                pu.payment_ref,
                pu.created_at,
                p.name AS product_name,
                u.name AS user_name,
                u.email AS user_email
            FROM purchases pu
            JOIN products p ON pu.product_id = p.id
            JOIN users u ON pu.user_id = u.id
            WHERE pu.id = :id'
        );

        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();

        return $row ?: null;
    }

    // Tutti gli acquisti di un utente
    public static function findByUserId(int $userId): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT
                pu.id,
                pu.product_id,
                pu.total_price_paid,
                pu.status,
                pu.payment_ref,
                pu.created_at,
                p.name AS product_name,
                p.description AS product_description
            FROM purchases pu
            JOIN products p ON pu.product_id = p.id
            WHERE pu.user_id = :uid
            ORDER BY pu.created_at DESC'
        );

        $stmt->execute([':uid' => $userId]);

        return $stmt->fetchAll();
    }

    // Tutti gli acquisti (admin)
    public static function findAll(): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT
                pu.id,
                pu.total_price_paid,
                pu.status,
                pu.payment_ref,
                pu.created_at,
                p.name AS product_name,
                u.name AS user_name,
                u.email AS user_email
            FROM purchases pu
            JOIN products p ON pu.product_id = p.id
            JOIN users u ON pu.user_id = u.id
            ORDER BY pu.created_at DESC'
        );

        return $stmt->fetchAll();
    }

    // Conteggio acquisti di un utente
    public static function countByUserId(int $userId): int
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS total
             FROM purchases
             WHERE user_id = :uid'
        );

        $stmt->execute([':uid' => $userId]);

        return (int)$stmt->fetch()['total'];
    }

    // Conteggio totale acquisti
    public static function countAll(): int
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT COUNT(*) AS total
             FROM purchases'
        );

        return (int)$stmt->fetch()['total'];
    }

    // Fatturato totale
    public static function totalRevenue(): float
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT COALESCE(SUM(total_price_paid), 0) AS total
             FROM purchases
             WHERE status = "paid"'
        );

        return (float)$stmt->fetch()['total'];
    }

    // Imposta come rimborsato
    public static function setRefunded(int $id): bool
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'UPDATE purchases
             SET status = "refunded"
             WHERE id = :id
             AND status = "paid"'
        );

        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }
}