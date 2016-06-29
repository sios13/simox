<?php
namespace Simox\Database\Adapter;

use Simox\Database\Database;

class Mysql extends Database
{
    private $dbname;
    private $host;
    private $username;
    private $password;

    public function __construct( $params )
    {
        $this->dbname = $params["dbname"];
        $this->host = $params["host"];
        $this->username = $params["username"];
        $this->password = $params["password"];
    }
    
    public function initialize()
    {
        $dsn = "mysql:dbname=" . $this->dbname . ";charset=utf8;host=" . $this->host;
        
        if ( !isset( $this->dbh ) )
        {
            $this->dbh = new \PDO( $dsn, $this->user, $this->password );
        }
    }
}
