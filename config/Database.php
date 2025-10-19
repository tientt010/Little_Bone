<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $connection = null;
    private static $instance = null;

    // trả về instance duy nhất của Database
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->connect();
    }

    // Kết nối đến database
    private function connect()
    {
        try {
            // Cập nhật để hỗ trợ UTF-8mb4 cho tiếng Việt
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->user,
                $this->pass,
                [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"]
            );

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            $this->connection->exec("SET CHARACTER SET utf8mb4");
            $this->connection->exec("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    // Lấy kết nối
    public function getConnection()
    {
        return $this->connection;
    }

    // Đóng kết nối
    public function closeConnection()
    {
        $this->connection = null;
    }
}
