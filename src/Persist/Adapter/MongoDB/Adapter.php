<?php namespace God\Persist\Adapter\MongoDB;

use God\Model\Model;
use God\Config\Consts;
use God\Persist\Helper\Helper;
use God\Exception\GodException;
use God\Persist\Adapter as AdapterInterface;

/**
 * ------------------------------------------------------------------------------------
 * God MongoDB Adapter
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/21
 */
class Adapter implements AdapterInterface
{

    // ------------------------------------------------------------------------------

    /**
     * mongodb collection handler
     *
     * @var \MongoDB\Collection
     */
    protected $collection;

    // ------------------------------------------------------------------------------

    /**
     * MongoDbAdapter constructor.
     *
     * @param \MongoDB\Collection $collection
     */
    public function __construct(\MongoDB\Collection $collection)
    {
        $this->collection = $collection;
    }

    // ------------------------------------------------------------------------------

    /**
     * loadPolicy loads all policy rules from the storage.
     *
     * @param Model $model
     */
    public function loadPolicy(Model $model) : void
    {
        if (!$this->collection)
        {
            throw new GodException('MongoDB collection handler required');
        }

        $this->loadPolicyMongoDB($model);
    }

    // ------------------------------------------------------------------------------

    /**
     * savePolicy saves all policy rules to the storage.
     *
     * @param Model $model
     */
    public function savePolicy(Model $model) : void
    {
        if (!$this->collection)
        {
            throw new GodException('mongodb handler cannot be empty');
        }

        $tmp = [];
        $this->collection->drop(); // empty dollection first

        foreach ($model->model[Consts::P] as $ptype => $ast)
        {
            foreach ($ast->policy as $rule)
            {
                $secKy = [Helper::SEC => Consts::P, Helper::KEY => $ptype];
                $rules = Helper::formatValues($rule);
                $tmp[] = array_merge($secKy, $rules);
            }
        }

        foreach ($model->model[Consts::G] as $ptype => $ast)
        {
            foreach ($ast->policy as $rule)
            {
                $secKy = [Helper::SEC => Consts::G, Helper::KEY => $ptype];
                $rules = Helper::formatValues($rule);
                $tmp[] = array_merge($secKy, $rules);
            }
        }

        $this->savePolicyMongoDB($tmp);
    }

    // ------------------------------------------------------------------------------

    /**
     * addPolicy adds a policy rule to the storage.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     */
    public function addPolicy(string $sec, string $ptype, array $rule) : void
    {
        $temp = [Helper::SEC => $sec, Helper::KEY => $ptype];

        $rule = Helper::formatValues($rule);

        $data = array_merge($temp, $rule);

        $this->savePolicyMongoDB([$data]);
    }

    // ------------------------------------------------------------------------------

    /**
     * removePolicy removes a policy rule from the storage.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     */
    public function removePolicy(string $sec, string $ptype, array $rule) : void
    {
        $temp = [Helper::SEC => $sec, Helper::KEY => $ptype];

        $rule = Helper::formatValues($rule);

        $data = array_merge($temp, $rule);

        $this->collection->deleteOne($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * removeFilteredPolicy removes policy rules that match the filter from the storage.
     *
     * @param string $sec
     * @param string $ptype
     * @param int    $fieldIndex
     * @param mixed ...$fieldValues
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, ...$fieldValues) : void
    {
        $fieldValues = is_array($fieldValues[0]) ? $fieldValues[0] : $fieldValues;

        $temp = [Helper::SEC => $sec, Helper::KEY => $ptype];

        $rule = Helper::formatValues($fieldValues, $fieldIndex);

        $data = array_merge($temp, $rule);

        $this->collection->deleteMany($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * loaad policy from mongodb
     *
     * @param \God\Model\Model $model
     * @throws \God\Exception\GodException
     */
    private function loadPolicyMongoDB(Model $model) : void
    {
        try
        {
            $data = $this->collection->find();

            foreach ($data as $line)
            {
                Helper::loadPolicyLine2($line, $model);
            }
        }
        catch (\Exception $e)
        {
            throw new GodException($e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * save policy to mongodb
     *
     * @param array $data
     * @throws \God\Exception\GodException
     */
    private function savePolicyMongoDB(array $data) : void
    {
        try
        {
            if (!$data) { return; }

            $data = Helper::concatFields($data, true);

            $this->collection->insertMany($data);
        }
        catch (\Exception $e)
        {
            throw new GodException($e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------

}
