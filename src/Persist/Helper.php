<?php namespace God\Persist;

use God\Model\Model;

/**
 * ------------------------------------------------------------------------------------
 * God Helper Interface
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/14
 */
interface Helper
{

    // ------------------------------------------------------------------------------

    /**
     * @param string                        $line
     * @param \God\Model\Model $model
     */
    public static function loadPolicyLine(string $line, Model $model) : void;

    // ------------------------------------------------------------------------------

    /**
     * @param array                         $line
     * @param \God\Model\Model $model
     */
    public static function loadPolicyLine2(array $line, Model $model) : void;

    // ------------------------------------------------------------------------------

}
