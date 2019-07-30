<?php namespace God\Rbac;

/**
 * ------------------------------------------------------------------------------------
 * God Group Role Manager
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2019/07/30
 */
class GroupRoleManager extends DefaultRoleManager
{

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
        if (parent::hasLink($name1, $name2, $domain))
        {
            return true;
        }

        if (count($domain) !== 1) { return false; }

        // check name1's groups
        try
        {
            $groups = parent::getRoles($name1);

            foreach($groups as $group)
            {
                if($this->hasLink($group, $name2, $domain))
                {
                    return true;
                }
            }
        }
        catch (\Throwable $e)
        {
            return false;
        }

        // nothing found
        return false;
    }

    // ------------------------------------------------------------------------------

}
