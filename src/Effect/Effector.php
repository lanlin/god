<?php namespace God\Effect;

/**
 * ------------------------------------------------------------------------------------
 * God Effector Interface
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/13
 */
interface Effector
{

    // ------------------------------------------------------------------------------

    /**
     * mergeEffects merges all matching results collected by the enforcer into a single decision.
     *
     * @param string $expr the expression of [policy_effect].
     * @param array $effects the effects of all matched rules.
     * @param array $results the matcher results of all matched rules.
     * @return bool the final effect.
     */
    public function mergeEffects(string $expr, array $effects, array $results) : bool;

    // ------------------------------------------------------------------------------

}
