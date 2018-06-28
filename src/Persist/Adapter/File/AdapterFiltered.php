<?php namespace God\Persist\Adapter\File;

use God\Model\Model;
use God\Config\Consts;
use God\Persist\Helper\Helper;
use God\Exception\GodException;
use God\Persist\AdapterFiltered as AdapterFilteredInterface;

/**
 * ------------------------------------------------------------------------------------
 * God File Adapter Filtered
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
     * @param string $filePath
     * @param mixed $filter
     */
    public function __construct(string $filePath, $filter = null)
    {
        parent::__construct($filePath);

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
     * savePolicy saves all policy rules to the storage.
     *
     * @param \God\Model\Model $model
     * @throws \God\Exception\GodException
     */
    public function savePolicy(Model $model) : void
    {
        if ($this->filtered)
        {
            throw new GodException('cannot save a filtered policy');
        }

        parent::savePolicy($model);
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
            $this->loadFilteredPolicyFile($model, $filter);

            $this->filtered = true;
        }
        catch (\Exception $e)
        {
            throw new GodException($e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * check if current line needed be skipped
     *
     * @param string $line
     * @param array  $filter
     * @return bool
     */
    private function filterLine(string $line, array $filter) : bool
    {
        if (!$filter) { return false; }

        $policy = explode(Consts::IMPLODE_DELIMITER, $line);

        if (count($policy) === 0)
        {
            return true;
        }

        return $this->filterWords($policy, $filter[$policy[0]]);
    }

    // ------------------------------------------------------------------------------

    /**
     * check the words determine if need skip the line
     *
     * @param array $policy
     * @param array $filter
     * @return bool
     */
    private function filterWords(array $policy, array $filter) : bool
    {
        if (count($policy) < count($filter)+1)
        {
            return true;
        }

        foreach($filter as $i => $v)
        {
            if (trim($v) && trim($v) !== trim($policy[$i+1]))
            {
                return true;
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------------

    /**
     * Filter defines the filtering rules for a FilteredAdapter's policy.
     * Empty values are ignored, but all others must match the filter.
     *
     * @param $filter
     *  [
     *      'p' => [...],
     *      'g' => [...]
     * ]
     *
     * @return bool
     * @throws \God\Exception\GodException
     */
    private function filterValidate($filter)
    {
        if (!$this->filePath)
        {
            throw new GodException('invalid file path, file path cannot be empty');
        }

        if (empty($filter))
        {
            return false;
        }

        if (!is_array($filter) || !(isset($filter[Consts::P]) || isset($filter[Consts::G])) )
        {
            throw new GodException('invalid filter condition');
        }

        return true;
    }

    // ------------------------------------------------------------------------------

    /**
     * load policy by filter
     *
     * @param \God\Model\Model $model
     * @param mixed $filter
     * @throws \God\Exception\GodException
     */
    private function loadFilteredPolicyFile(Model $model, array $filter)
    {
        try
        {
            $fp = fopen($this->filePath, Consts::R);

            while(($line = fgets($fp)) !== false)
            {
                if ($this->filterLine($line, $filter))
                {
                    continue; // skip this line
                }

                Helper::loadPolicyLine($line, $model);
            }

            fclose($fp);
        }
        catch (\Exception $e)
        {
            throw new GodException($e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------

}
