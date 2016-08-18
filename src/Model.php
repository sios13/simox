<?php
namespace Simox;

abstract class Model
{
    /**
     * Name of the table attached to this model
     */
    private $_table_name;
    
    /**
     * Array with an description of the table columns
     */
    private $_columns;
    
    /**
     * Array with the original attributes of the model
     */
    private $_snapshot;
    
    /**
     * Is true if model exists in db table
     * Is set to true when a model is created by the find function
     */
    private $_exists_in_db;

    public function __construct()
    {
        $this->_table_name = get_called_class();
        
        $this->_columns = $this->getColumns();
        
        $this->_snapshot = array();
        
        $this->_exists_in_db = false;
    }
    
    public function setExists( $value )
    {
        $this->_exists_in_db = $value;
    }
    
    /**
     * Helper function, returns an array describing the model columns
     */
    private function getColumns()
    {
        $columns = array();
        
        /**
         * Use call_user_func to make sure only public vars are returned
         */
        $vars = call_user_func('get_object_vars', $this);
        
        foreach ( $vars as $key => $value )
        {
            $columns[] = array(
                "name" => $key
            );
        }
        
        return $columns;
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
     * Helper function to create and initialize a model
     */
    private static function createModel( $row )
    {
        $table_name = get_called_class();
        
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
        
        return $model;
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
        $di = DI::getDefault();

        /**
         * Load parameters form $params
         */
        $bind = isset($params["bind"]) ? $params["bind"] : null;
        $cache = isset($params["cache"]) ? $params["cache"] : null;
        $find_first = isset($params["find_first"]) ? $params["find_first"] : null;
        
        $table_name = get_called_class();
        
        /**
         * Build the query, bind and fetch the resultset
         */
        $query = $di->getService("database")->buildQuery( $table_name, "find", array("sub_sql" => $sub_sql) );
        
        $query->execute( $bind );
        
        if ( $find_first )
        {
            $row = $query->fetch( \PDO::FETCH_ASSOC );
            
            if ( $row == false )
            {
                return false;
            }
            
            $model = self::createModel( $row );
            
            return $model;
        }
        
        $resultset = $query->fetchAll( \PDO::FETCH_ASSOC );
        
        if ( $resultset == false )
        {
            return false;
        }
        
        $models = array();
        
        foreach ( $resultset as $row )
        {
            $model = self::createModel( $row );
            
            $models[] = $model;
        }
        
        return $models;
    }
    
    /**
     * Returns only the first model
     */
    public static function findFirst( $sub_sql = null, $params = null )
    {
        $params["find_first"] = true;
        
        return self::find( $sub_sql, $params );
    }
    
    /**
     * Inserts or updates the database table
     */
    public function save()
    {
        $di = DI::getDefault();

        $database = $di->getService( "database" );

        /**
         * Decide if we should update or insert
         * (if we know the record exists in the database...)
         */
        if ( $this->_exists_in_db )
        {
            /**
             * Retrieve a query from the query builder
             */
            $query = $database->buildQuery( $this->_table_name, "update", array("columns" => $this->_columns) );
            
            /**
             * Create an array with the model attributes (binding parameters)
             */
            $attributes = $this->getAttributes();
            
            /**
             * Add "snap_" prefix to the snapshot in order to not get index conflict when we merge attributes and snapshot
             */
            $snapshot = array();
            
            foreach ( $this->_snapshot as $key => $value )
            {
                $snapshot["snap_" . $key] = $value;
            }
            
            /**
             * Merge our binding parameters.
             * Bind the parameters to the query and return the result.
             */
            return $query->execute( array_merge($attributes, $snapshot) );
        }
        else
        {
            /**
             * Retrieve a query from the query builder
             */
            $query = $database->buildQuery( $this->_table_name, "insert", array("columns" => $this->_columns) );
            
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
