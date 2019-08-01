<?php namespace God\Persist\Adapter\PDO;

use God\Model\Model;
use God\Config\Consts;
use God\Persist\Helper\Helper;
use God\Exception\GodException;
use God\Persist\Adapter as AdapterInterface;

/**
 * ------------------------------------------------------------------------------------
 * God PDO Drivers Adapter
 * ------------------------------------------------------------------------------------
 *
 * @link   https://www.php.net/manual/en/pdo.drivers.php
 * @author lanlin
 * @change 2019/08/01
 */
class Adapter implements AdapterInterface
{

    // ------------------------------------------------------------------------------

    /**
     * pdo handler
     *
     * @var \PDO
     */
    protected $pdo;

    // ------------------------------------------------------------------------------

    /**
     * @var \God\Persist\Adapter\PDO\DB\AbsSQL
     */
    protected $sql;

    // ------------------------------------------------------------------------------

    /**
     * database table name
     *
     * @var string
     */
    protected $table = 'adapter_god_policy';

    // ------------------------------------------------------------------------------

    /**
     * PDO Adapter constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->sql = $this->initSqlInstance();

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec($this->sql->sqlForTableCreate());
    }

    // ------------------------------------------------------------------------------

    /**
     * loadPolicy loads all policy rules from the storage.
     *
     * @param Model $model
     */
    public function loadPolicy(Model $model) : void
    {
        if (!$this->pdo)
        {
            throw new GodException('pdo handler required');
        }

        $this->loadPolicyDB($model);
    }

    // ------------------------------------------------------------------------------

    /**
     * savePolicy saves all policy rules to the storage.
     *
     * @param Model $model
     */
    public function savePolicy(Model $model) : void
    {
        if (!$this->pdo)
        {
            throw new GodException('pdo handler cannot be empty');
        }

        // empty dollection first
        $tmp = [];
        $this->pdo->exec($this->sql->sqlForTableClear());

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

        $this->savePolicyDB($tmp);
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

        $this->savePolicyDB([$data]);
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
        $stmt = $this->pdo->prepare($this->sql->sqlForDeleteWhere($data));

        $stmt->execute($data);
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
        $stmt = $this->pdo->prepare($this->sql->sqlForDeleteWhere($data));

        $stmt->execute($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * loaad policy from db
     *
     * @param \God\Model\Model $model
     * @param array $where
     * @throws \God\Exception\GodException
     */
    protected function loadPolicyDB(Model $model, array $where = []) : void
    {
        try
        {
            $stmt = $this->pdo->query($this->sql->sqlForFindWhere($where));

            while ($line = $stmt->fetch(\PDO::FETCH_ASSOC))
            {
                unset($line['id']);

                Helper::loadPolicyLine2($line, $model);
            }
        }
        catch (\Throwable $e)
        {
            throw new GodException($e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * save policy to db
     *
     * @param array $data
     * @throws \God\Exception\GodException
     */
    private function savePolicyDB(array $data) : void
    {
        if (!$data) { return; }

        $stmt = $this->pdo->prepare($this->sql->sqlForInsert());
        $data = Helper::concatFields($data, true);

        try
        {
            $this->pdo->beginTransaction();

            foreach ($data as $row)
            {
                $stmt->execute(array_values($row));
            }

            $this->pdo->commit();
        }
        catch (\Throwable $e)
        {
            $this->pdo->rollback();

            throw new GodException($e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * init sql instance
     *
     * @return \God\Persist\Adapter\PDO\DB\AbsSQL
     * @throws \God\Exception\GodException
     */
    private function initSqlInstance()
    {
        $dbName = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);

        switch ($dbName)
        {
            case 'mysql':  return new DB\MySQL($this->table);
            case 'pgsql':  return new DB\PostgreSQL($this->table);
            case 'sqlite': return new DB\SQLite($this->table);
            case 'sqlsrv': return new DB\SqlServer($this->table);

            default: throw new GodException("adapter {$dbName} not implemented yet.");
        }
    }

    // ------------------------------------------------------------------------------

}
