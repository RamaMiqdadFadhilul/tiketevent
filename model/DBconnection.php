<?php

require_once __DIR__ . '/Respon.php';

class DBconnection
{
    private ?PDO $db = null;

    public function __construct()
    {
        $this->db = new PDO(
            "pgsql:host=localhost;port=5432;dbname=cobacoba",
            "postgres",
            "rmfu2910"
        );

        $this->db->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public function send_query(
        string $query,
        array $params = []
    ): Respon {
        try {
            $statement = $this->db->prepare($query);
            $statement->execute($params);

            // Jika SELECT atau RETURNING
            if (
                stripos(trim($query), 'SELECT') === 0 ||
                stripos(trim($query), 'RETURNING') !== false
            ) {
                $data = $statement->fetchAll();

                return new Respon(
                    true,
                    'Query berhasil',
                    $data
                );
            }

            // INSERT, UPDATE, DELETE
            return new Respon(
                true,
                'Query berhasil',
                []
            );

        } catch (PDOException $e) {
            return new Respon(
                false,
                $e->getMessage()
            );
        }
    }

    public function getConnection(): ?PDO
    {
        return $this->db;
    }
}