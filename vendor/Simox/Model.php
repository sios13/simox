<?php
namespace Simox;

abstract class Model extends SimoxServiceBase
{
    /**
     * Name of the table attached to this model
     */
    private $_table_name;
    
    /**
     * Array with the original attributes of the model
     */
    private $_snapshot;
    
    /**
     * Array with an description of the table columns
     */
    private $_columns;
    
    /**
     * Is true if model exists in db table
     * Is set to true when a model is created by the find function
     */
    private $_exists_in_db;

    public function __construct()
    {
        $this->_table_name = get_called_class();
        
        $this->_columns = $this->database->describeColumns( $this->_table_name );
        
        $this->_snapshot = array();
        
        foreach ( $this->_columns as $column )
        {
            $this->_snapshot[$column["name"]] = $this->$column["name"];
        }
        
        $this->_exists_in_db = false;
    }
    
    public function setExists( $value )
    {
        $this->_exists_in_db = $value;
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
        
        $table_name = get_called_class();
        
        /**
         * Build the query, bind and fetch the resultset
         */
        $query = Simox::getService("database")->buildQuery( $table_name, "find", array("sub_sql" => $sub_sql) );
        
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
            
            $model->setExists( true );
			
            foreach ( $row as $key => $value )
            {
                if ( !is_int($key) )
                {
                    $model->$key = $value;
                    $model->_snapshot[$key] = $value;
                }
            }
			
			$models[] = $model;
		}
		
		return $models;
    }
    
    /**
     * NOTE: USE FETCH FIRST
     * Uses the find function but returns only the first model
     */
    public static function findFirst( $sub_sql = null, $params = null )
    {
        $models = self::find( $sub_sql, $params );
        
        return $models[0];
    }
    
    /**
     * Helper function, returns the model attributes as an array
     */
    private function getAttributes()
    {
        $attributes = array();
        
        foreach ( $this->_columns as $column )
        {
            $attributes[$column["name"]] = $this->$column["name"];
        }
        
        return $attributes;
    }
    
    /**
     * Inserts or updates the model
     */
    public function save()
    {
        /**
         * Decide if we should update or insert
         */
        if ( $this->_exists_in_db )
        {
            $query = $this->database->buildQuery( $this->_table_name, "update", array("columns" => $this->_columns) );
            
            $attributes = $this->getAttributes();
            
            // Add "snap_" prefix
            $snapshot = array();
            
            foreach ( $this->_snapshot as $key => $value )
            {
                $snapshot["snap_" . $key] = $value;
            }
            
            return $query->execute( array_merge($attributes, $snapshot) );
        }
        else
        {
            /**
             * Retrieve a query from the query builder
             */
            $query = $this->database->buildQuery( $this->_table_name, "insert", array("columns" => $this->_columns) );
            
            /**
             * Create an array with the model attributes (binding parameters)
             */
            $attributes = $this->getAttributes();
            
            /**
             * Bind the parameters and return the result
             */
            return $query->execute( $attributes );
        }
    }
}
