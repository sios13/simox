<?php

use Simox\Plugin;
use Simox\Dispatcher;
use Simox\Acl as AclList;

class SecurityPlugin extends Plugin
{
	public function beforeDispatch( $dispatcher )
	{
        // Check whether the "auth" variable exists in session to define the active role
        $auth = $this->session->get("auth");
        if (!$auth) {
            $role = "guests";
        } else {
            $role = "users";
        }

        // Take the active controller/action from the dispatcher
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();
		
        // Obtain the ACL list
        $acl = $this->getAcl();
		
        // Check if the Role have access to the controller (resource)
        $allowed = $acl->isAllowed($role, $controller, $action);
        if ($allowed == AclList::DENY) {
			
            // If he does not have access, forward him to the index controller
            $this->flash->error("You don't have access to this module");
            return $dispatcher->forward(
                array(
                    "controller" => "index",
                    "action"     => "index"
                )
            );
        }
	}
	
	public function getAcl()
	{
		// Create the ACL
		$acl = new AclList();

		$acl->setDefaultAction( AclList::DENY );

		//Register roles
		$roles = array("users", "guests");
		
		foreach ($roles as $role) {
			$acl->addRole($role);
		}

		//Private area resources
		$privateResources = array(
			"index" => array("hemligt")
		);
		foreach ($privateResources as $resource => $actions) {
			$acl->addResource($resource, $actions);
		}

		//Public area resources
		$publicResources = array(
			"index" => array("index", "login"),
			"project" => array("simox")
		);
		foreach ($publicResources as $resource => $actions) {
			$acl->addResource($resource, $actions);
		}

		//Grant access to public areas to both users and guests
		foreach ($roles as $role){
			foreach ($publicResources as $resource => $actions) {
				foreach ($actions as $action)
				{
					$acl->allow($role, $resource, $action);
				}
			}
		}

		//Grant access to private area to role Users
		foreach ($privateResources as $resource => $actions) {
			foreach ($actions as $action) {
				$acl->allow("users", $resource, $action);
			}
		}
		
		return $acl;
	}
}
