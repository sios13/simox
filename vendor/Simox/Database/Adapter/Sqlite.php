<?php
namespace Simox\Database\Adapter;

use Simox\Database\Database;

class Sqlite extends Database
{
    private $db_name;
    
    public function __construct( $params )
    {
        $this->db_name = $params["db_name"];
    }
    
    public function initialize()
    {
        if ( !isset( $this->db_connection ) )
        {
            $dsn = "sqlite:" . $this->db_name;
            $this->db_connection = new \PDO( $dsn );
        }
    }
    
    public function describeColumns( $table_name )
    {
        $this->initialize();
        
        $query = $this->db_connection->prepare( "PRAGMA table_info('$table_name');" );
        $query->execute();
        
        $resultset = $query->fetchAll();
        
        return $resultset;
    }
}
