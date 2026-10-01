<?php

require_once __DIR__ . '/Respon.php';

class DBconnection
{
    private ?PDO $db = null;

    public function __construct()
    {
        $host = "localhost";
        $port = "5432";
        $user = "postgres";
        $pass = "NoLimitersGuys";

        // Daftar kemungkinan nama database (lowercase vs format asli)
        $dbCandidates = ["dbtiketevent", "DBtiketevent"];
        $lastException = null;

        foreach ($dbCandidates as $dbname) {
            try {
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
                $this->db = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                // Kalau berhasil konek, langsung hentikan loop
                break;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }

        // Kalau kedua nama dicoba tetap gagal konek
        if ($this->db === null) {
            throw new Exception("Gagal terhubung ke PostgreSQL: " . ($lastException ? $lastException->getMessage() : "Unknown error"));
        }
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