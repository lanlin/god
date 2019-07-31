<?php namespace God\Persist\Adapter\PDO;

use God\Model\Model;
use God\Exception\GodException;
use God\Persist\AdapterFiltered as AdapterFilteredInterface;

/**
 * ------------------------------------------------------------------------------------
 * God PDO Drivers Adapter Filtered
 * ------------------------------------------------------------------------------------
 *
 * @link https://www.php.net/manual/en/pdo.drivers.php
 * @author lanlin
 * @change 2019/07/31
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
     * @param \PDO $pdo
     * @param mixed $filter
     */
    public function __construct(\PDO $pdo, $filter = null)
    {
        parent::__construct($pdo);

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
     * @param mixed $filter
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
            $this->loadFilteredPolicyDB($model, $filter);

            $this->filtered = true;
        }
        catch (\Exception $e)
        {
            throw new GodException($e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * loadFilteredPolicyDB loads matching policy lines from database.
     *
     * @param \God\Model\Model $model
     * @param array $filter
     */
    private function loadFilteredPolicyDB(Model $model, array $filter)
    {
        $arr = [];

        foreach ($filter as $key => $val)
        {
            $arr[] = "`$key`='{$val}''";
        }

        $where = implode(' AND', $arr);

        $this->loadPolicyDB($model, $where);
    }

    // ------------------------------------------------------------------------------

    /**
     * the filter must be a valid DB selector.
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
            throw new GodException('invalid database filter');
        }

        return true;
    }

    // ------------------------------------------------------------------------------

}
