<?php namespace God\Effect;

use God\Config\Consts;
use God\Exception\GodException;

/**
 * ------------------------------------------------------------------------------------
 * God Defualt Effector
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/13
 */
class DefaultEffector implements Effector
{

    // ------------------------------------------------------------------------------

    /**
     * mergeEffects merges all matching results collected by the enforcer into a single decision.
     *
     * @param string $expr
     * @param array $effects
     * @param array $results
     * @return bool
     */
    public function mergeEffects(string $expr, array $effects, array $results) : bool
    {
        $eft = Consts::P .'_eft';

        // @warning do not change strs below, even a space character.
        $caseA = 'some(where (' .$eft. ' == allow))';
        $caseB = '!some(where (' .$eft. ' == deny))';
        $caseC = 'some(where (' .$eft. ' == allow)) && !some(where (' .$eft. ' == deny))';
        $caseD = 'priority(' .$eft. ') || deny';

        switch ($expr)
        {
            case $caseA: return $this->sectionA($effects);   // any allow this will be true
            case $caseB: return $this->sectionB($effects);   // any deny this will be false
            case $caseC: return $this->sectionC($effects);   // at least one allow and none deny
            case $caseD: return $this->sectionD($effects);   // first match allow will be true else false

            default: throw new GodException("unsupported effect {$expr}");
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * $caseA = 'some(where (p_eft == allow))';
     *
     * @param array $effects
     * @return bool
     */
    private function sectionA(array $effects)
    {
        foreach ($effects as $eft)
        {
            if ($eft === Effect::Allow)
            {
                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------------

    /**
     * $caseB = '!some(where (p_eft == deny))';
     *
     * @param array $effects
     * @return bool
     */
    private function sectionB(array $effects) : bool
    {
        foreach ($effects as $eft)
        {
            if ($eft === Effect::Deny)
            {
                return false;
            }
        }

        return true;
    }

    // ------------------------------------------------------------------------------

    /**
     * $caseC = 'some(where (p_eft == allow)) && !some(where (p_eft == deny))';
     *
     * @param array $effects
     * @return bool
     */
    private function sectionC(array $effects) : bool
    {
        $result = false;

        foreach ($effects as $eft)
        {
            if ($eft === Effect::Deny)
            {
                return false;
            }

            if ($eft === Effect::Allow)
            {
                $result = true;
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------------------

    /**
     * $caseD = 'priority(p_eft) || deny';
     *
     * @param array $effects
     * @return bool
     */
    private function sectionD(array $effects) : bool
    {
        foreach ($effects as $eft)
        {
            if ($eft !== Effect::Indeterminate)
            {
                return ($eft === Effect::Allow);
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------------

}
