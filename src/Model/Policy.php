<?php namespace God\Model;

use God\Util\Util;
use God\Config\Consts;
use God\Rbac\RoleManager;

/**
 * ------------------------------------------------------------------------------------
 * God Model Policy
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/14
 */
class Policy
{

    // ------------------------------------------------------------------------------

    /**
     * @var array
     */
    public $model = [];

    // ------------------------------------------------------------------------------

    /**
     * buildRoleLinks initializes the roles in RBAC.
     *
     * @param RoleManager $rm the role manager.
     */
    public function buildRoleLinks(RoleManager $rm) : void
    {
        if (isset($this->model[Consts::G]))
        {
            foreach ($this->model[Consts::G] as $ast)
            {
                /** @var Assertion $ast */
                $ast->buildRoleLinks($rm);
            }
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * printPolicy prints the policy to log.
     */
    public function printPolicy() : void
    {
        Util::logPrint('Policy:');

        if (isset($this->model[Consts::P]))
        {
            foreach ($this->model[Consts::P] as $key => $ast)
            {
                $policy = json_encode($ast->policy, JSON_PRETTY_PRINT);

                Util::logPrint($key .': '. $ast->value .': '. $policy);
            }
        }

        if (isset($this->model[Consts::G]))
        {
            foreach ($this->model[Consts::G] as $key => $ast)
            {
                $policy = json_encode($ast->policy, JSON_PRETTY_PRINT);

                Util::logPrint($key .': '. $ast->value .': '. $policy);
            }
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * savePolicyToText saves the policy to the text.
     *
     * @return string the policy text.
     */
    public function savePolicyToText() : string
    {
        $res = '';

        if (isset($this->model[Consts::P]))
        {
            foreach ($this->model[Consts::P] as $key => $ast)
            {
                foreach ($ast->policy as $rule)
                {
                    $val  = implode(', ', $rule);
                    $res .= "{$key}, $val\n";
                }
            }
        }

        if (isset($this->model[Consts::G]))
        {
            foreach ($this->model[Consts::G] as $key => $ast)
            {
                foreach ($ast->policy as $rule)
                {
                    $val  = implode(', ', $rule);
                    $res .= "{$key}, {$val}\n";
                }
            }
        }

        return $res;
    }

    // ------------------------------------------------------------------------------

    /**
     * clearPolicy clears all current policy.
     */
    public function clearPolicy() : void
    {
        if (isset($this->model[Consts::P]))
        {
            foreach ($this->model[Consts::P] as $ast)
            {
                /** @var Assertion $ast */
                $ast->policy = [];
            }
        }

        if (isset($this->model[Consts::G]))
        {
            foreach ($this->model[Consts::G] as $ast)
            {
                /** @var Assertion $ast */
                $ast->policy = [];
            }
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * getPolicy gets all rules in a policy.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $ptype the policy type, "p", "p2", .. or "g", "g2", ..
     * @return array the policy rules of section sec and policy type ptype.
     */
    public function getPolicy(string $sec, string $ptype) : array
    {
        return $this->model[$sec][$ptype]->policy;
    }

    // ------------------------------------------------------------------------------

    /**
     * getFilteredPolicy gets rules based on field filters from a policy.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $ptype the policy type, "p", "p2", .. or "g", "g2", ..
     * @param int $fieldIndex the policy rule's start index to be matched.
     * @param mixed ...$fieldValues the field values to be matched, value "" means not to match this field.
     * @return array the filtered policy rules of section sec and policy type ptype.
     */
    public function getFilteredPolicy(string $sec, string $ptype, int $fieldIndex, ...$fieldValues) : array
    {
        $res = [];

        foreach ($this->model[$sec][$ptype]->policy as $rule)
        {
            $matched = true;

            foreach ($fieldValues as $i => $fieldValue)
            {
                if ($fieldValue !== '' && $rule[$fieldIndex + $i] !== $fieldValue)
                {
                    $matched = false;
                    break;
                }
            }

            if ($matched) { $res[] = $rule; }
        }

        return $res;
    }

    // ------------------------------------------------------------------------------

    /**
     * hasPolicy determines whether a model has the specified policy rule.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $ptype the policy type, "p", "p2", .. or "g", "g2", ..
     * @param array $rule the policy rule.
     * @return bool whether the rule exists.
     */
    public function hasPolicy(string $sec, string $ptype, array $rule) : bool
    {
        foreach ($this->model[$sec][$ptype]->policy as $r)
        {
            if (Util::arrayEquals($rule, $r))
            {
                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------------

    /**
     * addPolicy adds a policy rule to the model.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $ptype the policy type, "p", "p2", .. or "g", "g2", ..
     * @param array $rule the policy rule.
     * @return bool succeeds or not.
     */
    public function addPolicy(string $sec, string $ptype, array $rule) : bool
    {
        if ($this->hasPolicy($sec, $ptype, $rule))
        {
            return false;
        }

        $this->model[$sec][$ptype]->policy[] = $rule;
        return true;
    }

    // ------------------------------------------------------------------------------

    /**
     * removePolicy removes a policy rule from the model.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $ptype the policy type, "p", "p2", .. or "g", "g2", ..
     * @param array $rule the policy rule.
     * @return bool succeeds or not.
     */
    public function removePolicy(string $sec, string $ptype, array $rule) : bool
    {
        $temp = $this->model[$sec][$ptype]->policy;

        foreach ($temp as $key => $r)
        {
            if (Util::arrayEquals($rule, $r))
            {
                unset($temp[$key]);

                $this->model[$sec][$ptype]->policy = array_values($temp);
                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------------

    /**
     * removeFilteredPolicy removes policy rules based on field filters from the model.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $ptype the policy type, "p", "p2", .. or "g", "g2", ..
     * @param int $fieldIndex the policy rule's start index to be matched.
     * @param mixed ...$fieldValues the field values to be matched, value ""
     *                    means not to match this field.
     * @return bool succeeds or not.
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, ...$fieldValues) : bool
    {
        $tmp = [];
        $res = false;

        foreach ($this->model[$sec][$ptype]->policy as $rule)
        {
            $matched = true;

            foreach ($fieldValues as $i => $fieldValue)
            {
                if ($fieldValue !== '' && $rule[$fieldIndex + $i] !== $fieldValue)
                {
                    $matched = false;
                    break;
                }
            }

            $matched ? $res = true : $tmp[] = $rule;
        }

        $this->model[$sec][$ptype]->policy = array_values($tmp);

        return $res;
    }

    // ------------------------------------------------------------------------------

    /**
     * getValuesForFieldInPolicy gets all values for a field for all rules in a policy, duplicated values are removed.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $ptype the policy type, "p", "p2", .. or "g", "g2", ..
     * @param int $fieldIndex the policy rule's index.
     * @return array the field values specified by fieldIndex.
     */
    public function getValuesForFieldInPolicy(string $sec, string $ptype, int $fieldIndex) : array
    {
        $values = [];

        foreach ($this->model[$sec][$ptype]->policy as $rule)
        {
            $values[] = $rule[$fieldIndex];
        }

        Util::arrayRemoveDuplicates($values);

        return $values;
    }

    // ------------------------------------------------------------------------------

}
