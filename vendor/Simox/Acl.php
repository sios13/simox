<?php

namespace Simox;

class Acl
{
	const ALLOW = "ALLOW";
	const DENY = "DENY";
	
	private $default_action;
	
	private $roles;
	private $resources;
	
	private $deny_list;
	private $allow_list;
	
	public function __construct()
	{
		$this->default_action = self::ALLOW;
		
		$this->roles = array();
		$this->resources = array();
		
		$this->allow_list = array();
		$this->deny_list = array();
	}
	
	private function _confirmParams( $role, $resource, $action )
	{
		$role_exists     = in_array( $role, $this->roles )                  ? true : false;
		$resource_exists = array_key_exists($resource, $this->resources)    ? true : false;
		$action_exists   = in_array( $action, $this->resources[$resource] ) ? true : false;
		
		if ( $role_exists && $resource_exists && $action_exists )
		{
			return true;
		}
		
		return false;
	}
	
	public function allow( $role, $resource, $action )
	{
		if ( $this->_confirmParams($role, $resource, $action) )
		{
			$this->allow_list[$role][$resource][$action] = true;
		}
	}
	
	public function deny( $role, $resource, $action )
	{
		if ( $this->_confirmParams($role, $resource, $action) )
		{
			$this->deny_list[$role][$resource][$action] = true;
		}
	}
	
	public function isAllowed( $role, $resource, $action )
	{
		if ( $this->default_action == "ALLOW" )
		{
			$list = $this->deny_list;
		}
		else if ( $this->default_action == "DENY" )
		{
			$list = $this->allow_list;
		}
        
		if ( isset($list[$role][$resource][$action])
			&& $list[$role][$resource][$action] == true )
		{
			return self::ALLOW;
		}
        
		return self::DENY;
	}
	
	public function addRole( $role )
	{
		$this->roles[] = $role;
	}
	
	public function addResource( $resource, $actions )
	{
        if ( !isset($this->resources[$resource]) )
        {
            $this->resources[$resource] = array();
        }
        
        foreach ($actions as $action)
        {
            array_push( $this->resources[$resource], $action );
        }
	}
	
	public function setDefaultAction( $default_action )
	{
		$this->default_action = $default_action;
	}
}
