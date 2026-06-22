<?php

require_once __DIR__ . '/Db.php';

class Booking
{
    // Orari disponibili
    public const AVAILABLE_TIMES = [
        '09:00', '10:00', '11:00', '12:00',
        '14:00', '15:00', '16:00', '17:00', '18:00',
    ];

    // Trova una prenotazione per ID
    public static function findById(int $id): ?array
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT b.id,
                    b.user_id,
                    b.scheduled_date,
                    b.scheduled_time,
                    b.notes,
                    b.status,
                    b.created_at,
                    u.name AS user_name,
                    u.email AS user_email
             FROM bookings b
             JOIN users u ON b.user_id = u.id
             WHERE b.id = :id'
        );

        $stmt->execute([':id' => $id]);

        $booking = $stmt->fetch();

        return $booking ?: null;
    }

    // Tutte le prenotazioni di un utente
    public static function findByUserId(int $userId): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT id,
                    scheduled_date,
                    scheduled_time,
                    notes,
                    status,
                    created_at
             FROM bookings
             WHERE user_id = :uid
             ORDER BY scheduled_date DESC,
                      scheduled_time DESC'
        );

        $stmt->execute([':uid' => $userId]);

        return $stmt->fetchAll();
    }

    // Tutte le prenotazioni (admin)
    public static function findAll(): array
    {
        $pdo = Db::connect();

        $stmt = $pdo->query(
            'SELECT b.id,
                    b.scheduled_date,
                    b.scheduled_time,
                    b.notes,
                    b.status,
                    b.created_at,
                    u.id AS user_id,
                    u.name AS user_name,
                    u.email AS user_email
             FROM bookings b
             JOIN users u ON b.user_id = u.id
             ORDER BY
                CASE b.status
                    WHEN "pending" THEN 1
                    WHEN "completed" THEN 2
                    WHEN "cancelled" THEN 3
                END,
                b.scheduled_date DESC,
                b.scheduled_time DESC'
        );

        return $stmt->fetchAll();
    }

    // Crea una prenotazione
    public static function create(
        int $userId,
        string $date,
        string $time,
        ?string $notes = null
    ): int {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'INSERT INTO bookings (
                user_id,
                scheduled_date,
                scheduled_time,
                notes,
                status
             )
             VALUES (
                :uid,
                :date,
                :time,
                :notes,
                "pending"
             )'
        );

        $stmt->execute([
            ':uid'   => $userId,
            ':date'  => $date,
            ':time'  => $time,
            ':notes' => $notes,
        ]);

        return (int) $pdo->lastInsertId();
    }

    // Aggiorna lo stato della prenotazione
    public static function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['pending', 'cancelled', 'completed'], true)) {
            return false;
        }

        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'UPDATE bookings
             SET status = :status
             WHERE id = :id'
        );

        return $stmt->execute([
            ':status' => $status,
            ':id'     => $id,
        ]);
    }

    // Cancella una prenotazione dell'utente se ancora pending
    public static function deleteIfOwnedAndPending(int $id, int $userId): bool
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'DELETE FROM bookings
             WHERE id = :id
               AND user_id = :uid
               AND status = "pending"'
        );

        $stmt->execute([
            ':id'  => $id,
            ':uid' => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }

    // Cancella una prenotazione (admin)
    public static function deleteBooking(int $id): bool
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'DELETE FROM bookings
             WHERE id = :id'
        );

        return $stmt->execute([
            ':id' => $id,
        ]);
    }

    // Conta le prenotazioni di un utente
    public static function countByUserId(int $userId): int
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS total
             FROM bookings
             WHERE user_id = :uid'
        );

        $stmt->execute([
            ':uid' => $userId,
        ]);

        return (int) $stmt->fetch()['total'];
    }

    // Conta le prenotazioni per stato
    public static function countByStatus(string $status): int
    {
        $pdo = Db::connect();

        $stmt = $pdo->prepare(
            'SELECT COUNT(*) AS total
             FROM bookings
             WHERE status = :status'
        );

        $stmt->execute([
            ':status' => $status,
        ]);

        return (int) $stmt->fetch()['total'];
    }
}

