<?php
namespace Simox;

abstract class Model extends SimoxServiceBase
{
    private $table_name;
    
    private $attributes;
    
    // The snapshot is an array storing all the attributes with their 'original' value
    private $snapshot;

    public function __construct()
    {
        $this->table_name = get_called_class();
        
        $this->attributes = array();
        
        $this->snapshot = array();
    }
    
    /**
     * <code>
     * <?php
     * $yellow_animals = Animals::find(
     *     "WHERE color=:color ORDER BY size DESC",
     *     "bind" => array("color" = "yellow")
     * );
     * ?>
     * </code>
     * 
     * @param string $sub_sql additional sql
     * @param array $params
     * @return 
     */
    public static function find( $sub_sql = null, $params = null )
    {
        /**
         * Load parameters form $params
         */
        $bind = isset($params["bind"]) ? $params["bind"] : null;
        $cache = isset($params["cache"]) ? $params["cache"] : null;
        
        /**
         * Build the query, bind and fetch the resultset
         */
        $query = self::_buildQuery( "find", array("sub_sql" => $sub_sql) );
        
        $query->execute( $bind );
        
        $resultset = $query->fetchAll();
        
		if ( $resultset == false )
		{
			return false;
		}
        
        /**
         * Initialize the models
         */
        $models = array();
        
		foreach ( $resultset as $row )
		{
            $model = new $table_name();
			
            foreach ( $row as $key => $value )
            {
                if ( !is_int($key) )
                {
                    $model->$key = $value;
                    $model->snapshot[$key] = $value;
                }
            }
			
			$models[] = $model;
		}
		
		return $models;
    }
    
    /**
     * Uses the find function but returns only the first model
     */
    public static function findFirst( $sub_sql = null, $params = null )
    {
        $models = self::find( $sub_sql, $params );
        
        return $models[0];
    }
    
    /**
     * Helper function to check if a record exists
     */
    private function _exists( $attributes )
    {
        $sql = "SELECT * FROM $this->table_name WHERE (";
        
        foreach ( $attributes as $key => $value )
        {
            $sql .= $key . "=:" . $key . " AND ";
            //$sql .= $key . "=" . $value . " AND ";
        }
        
        $sql = rtrim( $sql, "AND " );
        $sql .= ") LIMIT 1;";
        
        $rows = $this->database->fetchAll( $sql, $this->snapshot );
        
        if ( count($rows) == 1 )
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Initialize model attributes
     * Checking the table, make sure they exist and make sure they are set in the model
     * 
     * @return array
     */
    private function _initializeAttributes()
    {
        // Retrieve the columns from the table
        $db_table_columns = $this->database->describeColumns( $this->table_name );
        
        // Make sure attributes are set and store attributes in the attributes array
        foreach ( $db_table_columns as $column )
        {
            if ( !isset($this->$column["name"]) )
            {
                throw new \Exception( "Attribute " . $column["name"] . " is not set." );
                return false;
            }
            
            $this->attributes[$column["name"]] = $this->$column["name"];
        }
    }
    
    /**
     * Builds a query, returns a query object
     */
    private static function _buildQuery( $type, $params = null )
    {
		$table_name = get_called_class();
        
        // The sql array will hold sql fragments that will later be imploded
        $sql = array();
        
        if ( $type == "find" )
        {
            $sub_sql = isset($params["sub_sql"]) ? $params["sub_sql"] : null;
        
            $sql[] = "SELECT * from $table_name";
            
            if ( isset($sub_sql) )
            {
                $sql[] .= " " . $sub_sql;
            }
            
            $sql[] = ";";
        }
        else if ( $type == "update" )
        {
            $attributes = isset($params["attributes"]) ? $params["attributes"] : null;
            $snapshot = isset($params["snapshot"]) ? $params["snapshot"] : null;
            
            $sql[] = "UPDATE $table_name SET ";
            
            foreach ( $this->attributes as $key => $value )
            {
                $sql[] = $key . "=:" . $key . ", ";
                //$sql[] = $key . "=" . $value . ", ";
            }
            
            $sql[count($sql)-1] = rtrim( $sql[count($sql)-1], ", " );
            $sql[] .= " WHERE ";
            
            foreach ( $this->snapshot as $key => $value )
            {
                $sql[] = $key . "=:snap_" . $key . " AND ";
                //$sql[] = $key . "=" . $value . " AND ";
            }
            
            $sql[count($sql)-1] = rtrim( $sql[count($sql)-1], " AND " );
            $sql[] = ";";
        }
            
        $sql = implode( $sql );
            
        return Simox::getService( "database" )->prepare( $sql );
    }
    
    /**
     * Inserts or updates the model
     */
    public function save()
    {
        // Initialize the model attributes
        $this->_initializeAttributes();
        
        // Decide if we should update or insert
        if ( $this->_exists( $attributes ) )
        {
            $query = slef::$_buildQuery( "update", array("attributes" => $this->attributes, "snapshot" => $this->snapshot) );
            
            // Add "snap_" prefix
            $_snapshot = array();
            foreach ( $this->snapshot as $key => $value )
            {
                $_snapshot["snap_" . $key] = $value;
            }
            
            $query->execute( array_merge($attributes, $_snapshot) );
        }
        else
        {
            echo "NEJ";
        }
    }
}
