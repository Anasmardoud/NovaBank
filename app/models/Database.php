<?php
class Database
{
    private $connection;

    public function __construct($host, $username, $password, $dbname)
    {
        $this->connection = new mysqli($host, $username, $password, $dbname);
        if ($this->connection->connect_error) {
            Helper::log("Database connection failed: " . $this->connection->connect_error, 'ERROR');
            die("We are currently experiencing technical difficulties. Please try again later.");
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
