<?php

    require_once __DIR__ . '/Db.php';


    class Booking 
    {
        public const AVAILABLE_TIMES = [
                                        '09:00', 
                                        '10:00',
                                        '11:00',
                                        '12:00',
                                        '14:00',
                                        '15:00',
                                        '16:00',
                                        '17:00',
                                        ];


        
        
        
        public static function findById(int $id): ?array
        {

            //connettiamo al db
            $pdo = Db::connect();
            
            //query con JOIN : prendiamo anche il nome ed email del cliente
            $stmt = $pdo->prepare(

                    'SELECT b.id, b.user_id,b.product_id, b.scheduled_date, b.scheduled_time, b.notes, b.created_at, b.updated_at,
                    
                            u.name AS user_name, u.email AS user_email 
                            
                    FROM bookings b
                    JOIN users u ON b.user_id = u.id
                    WHERE b.id = :id'



            );
            $stmt->execute([':id' => $id]);
            $booking = $stmt->fetch();

            return $booking ?: null;


        } 












    }








?>