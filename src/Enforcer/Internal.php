<?php namespace God\Enforcer;

use God\Exception\GodNotImplemented;

/**
 * ------------------------------------------------------------------------------------
 * God Internal Enforcer
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/13
 */
class Internal extends Core
{

    // ------------------------------------------------------------------------------

    /**
     * addPolicy adds a rule to the current policy.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     * @return bool
     */
    protected function addPolicyInternal(string $sec, string $ptype, array $rule) : bool
    {
        $ruleAdded = $this->model->addPolicy($sec, $ptype, $rule);

        if (!$ruleAdded || $this->adapter === null || !$this->autoSave)
        {
            return $ruleAdded;
        }

        try
        {
            $this->adapter->addPolicy($sec, $ptype, $rule);
        }
        catch (GodNotImplemented $e)
        {
            // ignore not implemented
        }

        if ($this->watcher !== null)
        {
            $this->watcher->update();
        }

        return $ruleAdded;
    }

    // ------------------------------------------------------------------------------

    /**
     * removePolicy removes a rule from the current policy.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     * @return bool
     */
    protected function removePolicyInternal(string $sec, string $ptype, array $rule) : bool
    {
        $ruleRemoved = $this->model->removePolicy($sec, $ptype, $rule);

        if (!$ruleRemoved || $this->adapter === null || !$this->autoSave)
        {
            return $ruleRemoved;
        }

        try
        {
            $this->adapter->removePolicy($sec, $ptype, $rule);
        }
        catch (GodNotImplemented $e)
        {
            // ignore not implemented
        }

        if ($this->watcher !== null)
        {
            $this->watcher->update();
        }

        return $ruleRemoved;
    }

    // ------------------------------------------------------------------------------

    /**
     * removeFilteredPolicy removes rules based on field filters from the current policy.
     *
     * @param string $sec
     * @param string $ptype
     * @param int    $fieldIndex
     * @param mixed ...$fieldValues
     * @return bool
     */
    protected function removeFilteredPolicyInternal(string $sec, string $ptype, int $fieldIndex, ...$fieldValues) : bool
    {
        $fieldValues = is_array($fieldValues[0]) ? $fieldValues[0] : $fieldValues;

        $ruleRemoved = $this->model->removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues);

        if (!$ruleRemoved || $this->adapter === null || !$this->autoSave)
        {
            return $ruleRemoved;
        }

        try
        {
            $this->adapter->removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues);
        }
        catch (GodNotImplemented $e)
        {
            // ignore not implemented
        }

        if ($this->watcher !== null)
        {
            $this->watcher->update();
        }

        return $ruleRemoved;
    }

    // ------------------------------------------------------------------------------

}
