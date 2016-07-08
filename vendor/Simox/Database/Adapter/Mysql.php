<?php
namespace Simox\Database\Adapter;

use Simox\Database\Database;

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
            $this->db_connection = new \PDO( $dsn, $this->username, $this->password );
        }
    }
    
    public function describeColumns( $table_name )
    {
        $this->initialize();
        
        $query = $this->db_connection->prepare( "describe $table_name;" );
        $query->execute();
        
        $resultset = $query->fetchAll();
        
        $columns = array();
        
        foreach ( $resultset as $row )
        {
            $columns[] = array(
                "name" => $row["Field"]
            );
        }
        
        return $columns;
    }
}
