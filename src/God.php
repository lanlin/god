<?php namespace God;

use God\Config\Consts;
use God\Exception\GodException;
use God\Exception\GodRoleNotFound;
use God\Model\Model;
use God\Model\FunctionMap;
use God\Enforcer\Management;
use God\Persist\Adapter;
use God\Persist\Adapter\File\Adapter as FileAdapter;

/**
 * ------------------------------------------------------------------------------------
 * God Enforcer
 * ------------------------------------------------------------------------------------
 *
 * "God said, Let there be light, and there was light."
 * "God will decide if you have permission."
 * "God bless you!"
 *
 * @author lanlin
 * @change 2018/06/15
 */
class God extends Management
{

    // ------------------------------------------------------------------------------

    /**
     * load policy when inited?
     *
     * @var bool
     */
    private $loadPolicy = true;

    // ------------------------------------------------------------------------------

    /**
     * Enforcer constructor.
     *
     * @usage
     *        God()
     *        God(string $modelPath)
     *        God(string $modelPath, string $policyFile)
     *        God(string $modelPath, Adapter $adapter)
     *        God(Model $model)
     *        God(Model $model, Adapter $adapter)
     *
     * @param mixed ...$params
     */
    public function __construct(...$params)
    {
        $size = count($params ?? []);

        if ($size && is_bool(end($params)))
        {
            $size--;
            $this->enableLog(end($params));
        }

        if ($size === 0)
        {
            $this->initWithFile('', '');
            return;
        }

        if ($size === 1)
        {
            is_string($params[0]) ?
            $this->initWithFile($params[0], '') :
            $this->initWithModelAndAdapter($params[0]);
            return;
        }

        if ($size === 2)
        {
            $bothStr = is_string($params[0]) && is_string($params[1]);
            $bothObj = is_object($params[0]) && is_object($params[1]);

            $bothStr === true     AND $this->initWithFile(...$params);
            $bothObj === true     AND $this->initWithModelAndAdapter(...$params);
            $bothStr === $bothObj AND $this->initWithAdapter(...$params);
            return;
        }

        throw new GodException('invalid params.');
    }

    // ------------------------------------------------------------------------------

