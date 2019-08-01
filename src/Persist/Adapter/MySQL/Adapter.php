<?php namespace God\Persist\Adapter\MySQL;

use God\Model\Model;
use God\Config\Consts;
use God\Persist\Helper\Helper;
use God\Persist\Helper\Fields;
use God\Exception\GodException;
use God\Persist\Adapter as AdapterInterface;

/**
 * ------------------------------------------------------------------------------------
 * God MySQl Adapter
 * ------------------------------------------------------------------------------------
 *
 * @link https://www.php.net/manual/en/pdo.drivers.php
 * @author lanlin
 * @change 2019/07/31
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
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        try
        {
            $this->pdo->query("SELECT 1 FROM `{$this->table}` LIMIT 1");
        }
        catch (\Throwable $e)
        {
            $this->pdo->exec($this->getTableSql());
        }
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
            throw new GodException('mysql handler required');
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
            throw new GodException('mysql handler cannot be empty');
        }

        // empty dollection first
        $tmp = [];
        $this->pdo->exec("TRUNCATE TABLE `{$this->table}`");

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
        $stmt = $this->getDeletePrepare($data);

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
        $stmt = $this->getDeletePrepare($data);

        $stmt->execute($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * loaad policy from db
     *
     * @param \God\Model\Model $model
     * @param string $where
     * @throws \God\Exception\GodException
     */
    protected function loadPolicyDB(Model $model, string $where = null) : void
    {
        $cond = "SELECT * from `{$this->table}`";

        if ($where) { $cond .= " WHERE {$where}"; }

        try
        {
            $stmt = $this->pdo->query($cond);

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

        $stmt = $this->getInsertPrepare();
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
     * get delete prepare
     *
     * @param array $where
     * @return \PDOStatement
     */
    private function getDeletePrepare(array $where) : \PDOStatement
    {
        $arr = [];

        foreach ($where as $key => $val)
        {
            $arr[] = "`$key`='{$val}'";
        }

        $str = implode(' AND', $arr);
        $sql = "DELETE FROM `{$this->table}` WHERE {$str}";

        return $this->pdo->prepare($sql);
    }

    // ------------------------------------------------------------------------------

    /**
     * get insert prepare
     *
     * @return \PDOStatement
     */
    private function getInsertPrepare() : \PDOStatement
    {
        $values = array_fill(0, count(Fields::FIELDS), '?');
        $values = implode(', ', $values);

        $fields = [];

        foreach (Fields::FIELDS as $field)
        {
            $fields[] = "`{$field}`";
        }

        $fields = implode(', ', $fields);

        $sql = "INSERT INTO `{$this->table}` ({$fields}) VALUES ({$values})";

        return $this->pdo->prepare($sql);
    }

    // ------------------------------------------------------------------------------

    /**
     * table construct sql for pdo db (exp: mysql)
     *
     * @return string
     */
    private function getTableSql() : string
    {
        $fields = [];

        foreach (Fields::FIELDS as $field)
        {
            $fields[] = "`{$field}` VARCHAR(128) NULL DEFAULT NULL";
        }

        $fieldsString = implode(",\n", $fields);

        return
        "CREATE TABLE `{$this->table}` (
            `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            {$fieldsString}
        )
        ENGINE = 'InnoDB'
        CHARACTER SET = 'utf8'
        COLLATE = 'utf8_general_ci'
        COMMENT = 'this is the god adapter policy table'";
    }

    // ------------------------------------------------------------------------------

}
