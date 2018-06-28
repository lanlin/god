<?php namespace God\Rbac;

use God\Util\Util;
use God\Config\Consts;
use God\Exception\GodException;
use God\Exception\GodRoleNotFound;

/**
 * ------------------------------------------------------------------------------------
 * God Default Role Manager
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/14
 */
class DefaultRoleManager implements RoleManager
{

    // ------------------------------------------------------------------------------

    /**
     * @var array
     */
    private $allRoles = [];

    // ------------------------------------------------------------------------------

    /**
     * @var int
     */
    private $maxHierarchyLevel;

    // ------------------------------------------------------------------------------

    /**
     * DefaultRoleManager is the constructor for creating an instance of the
     * default RoleManager implementation.
     *
     * @param int $maxHierarchyLevel the maximized allowed RBAC hierarchy level.
     */
    public function __construct(int $maxHierarchyLevel)
    {
        $this->maxHierarchyLevel = $maxHierarchyLevel;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $name
     * @return bool
     */
    private function hasRole(string $name) : bool
    {
        return isset($this->allRoles[$name]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $name
     * @return \God\Rbac\DefaultRole
     */
    private function createRole(string $name) : DefaultRole
    {
        if ($this->hasRole($name))
        {
            return $this->allRoles[$name];
        }

        $role = new DefaultRole($name);

        $this->allRoles[$name] = $role;

        return $role;
    }

    // ------------------------------------------------------------------------------

    /**
     * clear clears all stored data and resets the role manager to the initial state.
     */
    public function clear() : void
    {
        $this->allRoles = [];
    }

    // ------------------------------------------------------------------------------

    /**
     * addLink adds the inheritance link between role: name1 and role: name2.
     * aka role: name1 inherits role: name2.
     * domain is a prefix to the roles.
     *
     * @param string $name1
     * @param string $name2
     * @param mixed ...$domain
     */
    public function addLink(string $name1, string $name2, ...$domain) : void
    {
        $count = count($domain);

        if ($count === 1 && !empty($domain[0]))
        {
            $name1 = $domain[0] .Consts::CONFIG_SPLIT. $name1;
            $name2 = $domain[0] .Consts::CONFIG_SPLIT. $name2;
        }
        else if ($count > 1)
        {
            throw new GodException('error: domain should be 1 parameter');
        }

        $role1 = $this->createRole($name1);
        $role2 = $this->createRole($name2);

        $role1->addRole($role2);
    }

    // ------------------------------------------------------------------------------

    /**
     * deleteLink deletes the inheritance link between role: name1 and role: name2.
     * aka role: name1 does not inherit role: name2 any more.
     * domain is a prefix to the roles.
     *
     * @param string $name1
     * @param string $name2
     * @param mixed ...$domain
     * @throws \God\Exception\GodException
     */
    public function deleteLink(string $name1, string $name2, ...$domain) : void
    {
        $count = count($domain);

        if ($count === 1 && !empty($domain[0]))
        {
            $name1 = $domain[0] .Consts::CONFIG_SPLIT. $name1;
            $name2 = $domain[0] .Consts::CONFIG_SPLIT. $name2;
        }
        else if ($count > 1)
        {
            throw new GodException('error: domain should be 1 parameter');
        }

        if (!$this->hasRole($name1) || !$this->hasRole($name2))
        {
            throw new GodException('error: name1 or name2 does not exist');
        }

        $role1 = $this->createRole($name1);
        $role2 = $this->createRole($name2);

        $role1->deleteRole($role2);
    }

    // ------------------------------------------------------------------------------

    /**
     * hasLink determines whether role: name1 inherits role: name2.
     * domain is a prefix to the roles.
     *
     * @param string $name1
     * @param string $name2
     * @param mixed ...$domain
     * @return bool
     * @throws \God\Exception\GodException
     */
    public function hasLink(string $name1, string $name2, ...$domain) : bool
    {
        $count = count($domain);

        if ($count === 1 && !empty($domain[0]))
        {
            $name1 = $domain[0] .Consts::CONFIG_SPLIT. $name1;
            $name2 = $domain[0] .Consts::CONFIG_SPLIT. $name2;
        }
        else if ($count > 1)
        {
            throw new GodException('error: domain should be 1 parameter');
        }

        if ($name1 === $name2) { return true; }

        if (!$this->hasRole($name1) || !$this->hasRole($name2))
        {
            return false;
        }

        $role1 = $this->createRole($name1);

        return $role1->hasRole($name2, $this->maxHierarchyLevel);
    }

    // ------------------------------------------------------------------------------

    /**
     * getRoles gets the roles that a subject inherits.
     * domain is a prefix to the roles.
     *
     * @param string $name
     * @param mixed ...$domain
     * @return array
     * @throws \God\Exception\GodException
     */
    public function getRoles(string $name, ...$domain) : array
    {
        $count = count($domain);

        if ($count === 1 && !empty($domain[0]))
        {
            $name = $domain[0] .Consts::CONFIG_SPLIT. $name;
        }
        else if ($count > 1)
        {
            throw new GodException('error: domain should be 1 parameter');
        }

        if (!$this->hasRole($name))
        {
            throw new GodRoleNotFound('error: name does not exist');
        }

        $roles = $this->createRole($name)->getRoles();

        if ($count === 1 && !empty($domain[0]))
        {
            foreach ($roles as $i => $role)
            {
                $start = strlen($domain[0]) + 2;

                $roles[$i] = substr($role, $start);
            }
        }

        return $roles;
    }

    // ------------------------------------------------------------------------------

    /**
     * getUsers gets the users that inherits a subject.
     * domain is an unreferenced parameter here, may be used in other implementations.
     *
     * @param string $name
     * @return array
     * @throws \God\Exception\GodRoleNotFound
     */
    public function getUsers(string $name) : array
    {
        if (!$this->hasRole($name))
        {
            throw new GodRoleNotFound('error: name does not exist');
        }

        $names = [];

        foreach ($this->allRoles as $role)
        {
            if ($role->hasDirectRole($name))
            {
                $names[] = $role->name;
            }
        }

        return $names;
    }

    // ------------------------------------------------------------------------------

    /**
     * printRoles prints all the roles to log.
     */
    public function printRoles() : void
    {
        foreach ($this->allRoles as $role)
        {
            Util::logPrint( print_r($role, true) );
        }
    }

    // ------------------------------------------------------------------------------

}
