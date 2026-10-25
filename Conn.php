<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

abstract class Conn
{
    public string $db;
    public string $host;
    public string $user;
    public string $pass;
    public string $dbname;
    public int $port;
    public object $connect;

    public function __construct()
    {
        $this->db = $_ENV['DB_CONNECTION'];
        $this->host = $_ENV['DB_HOST'];
        $this->user = $_ENV['DB_USERNAME'];
        $this->pass = $_ENV['DB_PASSWORD'];
        $this->dbname = $_ENV['DB_DATABASE'];
        $this->port = $_ENV['DB_PORT'];
    }

    public function connectDb()
    {
       
try {
    $dsn = "{$this->db}:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";

    $this->connect = new PDO(
        $dsn,
        $this->user,
        $this->pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    return $this->connect;

} catch (PDOException $err) {

    error_log("Erro de conexão PDO: " . $err->getMessage());

    throw new RuntimeException(
        "Não foi possível conectar ao banco de dados.",
        0,
        $err
    );
}

    }
}