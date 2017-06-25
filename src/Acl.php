<?php
namespace Simox;

class Acl
{
	const ALLOW = "ALLOW";
	const DENY = "DENY";

	private $_default_action;

	private $_roles;
	private $_routes;

	private $_deny_list;
	private $_allow_list;

	public function __construct()
	{
		$this->_default_action = self::ALLOW;

		$this->_roles = array();
		$this->_routes = array();

		$this->_allow_list = array();
		$this->_deny_list = array();
	}

    /**
     * Private helper function to create a route
     */
    private function _createRoute( $controller_name, $action_name )
    {
        $route = new Router\Route();

        $route->setControllerName( $controller_name );
        $route->setActionName( $action_name );

        return $route;
    }

    /**
     * Private helper function to retrieve a route
     */
    private function _getRoute( $controller_name, $action_name )
    {
        $_route = $this->_createRoute( $controller_name, $action_name );

        foreach( $this->_routes as $route )
        {
            if ( $route->getControllerName() == $_route->getControllerName() && $route->getActionName() == $_route->getActionName() )
            {
                return $route;
            }
        }
    }

    /**
     * Sets the default action. ALLOW or DENY.
     */
	public function setDefaultAction( $default_action )
	{
		$this->_default_action = $default_action;
	}

	public function addRole( $role_name )
	{
		$this->roles[] = $role_name;
	}

	public function addRoutes( $controller_name, $action_names )
	{
        foreach ( $action_names as $action_name )
        {
            $route = $this->_createRoute( $controller_name, $action_name );

            $this->_routes[] = $route;
        }
	}

    /**
     * Attach a route with a role in the allow list
     */
	public function allow( $role, $controller_name, $action_name )
	{
        $route = $this->_getRoute( $controller_name, $action_name );

        $this->_allow_list[$role][] = $route;

	}

    /**
     * Attach a route with a role in the deny list
     */
	public function deny( $role, $resource, $action )
	{
        $route = $this->_getRoute( $controller_name, $action_name );

        $this->_deny_list[$role][] = $route;
	}

    /**
     * Check is role has the route attached
     */
	public function isAllowed( $role, $controller_name, $action_name )
	{
        $_route = $this->_createRoute( $controller_name, $action_name );

		if ( $this->_default_action == "ALLOW" )
		{
			$list = $this->_deny_list;
		}
		else if ( $this->_default_action == "DENY" )
		{
			$list = $this->_allow_list;
		}

        foreach ( $list[$role] as $route )
        {
            if ( $route->getControllerName() == $_route->getControllerName() && $route->getActionName() == $_route->getActionName() )
            {
                return self::ALLOW;
            }
        }

        return self::DENY;
	}
}
