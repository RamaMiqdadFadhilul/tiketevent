<?php

class DBconnection
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = new PDO(
            "pgsql:host=localhost;port=5432;dbname=DBTiketevent",
            "postgres",
            "Cho112ul-007"
        );

        $this->connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public function send_query(
        string $query,
        array $params = []
    ): Respon {
        try {
            $statement = $this->connection->prepare($query);

            $statement->execute($params);

            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

            return new Respon(
                true,
                'Query berhasil',
                $data
            );

        } catch (PDOException $e) {

            return new Respon(
                false,
                $e->getMessage()
            );
        }
    }
}