    /**
     * getRolesForUser gets the roles that a user has.
     *
     * @param string $name the user.
     * @return array the roles that the user has.
     */
    public function getRolesForUser(string $name) : array
    {
        try
        {
            return $this->model->model[Consts::G][Consts::G]->rm->getRoles($name);
        }
        catch (GodRoleNotFound $e)
        {
            return [];
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * getUsersForRole gets the users that has a role.
     *
     * @param string $name the role.
     * @return array the users that has the role.
     */
    public function getUsersForRole(string $name) : array
    {
        try
        {
            return $this->model->model[Consts::G][Consts::G]->rm->getUsers($name);
        }
        catch (GodRoleNotFound $e)
        {
            return [];
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * hasRoleForUser determines whether a user has a role.
     *
     * @param string $name the user.
     * @param string $role the role.
     * @return bool whether the user has the role.
     */
    public function hasRoleForUser(string $name, string $role) : bool
    {
        $roles = $this->getRolesForUser($name);

        $hasRole = false;

        foreach ($roles as $r)
        {
            if ($r === $role)
            {
                $hasRole = true;
                break;
            }
        }

        return $hasRole;
    }

    // ------------------------------------------------------------------------------

    /**
     * addRoleForUser adds a role for a user.
     * Returns false if the user already has the role (aka not affected).
     *
     * @param string $user the user.
     * @param string $role the role.
     * @return bool succeeds or not.
     */
    public function addRoleForUser(string $user, string $role) : bool
    {
        return $this->addGroupingPolicy($user, $role);
    }

    // ------------------------------------------------------------------------------

    /**
     * deleteRoleForUser deletes a role for a user.
     * Returns false if the user does not have the role (aka not affected).
     *
     * @param string $user the user.
     * @param string $role the role.
     * @return bool succeeds or not.
     */
    public function deleteRoleForUser(string $user, string $role)
    {
        return $this->removeGroupingPolicy($user, $role);
    }

    // ------------------------------------------------------------------------------

    /**
     * deleteRolesForUser deletes all roles for a user.
     * Returns false if the user does not have any roles (aka not affected).
     *
     * @param string $user the user.
     * @return bool succeeds or not.
     */
    public function deleteRolesForUser(string $user) : bool
    {
        return $this->removeFilteredGroupingPolicy(0, $user);
    }

    // ------------------------------------------------------------------------------

    /**
     * deleteUser deletes a user.
     * Returns false if the user does not exist (aka not affected).
     *
     * @param string $user the user.
     * @return bool succeeds or not.
     */
    public function deleteUser(string $user) : bool
    {
        return $this->removeFilteredGroupingPolicy(0, $user);
    }

    // ------------------------------------------------------------------------------

    /**
     * deleteRole deletes a role.
     *
     * @param string $role the role.
     */
    public function deleteRole(string $role) : void
    {
        $this->removeFilteredGroupingPolicy(1, $role);
        $this->removeFilteredPolicy(0, $role);
    }

    // ------------------------------------------------------------------------------

    /**
     * deletePermission deletes a permission.
     * Returns false if the permission does not exist (aka not affected).
     *
     * @usage deletePermission(string...) or deletePermission(array)
     * @param mixed ...$permission the permission, usually be (obj, act).
     *                              It is actually the rule without the subject.
     * @return bool succeeds or not.
     */
    public function deletePermission(...$permission) : bool
    {
        $permission = is_array($permission[0]) ? $permission[0] : $permission;

        return $this->removeFilteredPolicy(1, ...$permission);
    }

    // ------------------------------------------------------------------------------

    /**
     * addPermissionForUser adds a permission for a user or role.
     * Returns false if the user or role already has the permission (aka not affected).
     *
     * @usage addPermissionForUser(string, string...) or addPermissionForUser(string, array)
     * @param string $user the user.
     * @param mixed ...$permission the permission, usually be (obj, act). It is actually the rule without the subject.
     * @return bool succeeds or not.
     */
    public function addPermissionForUser(string $user, ...$permission) : bool
    {
        $permission = is_array($permission[0]) ? $permission[0] : $permission;

        $policy = array_merge([$user], $permission);

        return $this->addPolicy($policy);
    }

    // ------------------------------------------------------------------------------

    /**
     * deletePermissionForUser deletes a permission for a user or role.
     * Returns false if the user or role does not have the permission (aka not affected).
     *
     * @usage deletePermissionForUser(string, string...) or deletePermissionForUser(string, array)
     * @param string $user the user.
     * @param mixed ...$permission the permission, usually be (obj, act). It is actually the rule without the subject.
     * @return bool succeeds or not.
     */
    public function deletePermissionForUser(string $user, ...$permission) : bool
    {
        $permission = is_array($permission[0]) ? $permission[0] : $permission;

        $policy = array_merge([$user], $permission);

        return $this->removePolicy($policy);
    }

    // ------------------------------------------------------------------------------

    /**
     * deletePermissionsForUser deletes permissions for a user or role.
     * Returns false if the user or role does not have any permissions (aka not affected).
     *
     * @param string $user the user.
     * @return bool succeeds or not.
     */
    public function deletePermissionsForUser(string $user) : bool
    {
        return $this->removeFilteredPolicy(0, $user);
    }

    // ------------------------------------------------------------------------------

    /**
     * getPermissionsForUser gets permissions for a user or role.
     *
     * @param string $user the user.
     * @return array the permissions, a permission is usually like (obj, act). It is actually the rule without the subject.
     */
    public function getPermissionsForUser(string $user) : array
    {
        return $this->getFilteredPolicy(0, $user);
    }

    // ------------------------------------------------------------------------------

    /**
     * hasPermissionForUser determines whether a user has a permission.
     *
     * @usage hasPermissionForUser(string, string...) or hasPermissionForUser(string, array)
     * @param string $user the user.
     * @param mixed ...$permission the permission, usually be (obj, act). It is actually the rule without the subject.
     * @return bool whether the user has the permission.
     */
    public function hasPermissionForUser(string $user, ...$permission) : bool
    {
        $permission = is_array($permission[0]) ? $permission[0] : $permission;

        $policy = array_merge([$user], $permission);

        return $this->hasPolicy($policy);
    }

    // ------------------------------------------------------------------------------

    /**
     * getRolesForUserInDomain gets the roles that a user has inside a domain.
     *
     * @param string $name the user.
     * @param string $domain the domain.
     * @return array the roles that the user has in the domain.
     */
    public function getRolesForUserInDomain(string $name, string $domain) : array
    {
        try
        {
            return $this->model->model[Consts::G][Consts::G]->rm->getRoles($name, $domain);
        }
        catch (GodRoleNotFound $e)
        {
            return [];
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * getPermissionsForUserInDomain gets permissions for a user or role inside a domain.
     *
     * @param string $user the user.
     * @param string $domain the domain.
     * @return array the permissions, a permission is usually like (obj, act). It is actually the rule without the subject.
     */
    public function getPermissionsForUserInDomain(string $user, string $domain) : array
    {
        return $this->getFilteredPolicy(0, $user, $domain);
    }

    // ------------------------------------------------------------------------------

    /**
     * addRoleForUserInDomain adds a role for a user inside a domain.
     * Returns false if the user already has the role (aka not affected).
     *
     * @param string $user the user.
     * @param string $role the role.
     * @param string $domain the role.
     * @return bool succeeds or not.
     */
    public function addRoleForUserInDomain(string $user, string $role, string $domain) : bool
    {
        return $this->addGroupingPolicy($user, $role, $domain);
    }

    // ------------------------------------------------------------------------------

    /**
     * deleteRoleForUserInDomain deletes a role for a user inside a domain.
     * Returns false if the user does not have the role (aka not affected).
     *
     * @param string $user the user.
     * @param string $role the role.
     * @param string $domain the domain.
     * @return bool
     */
    public function deleteRoleForUserInDomain(string $user, string $role, string $domain) : bool
    {
        return $this->removeGroupingPolicy($user, $role, $domain);
    }

    // ------------------------------------------------------------------------------

    /**
     * initializes with a model file and a policy file
     *
     * @param string $modelPath
     * @param string $policyPath
     */
    private function initWithFile(string $modelPath, string $policyPath)
    {
        if (!$policyPath)
        {
            $this->loadPolicy = false;
        }

        $this->initWithAdapter($modelPath, new FileAdapter($policyPath));
    }

    // ------------------------------------------------------------------------------

    /**
     * initializes with a adapter
     *
     *
     * @param string                            $modelPath
     * @param \God\Persist\Adapter $adapter
     */
    private function initWithAdapter(string $modelPath, Adapter $adapter)
    {
        $this->modelPath = $modelPath;

        $model = $this->newModel($modelPath, '');

        $this->initWithModelAndAdapter($model, $adapter);
    }

    // ------------------------------------------------------------------------------

    /**
     * initializes with a model and a database adapter
     *
     * @param \God\Model\Model     $model
     * @param \God\Persist\Adapter $adapter
     */
    private function initWithModelAndAdapter(Model $model, Adapter $adapter = null)
    {
        $this->model   = $model;
        $this->adapter = $adapter;

        $this->model->printModel();

        $this->fm = FunctionMap::loadFunctionMap();

        $this->initialize();

        if ($this->loadPolicy && $this->adapter)
        {
            $this->loadPolicy();
        }
    }

    // ------------------------------------------------------------------------------

}
