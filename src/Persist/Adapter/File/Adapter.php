<?php namespace God\Persist\Adapter\File;

use God\Util\Util;
use God\Model\Model;
use God\Config\Consts;
use God\Persist\Helper\Helper;
use God\Exception\GodException;
use God\Exception\GodNotImplemented;
use God\Persist\Adapter as AdapterInterface;

/**
 * ------------------------------------------------------------------------------------
 * God File Adapter
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2019/07/30
 */
class Adapter implements AdapterInterface
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $filePath = '';

    // ------------------------------------------------------------------------------

    /**
     * FileAdapter is the constructor for FileAdapter.
     *
     * @param string $filePath the path of the policy file.
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    // ------------------------------------------------------------------------------

    /**
     * loadPolicy loads all policy rules from the storage.
     *
     * @param Model $model
     */
    public function loadPolicy(Model $model) : void
    {
        if (!empty($this->filePath))
        {
            try
            {
                $this->loadPolicyData($model);
            }
            catch (\Throwable $e)
            {
                throw new GodException('file operator error', $e->getCode());
            }
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * savePolicy saves all policy rules to the storage.
     *
     * @param Model $model
     */
    public function savePolicy(Model $model) : void
    {
        if (!$this->filePath)
        {
            throw new GodException('invalid file path, file path cannot be empty');
        }

        $p = $this->getModelPolicy($model, Consts::P);
        $g = $this->getModelPolicy($model, Consts::G);

        $policy = array_merge($p, $g);
        $policy = implode("\n", $policy);

        $this->savePolicyFile($policy);
    }

    // ------------------------------------------------------------------------------

    /**
     * addPolicy adds a policy rule to the storage.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     * @throws \God\Exception\GodNotImplemented
     */
    public function addPolicy(string $sec, string $ptype, array $rule) : void
    {
        throw new GodNotImplemented('not implemented');
    }

    // ------------------------------------------------------------------------------

    /**
     * removePolicy removes a policy rule from the storage.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     * @throws \God\Exception\GodNotImplemented
     */
    public function removePolicy(string $sec, string $ptype, array $rule) : void
    {
        throw new GodNotImplemented('not implemented');
    }

    // ------------------------------------------------------------------------------

    /**
     * removeFilteredPolicy removes policy rules that match the filter from the storage.
     *
     * @param string $sec
     * @param string $ptype
     * @param int    $fieldIndex
     * @param mixed ...$fieldValues
     * @throws \God\Exception\GodNotImplemented
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, ...$fieldValues) : void
    {
        throw new GodNotImplemented('not implemented');
    }

    // ------------------------------------------------------------------------------

    /**
     * get model policy
     *
     * @param \God\Model\Model $model
     * @param string           $ptype
     * @return array
     */
    private function getModelPolicy(Model $model, string $ptype) : array
    {
        $policy = [];
        $ptypes = $model->model[$ptype];

        forEach($ptypes as $k => $v)
        {
            $p = $v->policy;

            foreach ($p as &$x)
            {
                $x = "{$k}, " . Util::arrayToString($x);
            }

            $policy = array_merge($policy, $p);
        }

        return $policy;
    }


    // ------------------------------------------------------------------------------

    /**
     * load policy data
     *
     * @param \God\Model\Model $model
     * @throws \God\Exception\GodException
     */
    private function loadPolicyData(Model $model) : void
    {
        try
        {
            $fp = fopen($this->filePath, Consts::R);

            while(($line = fgets($fp)) !== false)
            {
                Helper::loadPolicyLine($line, $model);
            }

            fclose($fp);
        }
        catch (\Throwable $e)
        {
            throw new GodException('Policy load error');
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * save policy to file
     *
     * @param string $text
     * @throws \God\Exception\GodException
     */
    private function savePolicyFile(string $text) : void
    {
        try
        {
            $fp = fopen($this->filePath, 'w+');

            fwrite($fp, $text);
            fclose($fp);
        }
        catch (\Throwable $e)
        {
            throw new GodException('Policy save error');
        }
    }

    // ------------------------------------------------------------------------------

}
