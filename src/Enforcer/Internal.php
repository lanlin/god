<?php namespace God\Enforcer;

use God\Exception\GodNotImplemented;

/**
 * ------------------------------------------------------------------------------------
 * God Internal Enforcer
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2019/07/30
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
        if ($this->adapter && $this->autoSave)
        {
            try
            {
                $this->adapter->addPolicy($sec, $ptype, $rule);
            }
            catch (\Throwable $e)
            {
                if ($e instanceof GodNotImplemented) { throw $e; }
            }

            if ($this->watcher !== null)
            {
                $this->watcher->update();
            }
        }

        return $this->model->addPolicy($sec, $ptype, $rule);
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

        if ($this->adapter && $this->autoSave)
        {
            try
            {
                $this->adapter->removePolicy($sec, $ptype, $rule);
            }
            catch (\Throwable $e)
            {
                if ($e instanceof GodNotImplemented) { throw $e; }
            }

            if ($this->watcher !== null)
            {
                $this->watcher->update();
            }
        }

        return $this->model->removePolicy($sec, $ptype, $rule);
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

        if ($this->adapter && $this->autoSave)
        {
            try
            {
                $this->adapter->removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues);
            }
            catch (\Throwable $e)
            {
                if ($e instanceof GodNotImplemented) { throw $e; }
            }

            if ($this->watcher !== null)
            {
                $this->watcher->update();
            }
        }

        return $this->model->removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues);
    }

    // ------------------------------------------------------------------------------

}
