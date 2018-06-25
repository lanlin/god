<?php namespace God\Model;

use God\Util\Util;
use God\Rbac\RoleManager;
use God\Exception\GodException;

/**
 * ------------------------------------------------------------------------------------
 * God Model Assertion
 * ------------------------------------------------------------------------------------
 *
 * Assertion represents an expression in a section of the model.
 * For example: r = sub, obj, act
 *
 * @author lanlin
 * @change 2018/06/13
 */
class Assertion
{

    // ------------------------------------------------------------------------------

    /**
     * @var RoleManager
     */
    public $rm;

    // ------------------------------------------------------------------------------

    public $key;
    public $value;
    public $tokens = [];
    public $policy = [];

    // ------------------------------------------------------------------------------

    /**
     * build role links
     *
     * @param RoleManager $rm
     */
    public function buildRoleLinks(RoleManager $rm) : void
    {
        $this->rm = $rm;

         $count = 0;

        for ($i = 0; $i < strlen($this->value); $i++)
        {
            if ($this->value[$i] === '_')
            {
                $count++;
            }
        }

        foreach ($this->policy as $rule)
        {
            if ($count < 2)
            {
                $msg = 'the number of "_" in role definition should be at least 2';

                throw new GodException($msg);
            }

            if (count($rule) < $count)
            {
                $msg = 'grouping policy elements do not meet role definition';

                throw new GodException($msg);
            }

            $rules = array_slice($rule, 0, $count);

            $this->rm->addLink(...$rules);
        }

        Util::logPrint('Role links for: ' . $this->key);

        $this->rm->printRoles();
    }

    // ------------------------------------------------------------------------------

}
