<?php namespace God\Enforcer;

use God\Config\Consts;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * ------------------------------------------------------------------------------------
 * God Management Enforcer
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/15
 */
class Management extends Internal
{

    // ------------------------------------------------------------------------------

    /**
     * getAllSubjects gets the list of subjects that show up in the current policy.
     *
     * @return array all the subjects in "p" policy rules. It actually collects the
     *         0-index elements of "p" policy rules. So make sure your subject
     *         is the 0-index element, like (sub, obj, act). Duplicates are removed.
     */
    public function getAllSubjects() : array
    {
        return $this->getAllNamedSubjects(Consts::P);
    }

    // ------------------------------------------------------------------------------

    /**
     * GetAllNamedSubjects gets the list of subjects that show up in the currentnamed policy.
     *
     * @param string $ptype the policy type, can be "p", "p2", "p3", ..
     * @return array all the subjects in policy rules of the ptype type. It actually
     *         collects the 0-index elements of the policy rules. So make sure
     *         your subject is the 0-index element, like (sub, obj, act).
     *         Duplicates are removed.
     */
    public function getAllNamedSubjects(string $ptype) : array
    {
        return $this->model->getValuesForFieldInPolicy(Consts::P, $ptype, 0);
    }

    // ------------------------------------------------------------------------------

    /**
     * getAllObjects gets the list of objects that show up in the current policy.
     *
     * @return array all the objects in "p" policy rules. It actually collects the
     *         1-index elements of "p" policy rules. So make sure your object
     *         is the 1-index element, like (sub, obj, act).
     *         Duplicates are removed.
     */
    public function getAllObjects() : array
    {
        return $this->getAllNamedObjects(Consts::P);
    }

    // ------------------------------------------------------------------------------

    /**
     * getAllNamedObjects gets the list of objects that show up in the current named policy.
     *
     * @param string $ptype the policy type, can be "p", "p2", "p3", ..
     * @return array all the objects in policy rules of the ptype type. It actually
     *         collects the 1-index elements of the policy rules. So make sure
     *         your object is the 1-index element, like (sub, obj, act).
     *         Duplicates are removed.
     */
    public function getAllNamedObjects(string $ptype) : array
    {
        return $this->model->getValuesForFieldInPolicy(Consts::P, $ptype, 1);
    }

    // ------------------------------------------------------------------------------

    /**
     * getAllActions gets the list of actions that show up in the current policy.
     *
     * @return array all the actions in "p" policy rules. It actually collects
     *         the 2-index elements of "p" policy rules. So make sure your action
     *         is the 2-index element, like (sub, obj, act).
     *         Duplicates are removed.
     */
    public function getAllActions() : array
    {
        return $this->getAllNamedActions(Consts::P);
    }

    // ------------------------------------------------------------------------------

    /**
     * GetAllNamedActions gets the list of actions that show up in the current named policy.
     *
     * @param string $ptype the policy type, can be "p", "p2", "p3", ..
     * @return array all the actions in policy rules of the ptype type. It actually
     *         collects the 2-index elements of the policy rules. So make sure
     *         your action is the 2-index element, like (sub, obj, act).
     *         Duplicates are removed.
     */
    public function getAllNamedActions(string $ptype) : array
    {
        return $this->model->getValuesForFieldInPolicy(Consts::P, $ptype, 2);
    }

    // ------------------------------------------------------------------------------

    /**
     * getAllRoles gets the list of roles that show up in the current policy.
     *
     * @return array all the roles in "g" policy rules. It actually collects
     *         the 1-index elements of "g" policy rules. So make sure your
     *         role is the 1-index element, like (sub, role).
     *         Duplicates are removed.
     */
    public function getAllRoles() : array
    {
        return $this->getAllNamedRoles(Consts::G);
    }

    // ------------------------------------------------------------------------------

