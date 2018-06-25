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
 * @change 2018/06/13
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
        if (!$this->filePath)
        {
            throw new GodException('invalid file path, file path cannot be empty');
        }

        $this->loadPolicyFile($model);
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

        $tmp = '';

        foreach ($model->model[Consts::P] as $ptype => $ast)
        {
            foreach ($ast->policy as $rule)
            {
                $tmp .= $ptype . Consts::IMPLODE_DELIMITER;
                $tmp .= Util::arrayToString($rule);
                $tmp .= Consts::LINE_BREAK_KEEPED;
            }
        }

        foreach ($model->model[Consts::G] as $ptype => $ast)
        {
            foreach ($ast->policy as $rule)
            {
                $tmp .= $ptype . Consts::IMPLODE_DELIMITER;
                $tmp .= Util::arrayToString($rule);
                $tmp .= Consts::LINE_BREAK_KEEPED;
            }
        }

        $this->savePolicyFile(trim($tmp));
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
     * load policy from file
     *
     * @param \God\Model\Model $model
     * @throws \God\Exception\GodException
     */
    private function loadPolicyFile(Model $model) : void
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
        catch (\Exception $e)
        {
            throw new GodException($e->getMessage());
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
        catch (\Exception $e)
        {
            throw new GodException($e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------

}
