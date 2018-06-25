<?php namespace God\Persist;

use God\Model\Model;

/**
 * ------------------------------------------------------------------------------------
 * God Adapter Filter Interface
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/22
 */
interface AdapterFiltered extends Adapter
{

    // ------------------------------------------------------------------------------

    /**
     * isFiltered returns true if the loaded policy has been filtered.
     *
     * @return bool
     */
    public function isFiltered() : bool;

    // ------------------------------------------------------------------------------

    /**
     * loadFilteredPolicy loads only policy rules that match the filter.
     *
     * @param \God\Model\Model $model
     * @param mixed $filter
     */
    public function loadFilteredPolicy(Model $model, $filter) : void;

    // ------------------------------------------------------------------------------

}
