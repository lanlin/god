<?php namespace God\Util\Functions;

use God\Config\Consts;
use God\Util\AbstractFunction;

/**
 * ------------------------------------------------------------------------------------
 * God Key Match
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/15
 */
class KeyMatchFunc extends AbstractFunction
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $name = 'keyMatch';

    // ------------------------------------------------------------------------------

    /**
     * @return callable
     */
    protected function getCompiler() : callable
    {
        return function ($arg1, $arg2)
        {
            return 'keyMatch('. $arg1 .Consts::IMPLODE_DELIMITER. $arg2 .')';
        };
    }

    // ------------------------------------------------------------------------------

    /**
     * @return callable
     */
    protected function getEvaluator() : callable
    {
        return function ($args, $key1, $key2)
        {
            return self::keyMatch($key1, $key2);
        };
    }

    // ------------------------------------------------------------------------------

    /**
     * keyMatch determines whether key1 matches the pattern of key2 (similar to RESTful path), key2 can contain a *.
     * For example, "/foo/bar" matches "/foo/*"
     *
     * @param string $key1 the first argument.
     * @param string $key2 the second argument.
     * @return bool whether key1 matches key2.
     */
    public static function keyMatch(string $key1, string $key2) : bool
    {
        $i = strpos($key2, '*');

        if ($i === false)
        {
            return $key1 === $key2;
        }

        if (strlen($key1) > $i)
        {
            return substr($key1, 0, $i) === substr($key2, 0, $i);
        }

        return $key1 === substr($key2, 0, $i);
    }
    // ------------------------------------------------------------------------------

}
