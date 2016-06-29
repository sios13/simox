<?php
namespace Simox\Database;

use Simox\Database\Database;

class Sqlite extends Database
{
    private $dbname;
    
    public function __construct( $params )
    {
        $this->dbname = $params["dbname"];
    }
    
    public function initialize()
    {
        $dsn = "sqlite:" . $this->dbname;
        
        if ( !isset( $this->dbh ) )
        {
            $this->dbh = new \PDO( $dsn );
        }
    }
}