    /**
     * getAllNamedRoles gets the list of roles that show up in the current named policy.
     *
     * @param string $ptype the policy type, can be "g", "g2", "g3", ..
     * @return array all the subjects in policy rules of the ptype type. It actually
     *         collects the 0-index elements of the policy rules. So make
     *         sure your subject is the 0-index element, like (sub, obj, act).
     *         Duplicates are removed.
     */
    public function getAllNamedRoles(string $ptype) : array
    {
        return $this->model->getValuesForFieldInPolicy(Consts::G, $ptype, 1);
    }

    // ------------------------------------------------------------------------------

    /**
     * getPolicy gets all the authorization rules in the policy.
     *
     * @return array all the "p" policy rules.
     */
    public function getPolicy() : array
    {
        return $this->getNamedPolicy(Consts::P);
    }

    // ------------------------------------------------------------------------------

    /**
     * getFilteredPolicy gets all the authorization rules in the policy, field filters can be specified.
     *
     * @param int $fieldIndex the policy rule's start index to be matched.
     * @param mixed ...$fieldValues the field values to be matched, value ""
     *                    means not to match this field.
     * @return array the filtered "p" policy rules.
     */
    public function getFilteredPolicy(int $fieldIndex, ...$fieldValues) : array
    {
        $fieldValues = is_array($fieldValues[0]) ? $fieldValues[0] : $fieldValues;

        return $this->getFilteredNamedPolicy(Consts::P, $fieldIndex, ...$fieldValues);
    }

    // ------------------------------------------------------------------------------

    /**
     * getNamedPolicy gets all the authorization rules in the named policy.
     *
     * @param string $ptype the policy type, can be "p", "p2", "p3", ..
     * @return array the "p" policy rules of the specified ptype.
     */
    public function getNamedPolicy(string $ptype) : array
    {
        return $this->model->getPolicy(Consts::P, $ptype);
    }

    // ------------------------------------------------------------------------------

    /**
     * getFilteredNamedPolicy gets all the authorization rules in the named policy, field filters can be specified.
     *
     * @param string $ptype the policy type, can be "p", "p2", "p3", ..
     * @param int $fieldIndex the policy rule's start index to be matched.
     * @param mixed ...$fieldValues the field values to be matched, value ""
     *                    means not to match this field.
     * @return array the filtered "p" policy rules of the specified ptype.
     */
    public function getFilteredNamedPolicy(string $ptype, int $fieldIndex, ...$fieldValues) : array
    {
        $fieldValues = is_array($fieldValues[0]) ? $fieldValues[0] : $fieldValues;

        return $this->model->getFilteredPolicy(Consts::P, $ptype, $fieldIndex, ...$fieldValues);
    }

    // ------------------------------------------------------------------------------

    /**
     * getGroupingPolicy gets all the role inheritance rules in the policy.
     *
     * @return array all the "g" policy rules.
     */
    public function getGroupingPolicy() : array
    {
        return $this->getNamedGroupingPolicy(Consts::G);
    }

    // ------------------------------------------------------------------------------

    /**
     * getFilteredGroupingPolicy gets all the role inheritance rules in the policy, field filters can be specified.
     *
     * @param int $fieldIndex the policy rule's start index to be matched.
     * @param mixed ...$fieldValues the field values to be matched, value ""
                          means not to match this field.
     * @return array the filtered "g" policy rules.
     */
    public function getFilteredGroupingPolicy(int $fieldIndex, ...$fieldValues) : array
    {
        $fieldValues = is_array($fieldValues[0]) ? $fieldValues[0] : $fieldValues;

        return $this->getFilteredNamedGroupingPolicy(Consts::G, $fieldIndex, ...$fieldValues);
    }

    // ------------------------------------------------------------------------------

    /**
     * getNamedGroupingPolicy gets all the role inheritance rules in the policy.
     *
     * @param string $ptype the policy type, can be "g", "g2", "g3", ..
     * @return array the "g" policy rules of the specified ptype.
     */
    public function getNamedGroupingPolicy(string $ptype) : array
    {
        return $this->model->getPolicy(Consts::G, $ptype);
    }

