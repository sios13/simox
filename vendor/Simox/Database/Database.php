<?php
namespace Simox\Database;

abstract class Database
{
    protected $dbh;
	
	public function __construct() {}
	
	public function initialize() {}

    public function fetchAll( $table_name, $sql, $bind )
    {
        $this->initialize();
        
        $stmt = $this->dbh->prepare( $sql );
        $stmt->execute( $bind );
        $rows = $stmt->fetchAll();
        return $rows;
    }
    
    public function insertInto( $sql )
    {
        $this->initialize();
        
        $stmt = $this->dbh->prepare( $sql );
        
        //Returns true on success or false on failure
        return $stmt->execute();
    }
}
