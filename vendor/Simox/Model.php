<?php
namespace Simox;

abstract class Model extends SimoxServiceBase
{
    private $table_name;

    public function __construct()
    {
        $this->table_name = get_called_class();
    }
    
    /**
    * @param string $sub_sql additional sql
    * @param string $bind variables that bind to the sql
    * @return 
    */
    public static function find( $sub_sql = null, $bind = null )
    {
        $db_connection = Simox::getService( "database" );
        
		$table_name = get_called_class();
		
		$sql = "SELECT * from $table_name $sub_sql;";
        
        $rows = $db_connection->fetchAll( $table_name, $sql, $bind );
        
		if ( $rows == false )
		{
			return false;
		}
     
        $models = array();
		
		foreach ( $rows as $row )
		{
            $model = new $table_name();
			
            foreach ( $row as $key => $value )
            {
                $model->$key = $value;
            }
			
			$models[] = $model;
		}
		
		return $models;
    }
    
    public function save()
    {
        $sql = "INSERT INTO $this->table_name VALUES (";
        
        foreach ( $this as $key => $value )
        {
            if ( !is_numeric( $key ) && $key != "database" && $key != "table_name" )
            {
                if ( is_string($value) )
                {
                    $sql .= "'$value'" . ",";
                }
                else if ( $value == "" )
                {
                    $sql .= "NULL" . ",";
                }
                else
                {
                    $sql .= "$value" . ",";
                }
            }
        }
        
        $sql = rtrim( $sql, "," );
        $sql .= ");";
        
		//Returns true on success or false on failure
        return Simox::getService( "database" )->insertInto( $sql );
    }
}
