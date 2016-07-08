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
    
    /**
     * Builds a query, returns a query object
     */
    public function buildQuery( $table_name, $type, $params = null )
    {
        if ( $type == "find" )
        {
            $sub_sql = isset($params["sub_sql"]) ? $params["sub_sql"] : null;
        
            $sql = "SELECT * FROM $table_name";
            
            if ( isset($sub_sql) )
            {
                $sql .= " " . $sub_sql;
            }
            
            $sql .= ";";
        }
        else if ( $type == "update" )
        {
            $columns = isset($params["columns"]) ? $params["columns"] : null;
            
            $sql = "UPDATE $table_name SET ";
            
            foreach ( $columns as $column )
            {
                $sql .= $column["name"] . "=:" . $column["name"] . ",";
            }
            
            $sql = rtrim( $sql, ", " );
            $sql .= " WHERE ";
            
            foreach ( $columns as $column )
            {
                $sql .= $column["name"] . "=:snap_" . $column["name"] . " AND ";
            }
            
            $sql = rtrim( $sql, " AND " );
            $sql .= ";";
        }
        else if ( $type == "insert" )
        {
            $columns = isset($params["columns"]) ? $params["columns"] : null;
            
            $sql = "INSERT INTO $table_name (";
            
            foreach ( $columns as $column )
            {
                $sql .= $column["name"] . ", ";;
            }
            
            $sql = rtrim( $sql, ", " );
            
            $sql .= ") VALUES (";
            
            foreach ( $columns as $column )
            {
                $sql .= ":" . $column["name"] . ", ";
            }
            
            $sql = rtrim( $sql, ", " );
            $sql .= ");";
        }
        
        echo $sql . "<br>";
        return $this->prepare( $sql );
    }
}
