<?php namespace God\Persist\Adapter\PDO\DB;

/**
 * ------------------------------------------------------------------------------------
 * God PDO Adapter SQL Interface
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2019/08/01
 */
interface AbsSQL
{

    // ------------------------------------------------------------------------------

    /**
     * AbsSQL constructor.
     *
     * @param string $table
     */
    public function __construct(string $table);

    // ------------------------------------------------------------------------------

    /**
     * sql for clear table
     *
     * @return string
     */
    public function sqlForTableClear() : string;

    // ------------------------------------------------------------------------------

    /**
     * sql for create table
     *
     * @return string
     */
    public function sqlForTableCreate() : string;

    // ------------------------------------------------------------------------------

    /**
     * sql for insert
     *
     * @return string
     */
    public function sqlForInsert() : string;

    // ------------------------------------------------------------------------------

    /**
     * sql for delete where
     *
     * @param array $where
     * @return string
     */
    public function sqlForDeleteWhere(array $where) : string;

    // ------------------------------------------------------------------------------

    /**
     * sql for find where
     *
     * @param array $where
     * @return string
     */
    public function sqlForFindWhere(array $where = []) : string;

    // ------------------------------------------------------------------------------

}
