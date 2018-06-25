<?php namespace God\Rbac;

use God\Config\Consts;

/**
 * ----------------------------------------------------------------------------------
 * God RBAC Role
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/6/14
 */
class DefaultRole
{

    // ------------------------------------------------------------------------------

    public $name;

    // ------------------------------------------------------------------------------

    /**
     * @var array
     */
    private $roles = [];

    // ------------------------------------------------------------------------------

    /**
     * DefaultRole constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param \God\Rbac\DefaultRole $role
     */
    public function addRole(DefaultRole $role) : void
    {
        foreach ($this->roles as $r)
        {
            if ($r->name === $role->name)
            {
                return;
            }
        }

        $this->roles[] = $role;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param \God\Rbac\DefaultRole $role
     */
    public function deleteRole(DefaultRole $role) : void
    {
        foreach ($this->roles as $k => $r)
        {
            if ($r->name === $role->name)
            {
                unset($this->roles[$k]);
            }
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $name
     * @param int    $hierarchyLevel
     * @return bool
     */
    public function hasRole(string $name, int $hierarchyLevel) : bool
    {
        if ($this->name === $name) { return true; }

        if ($hierarchyLevel <= 0) { return false; }

        foreach ($this->roles as $role)
        {
            if ($role->hasRole($name, $hierarchyLevel - 1))
            {
                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $name
     * @return bool
     */
    public function hasDirectRole(string $name) : bool
    {
        foreach ($this->roles as $r)
        {
            if ($r->name === $name)
            {
                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function toString() : string
    {
        $names = '';

        foreach ($this->roles as $i => $role)
        {
            $names .= $i === 0 ? $role->name : Consts::IMPLODE_DELIMITER. $role->name;
        }

        return $this->name .' < '. $names;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    public function getRoles() : array
    {
        $names = [];

        foreach ($this->roles as $r)
        {
            $names[] = $r->name;
        }

        return $names;
    }

    // ------------------------------------------------------------------------------
}
