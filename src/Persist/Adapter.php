<?php namespace God\Persist;

use God\Model\Model;

/**
 * ------------------------------------------------------------------------------------
 * God Adapter Interface
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/14
 */
interface Adapter
{

    // ------------------------------------------------------------------------------

    /**
     * loadPolicy loads all policy rules from the storage.
     *
     * @param Model $model the model.
     */
    public function loadPolicy(Model $model) : void;

    // ------------------------------------------------------------------------------

    /**
     * savePolicy saves all policy rules to the storage.
     *
     * @param Model $model the model.
     */
    public function savePolicy(Model $model) : void;

    // ------------------------------------------------------------------------------

    /**
     * addPolicy adds a policy rule to the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $ptype the policy type, "p", "p2", .. or "g", "g2", ..
     * @param array $rule the rule, like (sub, obj, act).
     */
    public function addPolicy(string $sec, string $ptype, array $rule) : void;

    // ------------------------------------------------------------------------------

    /**
     * removePolicy removes a policy rule from the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $ptype the policy type, "p", "p2", .. or "g", "g2", ..
     * @param array $rule the rule, like (sub, obj, act).
     */
    public function removePolicy(string $sec, string $ptype, array $rule) : void;

    // ------------------------------------------------------------------------------

    /**
     * removeFilteredPolicy removes policy rules that match the filter from the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $ptype the policy type, "p", "p2", .. or "g", "g2", ..
     * @param int $fieldIndex the policy rule's start index to be matched.
     * @param mixed ...$fieldValues the field values to be matched, value "" means not to match this field.
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, ...$fieldValues);

    // ------------------------------------------------------------------------------
}
