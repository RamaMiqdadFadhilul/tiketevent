<?php

require_once __DIR__ . '/Respon.php';

class DBconnection
{
    private ?PDO $db = null;

    public function __construct()
    {
        $this->connection = new PDO(
            "pgsql:host=localhost;port=5433;dbname=dbticketevent",
            "postgres",
            "Velisa_272017"
        );

        $this->connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public function send_query(string $sql, array $params = []): Respon
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            // Jika query adalah SELECT atau mengembalikan data
            if (stripos(trim($sql), 'SELECT') === 0 || stripos(trim($sql), 'RETURNING') !== false) {
                $data = $stmt->fetchAll();
                return new Respon(true, 'Query berhasil', $data);
            }

            // Jika query adalah INSERT, UPDATE, DELETE
            return new Respon(true, 'Query berhasil', []);

        } catch (PDOException $e) {
            return new Respon(false, 'Database menolak: ' . $e->getMessage(), []);
        }
    }

    public function getConnection(): ?PDO
    {
        return $this->db;
    }
}