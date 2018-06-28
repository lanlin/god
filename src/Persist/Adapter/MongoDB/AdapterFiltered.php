<?php namespace God\Persist\Adapter\MongoDB;

use God\Model\Model;
use God\Persist\Helper\Helper;
use God\Exception\GodException;
use God\Persist\AdapterFiltered as AdapterFilteredInterface;

/**
 * ------------------------------------------------------------------------------------
 * God MongoDB Adapter Filtered
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/22
 */
class AdapterFiltered extends Adapter implements AdapterFilteredInterface
{

    // ------------------------------------------------------------------------------

    /**
     * is filtered
     *
     * @var bool
     */
    private $filtered = false;

    // ------------------------------------------------------------------------------

    /**
     * filter conditions
     *
     * @var mixed
     */
    private $filter = null;

    // ------------------------------------------------------------------------------

    /**
     * AdapterFiltered constructor.
     *
     * @param \MongoDB\Collection $collection
     * @param mixed $filter
     */
    public function __construct(\MongoDB\Collection $collection, $filter = null)
    {
        parent::__construct($collection);

        $this->filter = $filter;
    }

    // ------------------------------------------------------------------------------

    /**
     * isFiltered returns true if the loaded policy has been filtered.
     *
     * @return bool
     */
    public function isFiltered() : bool
    {
        return $this->filtered;
    }

    // ------------------------------------------------------------------------------

    /**
     * loadPolicy loads all policy rules from the storage.
     *
     * @param \God\Model\Model $model
     */
    public function loadPolicy(Model $model) : void
    {
        $this->filtered = false;

        empty($this->filter) ?
        parent::loadPolicy($model) :
        $this->loadFilteredPolicy($model, $this->filter);
    }

    // ------------------------------------------------------------------------------

    /**
     * loadFilteredPolicy loads only policy rules that match the filter.
     *
     * @param \God\Model\Model $model
     * @param mixed                         $filter
     * @throws \God\Exception\GodException
     */
    public function loadFilteredPolicy(Model $model, $filter) : void
    {
        if (!$this->filterValidate($filter))
        {
            $this->loadPolicy($model);
            return;
        }

        try
        {
            $this->loadFilteredPolicyMongoDB($model, $filter);

            $this->filtered = true;
        }
        catch (\Exception $e)
        {
            throw new GodException($e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * loadFilteredPolicyMongoDB loads matching policy lines from database.
     *
     * @param \God\Model\Model $model
     * @param array                         $filter
     */
    private function loadFilteredPolicyMongoDB(Model $model, array $filter)
    {
        $cursor = $this->collection->Find($filter);

        foreach ($cursor as $line)
        {
            Helper::loadPolicyLine2($line, $model);
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * the filter must be a valid MongoDB selector.
     *
     * @param mixed $filter
     * @return bool
     * @throws \God\Exception\GodException
     */
    private function filterValidate($filter)
    {
        if (empty($filter))
        {
            return false;
        }

        if (!is_array($filter) && !is_object($filter))
        {
            throw new GodException('invalid mongodb filter');
        }

        return true;
    }

    // ------------------------------------------------------------------------------

}
