<?php namespace God\Persist\Adapter\PDO\DB;

use God\Persist\Helper\Fields;

/**
 * ------------------------------------------------------------------------------------
 * God SQLite SQL
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2019/08/01
 */
class SQLite implements AbsSQL
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    private $table;

    // ------------------------------------------------------------------------------

    /**
     * SQLite constructor.
     *
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    // ------------------------------------------------------------------------------

    /**
     * sql for clear table
     *
     * @return string
     */
    public function sqlForTableClear() : string
    {
        return "TRUNCATE TABLE `{$this->table}`";
    }

    // ------------------------------------------------------------------------------

    /**
     * sql for create table
     *
     * @return string
     */
    public function sqlForTableCreate() : string
    {
        $fields = [];

        foreach (Fields::FIELDS as $field)
        {
            $fields[] = "`{$field}` VARCHAR(128) DEFAULT NULL";
        }

        $fieldsString = implode(",\n", $fields);

        return
        "CREATE TABLE IF NOT EXISTS `{$this->table}` (
            `id` INTEGER AUTOINCREMENT PRIMARY KEY,
            {$fieldsString}
        )";
    }

    // ------------------------------------------------------------------------------

    /**
     * sql for insert
     *
     * @return string
     */
    public function sqlForInsert() : string
    {
        $values = array_fill(0, count(Fields::FIELDS), '?');
        $values = implode(', ', $values);

        $fields = [];

        foreach (Fields::FIELDS as $field)
        {
            $fields[] = "`{$field}`";
        }

        $fields = implode(', ', $fields);

        return "INSERT INTO `{$this->table}` ({$fields}) VALUES ({$values})";
    }

    // ------------------------------------------------------------------------------

    /**
     * sql for delete where
     *
     * @param array $where
     * @return string
     */
    public function sqlForDeleteWhere(array $where) : string
    {
        $condition = $this->whereConditions($where);

        return "DELETE FROM `{$this->table}` WHERE {$condition}";
    }

    // ------------------------------------------------------------------------------

    /**
     * sql for find where
     *
     * @param array $where
     * @return string
     */
    public function sqlForFindWhere(array $where = []) : string
    {
        $sql = "SELECT * from `{$this->table}`";

        if (!empty($where))
        {
            $cond = $this->whereConditions($where);
            $sql .= " WHERE {$cond}";
        }

        return $sql;
    }

    // ------------------------------------------------------------------------------

    /**
     * get where conditions
     *
     * @param array $where
     * @return string
     */
    private function whereConditions(array $where)
    {
        $arr = [];

        foreach ($where as $key => $val)
        {
            $arr[] = "`$key`='{$val}'";
        }

        return implode(' AND', $arr);
    }

    // ------------------------------------------------------------------------------

}