    // ------------------------------------------------------------------------------

    /**
     * getFilteredNamedGroupingPolicy gets all the role inheritance rules in the policy, field filters can be specified.
     *
     * @param string $ptype the policy type, can be "g", "g2", "g3", ..
     * @param int $fieldIndex the policy rule's start index to be matched.
     * @param mixed ...$fieldValues the field values to be matched, value ""
     *                    means not to match this field.
     * @return array the filtered "g" policy rules of the specified ptype.
     */
    public function getFilteredNamedGroupingPolicy(string $ptype, int $fieldIndex, ...$fieldValues) : array
    {
        $fieldValues = is_array($fieldValues[0]) ? $fieldValues[0] : $fieldValues;

        return $this->model->getFilteredPolicy(Consts::G, $ptype, $fieldIndex, ...$fieldValues);
    }

    // ------------------------------------------------------------------------------

    /**
     * hasPolicy determines whether an authorization rule exists.
     *
     * @usage hasPolicy(array) or hasPolicy(string...)
     * @param  mixed ...$params the "p" policy rule, ptype "p" is implicitly used.
     * @return bool whether the rule exists.
     */
    public function hasPolicy(...$params) : bool
    {
        $params = is_array($params[0]) ? $params[0] : $params;

        return $this->hasNamedPolicy(Consts::P, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * hasNamedPolicy determines whether a named authorization rule exists.
     *
     * @usage hasNamedPolicy(string, array) or hasNamedPolicy(string, string...)
     * @param string ptype the policy type, can be "p", "p2", "p3", ..
     * @param mixed ...$params the "p" policy rule.
     * @return bool whether the rule exists.
     */
    public function hasNamedPolicy(string $ptype, ...$params) : bool
    {
        $params = is_array($params[0]) ? $params[0] : $params;

        return $this->model->hasPolicy(Consts::P, $ptype, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * addPolicy adds an authorization rule to the current policy.
     * If the rule already exists, the function returns false and the rule will not be added.
     * Otherwise the function returns true by adding the new rule.
     *
     * @usage addPolicy(string...) or addPolicy(array) or addPolicy(string, string, array)
     * @param mixed ...$params the "p" policy rule, ptype "p" is implicitly used.
     * @return bool succeeds or not.
     */
    public function addPolicy(...$params) : bool
    {
        if (count($params) > 2 && is_array(end($params)))
        {
            return $this->addPolicyInternal(...$params);
        }

        $params = is_array($params[0]) ? $params[0] : $params;

        return $this->addNamedPolicy(Consts::P, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * AddNamedPolicy adds an authorization rule to the current named policy.
     * If the rule already exists, the function returns false and the rule will not be added.
     * Otherwise the function returns true by adding the new rule.
     *
     * @usage addNamedPolicy(string, array) or addNamedPolicy(string, string...)
     * @param string $ptype the policy type, can be "p", "p2", "p3", ..
     * @param mixed ...$params the "p" policy rule.
     * @return bool succeeds or not.
     */
    public function addNamedPolicy(string $ptype, ...$params) : bool
    {
        $params = is_array($params[0]) ? $params[0] : $params;

        return $this->addPolicy(Consts::P, $ptype, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * removePolicy removes an authorization rule from the current policy.
     *
     * @usage removePolicy(string...) or removePolicy(array) or removePolicy(string, string, array)
     * @param mixed ...$params the "p" policy rule, ptype "p" is implicitly used.
     * @return bool succeeds or not.
     */
    public function removePolicy(...$params) : bool
    {
        if (count($params) > 2 && is_array(end($params)))
        {
            return $this->removePolicyInternal(...$params);
        }

        $params = is_array($params[0]) ? $params[0] : $params;

        return $this->removeNamedPolicy(Consts::P, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * removeFilteredPolicy removes an authorization rule from the current policy, field filters can be specified.
     *
     * @usage removeFilteredPolicy(int, string...) or removeFilteredPolicy(string, string, int, string...)
     * @param mixed ...$params
     * @return bool succeeds or not.
     */
    public function removeFilteredPolicy(...$params) : bool
    {
        if (is_string($params[0]))
        {
            return $this->removeFilteredPolicyInternal(...$params);
        }

        $temp = array_slice($params, 1);

        return $this->removeFilteredNamedPolicy(Consts::P, $params[0], ...$temp);
    }

    // ------------------------------------------------------------------------------

    /**
     * removeNamedPolicy removes an authorization rule from the current named policy.
     *
     * @usage removeNamedPolicy(string, string...) or removeNamedPolicy(string, array)
     * @param string $ptype the policy type, can be "p", "p2", "p3", ..
     * @param mixed ...$params the "p" policy rule.
     * @return bool succeeds or not.
     */
    public function removeNamedPolicy(string $ptype, ...$params) : bool
    {
        $params = is_array($params[0]) ? $params[0] : $params;

        return $this->removePolicy(Consts::P, $ptype, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * removeFilteredNamedPolicy removes an authorization rule from the current named policy, field filters can be specified.
     *
     * @param string $ptype the policy type, can be "p", "p2", "p3", ..
     * @param int $fieldIndex the policy rule's start index to be matched.
     * @param mixed ...$fieldValues the field values to be matched, value ""
     *                    means not to match this field.
     * @return bool succeeds or not.
     */
    public function removeFilteredNamedPolicy(string $ptype, int $fieldIndex, ...$fieldValues) : bool
    {
        $fieldValues = is_array($fieldValues[0]) ? $fieldValues[0] : $fieldValues;

        return $this->removeFilteredPolicy(Consts::P, $ptype, $fieldIndex, ...$fieldValues);
    }

    // ------------------------------------------------------------------------------

    /**
     * hasGroupingPolicy determines whether a role inheritance rule exists.
     *
     * @usage hasGroupingPolicy(string...) or hasGroupingPolicy(array)
     * @param mixed ...$params the "g" policy rule, ptype "g" is implicitly used.
     * @return bool whether the rule exists.
     */
    public function hasGroupingPolicy(...$params) : bool
    {
        $params = is_array($params[0]) ? $params[0] : $params;

        return $this->hasNamedGroupingPolicy(Consts::G, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * hasNamedGroupingPolicy determines whether a named role inheritance rule exists.
     *
     * @usage hasNamedGroupingPolicy(string, string...) or hasNamedGroupingPolicy(string, array)
     * @param string $ptype the policy type, can be "g", "g2", "g3", ..
     * @param mixed ...$params the "g" policy rule.
     * @return bool whether the rule exists.
     */
    public function hasNamedGroupingPolicy(string $ptype, ...$params) : bool
    {
        $params = is_array($params[0]) ? $params[0] : $params;

        return $this->model->hasPolicy(Consts::G, $ptype, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * addGroupingPolicy adds a role inheritance rule to the current policy.
     * If the rule already exists, the function returns false and the rule will not be added.
     * Otherwise the function returns true by adding the new rule.
     *
     * @usage addGroupingPolicy(string...) or addGroupingPolicy(array)
     * @param mixed ...$params the "g" policy rule, ptype "g" is implicitly used.
     * @return bool succeeds or not.
     */
    public function addGroupingPolicy(...$params) : bool
    {
        $params = is_array($params[0]) ? $params[0] : $params;

        return $this->addNamedGroupingPolicy(Consts::G, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * addNamedGroupingPolicy adds a named role inheritance rule to the current policy.
     * If the rule already exists, the function returns false and the rule will not be added.
     * Otherwise the function returns true by adding the new rule.
     *
     * @usage addNamedGroupingPolicy(string, string...) or addNamedGroupingPolicy(string, array)
     * @param string $ptype the policy type, can be "g", "g2", "g3", ..
     * @param mixed ...$params the "g" policy rule.
     * @return bool succeeds or not.
     */
    public function addNamedGroupingPolicy(string $ptype, ...$params) : bool
    {
        $params    = is_array($params[0]) ? $params[0] : $params;
        $ruleAdded = $this->addPolicy(Consts::G, $ptype, $params);

        if ($this->autoBuildRoleLinks)
        {
            $this->buildRoleLinks();
        }

        return $ruleAdded;
    }

    // ------------------------------------------------------------------------------

    /**
     * removeGroupingPolicy removes a role inheritance rule from the current policy.
     *
     * @usage removeGroupingPolicy(string...) or removeGroupingPolicy(array)
     * @param mixed ...$params the "g" policy rule, ptype "g" is implicitly used.
     * @return bool succeeds or not.
     */
    public function removeGroupingPolicy(...$params) : bool
    {
        $params = is_array($params[0]) ? $params[0] : $params;

        return $this->removeNamedGroupingPolicy(Consts::G, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * removeFilteredGroupingPolicy removes a role inheritance rule from the current policy, field filters can be specified.
     *
     * @param int $fieldIndex the policy rule's start index to be matched.
     * @param mixed ...$fieldValues the field values to be matched, value ""
     *                    means not to match this field.
     * @return bool succeeds or not.
     */
    public function removeFilteredGroupingPolicy(int $fieldIndex, ...$fieldValues)
    {
        $fieldValues = is_array($fieldValues[0]) ? $fieldValues[0] : $fieldValues;

        return $this->removeFilteredNamedGroupingPolicy(Consts::G, $fieldIndex, ...$fieldValues);
    }

    // ------------------------------------------------------------------------------

    /**
     * removeNamedGroupingPolicy removes a role inheritance rule from the current named policy.
     *
     * @usage removeNamedGroupingPolicy(string, string...) or removeNamedGroupingPolicy(string, array)
     * @param string $ptype the policy type, can be "g", "g2", "g3", ..
     * @param mixed ...$params the "g" policy rule.
     * @return bool succeeds or not.
     */
    public function removeNamedGroupingPolicy(string $ptype, ...$params) : bool
    {
        $params = is_array($params[0]) ? $params[0] : $params;

        $ruleRemoved = $this->removePolicy(Consts::G, $ptype, $params);

        if ($this->autoBuildRoleLinks)
        {
            $this->buildRoleLinks();
        }

        return $ruleRemoved;
    }

    // ------------------------------------------------------------------------------

    /**
     * removeFilteredNamedGroupingPolicy removes a role inheritance rule from the current named policy, field filters can be specified.
     *
     * @param string $ptype the policy type, can be "g", "g2", "g3", ..
     * @param int $fieldIndex the policy rule's start index to be matched.
     * @param mixed ...$fieldValues the field values to be matched, value ""
     *                    means not to match this field.
     * @return bool succeeds or not.
     */
    public function removeFilteredNamedGroupingPolicy(string $ptype, int $fieldIndex, ...$fieldValues) : bool
    {
        $fieldValues = is_array($fieldValues[0]) ? $fieldValues[0] : $fieldValues;

        $ruleRemoved = $this->removeFilteredPolicy(Consts::G, $ptype, $fieldIndex, ...$fieldValues);

        if ($this->autoBuildRoleLinks)
        {
            $this->buildRoleLinks();
        }

        return $ruleRemoved;
    }

    // ------------------------------------------------------------------------------

    /**
     * addFunction adds a customized function.
     *
     * @param string $name the name of the new function.
     * @param ExpressionFunction $func the function.
     */
    public function addFunction(string $name, ExpressionFunction $func) : void
    {
        $this->fm->addFunction($name, $func);
    }

    // ------------------------------------------------------------------------------
}
