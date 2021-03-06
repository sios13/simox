<?php
namespace Simox\Database\Adapter;

use Simox\Database;

class Sqlite extends Database
{
    private $db_name;
    
    public function __construct( $params )
    {
        $this->db_name = $params["db_name"] . ".db";
    }
    
    public function initialize()
    {
        if ( !isset( $this->db_connection ) )
        {
            $dsn = "sqlite:" . $this->db_name;
            
            try {
                $this->db_connection = new \PDO( $dsn );
            } catch ( \PDOException $e ) {
                throw new \Exception( "Database exception. Access denied." );
            }
        }
    }
}
