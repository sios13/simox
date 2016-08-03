<?php
namespace Simox\Database\Adapter;

use Simox\Database;

class Mysql extends Database
{
    private $db_name;
    private $host;
    private $username;
    private $password;

    public function __construct( $params )
    {
        $this->db_name = $params["db_name"];
        $this->host = $params["host"];
        $this->username = $params["username"];
        $this->password = $params["password"];
    }
    
    public function initialize()
    {
        if ( !isset( $this->db_connection ) )
        {
            $dsn = "mysql:dbname=" . $this->db_name . ";charset=utf8;host=" . $this->host;
            
            try {
                $this->db_connection = new \PDO( $dsn, $this->username, $this->password );
            } catch ( \PDOException $e ) {
                throw new \Exception( "Database exception. Access denied." );
            }
        }
    }
}
