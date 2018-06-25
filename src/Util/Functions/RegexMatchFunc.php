<?php namespace God\Util\Functions;

use God\Config\Consts;
use God\Util\AbstractFunction;

/**
 * ------------------------------------------------------------------------------------
 * God Regex Match
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/15
 */
class RegexMatchFunc extends AbstractFunction
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $name = 'regexMatch';

    // ------------------------------------------------------------------------------

    /**
     * @return callable
     */
    protected function getCompiler() : callable
    {
        return function ($arg1, $arg2)
        {
            return 'regexMatch('. $arg1 .Consts::IMPLODE_DELIMITER. $arg2 .')';
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
            return self::regexMatch($key1, $key2);
        };
    }

    // ------------------------------------------------------------------------------

    /**
     * regexMatch determines whether key1 matches the pattern of key2 in regular expression.
     *
     * @param string $key1 the first argument.
     * @param string $key2 the second argument.
     * @return bool whether key1 matches key2.
     */
    public static function regexMatch(string $key1, string $key2) : bool
    {
        $pattern = self::getRegexPattern($key2);

        return !!preg_match($pattern, $key1);
    }

    // ------------------------------------------------------------------------------

}
