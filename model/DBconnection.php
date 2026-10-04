<?php

class DBconnection
{
    private string $host = "localhost";
    private string $port = "5432";
    private string $dbname = "cobacoba";
    private string $username = "postgres";
    private string $password = "rmfu2910";
    private $dbconn = null;

    public function __construct()
    {
        $this->init_connect();
    }

    public function init_connect(): void
    {
        $conn_string =
            "host={$this->host} " .
            "port={$this->port} " .
            "dbname={$this->dbname} " .
            "user={$this->username} " .
            "password={$this->password}";

        $this->dbconn = @pg_connect($conn_string);

        if (!$this->dbconn) {
            throw new DatabaseException(
                "Koneksi ke database {$this->dbname} tidak dapat dibentuk."
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
