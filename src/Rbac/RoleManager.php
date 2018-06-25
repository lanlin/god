<?php namespace God\Rbac;

/**
 * ------------------------------------------------------------------------------------
 * God Role Manager Interface
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/14
 */
interface RoleManager
{

    // ------------------------------------------------------------------------------

    /**
     * Clear clears all stored data and resets the role manager to the initial state.
     */
    public function clear() : void;

    // ------------------------------------------------------------------------------

    /**
     * addLink adds the inheritance link between two roles. role: name1 and role: name2.
     * domain is a prefix to the roles.
     *
     * @param string $name1 the first role (or user).
     * @param string $name2 the second role.
     * @param mixed ...$domain the domain the roles belong to.
     */
    public function addLink(string $name1, string $name2, ...$domain) : void;

    // ------------------------------------------------------------------------------

    /**
     * deleteLink deletes the inheritance link between two roles. role: name1 and role: name2.
     * domain is a prefix to the roles.
     *
     * @param string $name1 the first role (or user).
     * @param string $name2 the second role.
     * @param mixed ...$domain the domain the roles belong to.
     */
    public function deleteLink(string $name1, string $name2, ...$domain) : void;

    // ------------------------------------------------------------------------------

    /**
     * hasLink determines whether a link exists between two roles. role: name1 inherits role: name2.
     * domain is a prefix to the roles.
     *
     * @param string $name1 the first role (or a user).
     * @param string $name2 the second role.
     * @param mixed ...$domain the domain the roles belong to.
     * @return bool whether name1 inherits name2 (name1 has role name2).
     */
    public function hasLink(string $name1, string $name2, ...$domain) : bool;

    // ------------------------------------------------------------------------------

    /**
     * getRoles gets the roles that a user inherits.
     * domain is a prefix to the roles.
     *
     * @param string $name the user (or a role).
     * @param mixed ...$domain the domain the roles belong to.
     * @return array the roles.
     */
    public function getRoles(string $name, ...$domain) : array;

    // ------------------------------------------------------------------------------

    /**
     * getUsers gets the users that inherits a role.
     *
     * @param string $name the role.
     * @return array the users.
     */
    public function getUsers(string $name) : array;

    // ------------------------------------------------------------------------------

    /**
     * printRoles prints all the roles to log.
     */
    public function printRoles() : void;

    // ------------------------------------------------------------------------------

}
