<?php

class DBconnection
{
    private $dbconn = null;

    public function __construct()
    {
        $this->init_connect();
    }

    public function init_connect(): void
    {
        $database_url = getenv('DATABASE_URL');

        if ($database_url) {

            $conn_string = $database_url . " sslmode=require";

        } else {

            $conn_string =
                "host=localhost " .
                "port=5432 " .
                "dbname=ticketevent " .
                "user=postgres " .
                "password=PASSWORD_DATABASE_KAMU";
        }

        $this->dbconn = @pg_connect($conn_string);

        if (!$this->dbconn) {
            throw new DatabaseException(
                "Koneksi ke database tidak dapat dibentuk."
            );
        }
    }

    public function send_query(
        string $query,
        array $params = []
    ): Respon {

        if (!$this->dbconn) {
            return new Respon(
                false,
                "Koneksi belum terbentuk."
            );
        }

        $result = @pg_query_params(
            $this->dbconn,
            $query,
            $params
        );

        if ($result === false) {
            return new Respon(
                false,
                pg_last_error($this->dbconn)
            );
        }

        $data = [];

        if (pg_num_rows($result) > 0) {
            $data = pg_fetch_all($result);
        }

        return new Respon(
            true,
            "Query executed successfully",
            $data
        );
    }

    public function close_connection(): void
    {
        if ($this->dbconn) {
            pg_close($this->dbconn);
            $this->dbconn = null;
        }
    }

    public function getConnection()
    {
        return $this->dbconn;
    }
}
