<?php
namespace Simox\Database;

abstract class Database
{
    protected $db_connection;
	
	public function __construct() {}
	
	public function initialize() {}
    
    public abstract function describeColumns( $table_name );
    
    public function prepare( $sql )
    {
        $this->initialize();
        
        return $this->db_connection->prepare( $sql );
    }
    /*
    public function fetchAll( $sql, $bind )
    {
        $this->initialize();
        
        $query = $this->dbh->prepare( $sql );
        $query->execute( $bind );
        $rows = $query->fetchAll();
        return $rows;
    }
    
    public function insertInto( $sql, $bind )
    {
        $this->initialize();
        
        $query = $this->db_connection->prepare( $sql );
        
        //Returns true on success or false on failure
        return $query->execute( $bind );
    }
    */
}
