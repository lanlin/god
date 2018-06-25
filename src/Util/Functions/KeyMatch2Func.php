<?php namespace God\Util\Functions;

use God\Config\Consts;
use God\Util\AbstractFunction;

/**
 * ------------------------------------------------------------------------------------
 * God Key Match2
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/13
 */
class KeyMatch2Func extends AbstractFunction
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $name = 'keyMatch2';

    // ------------------------------------------------------------------------------

    /**
     * @return callable
     */
    protected function getCompiler() : callable
    {
        return function ($arg1, $arg2)
        {
            return 'keyMatch2('. $arg1 .Consts::IMPLODE_DELIMITER. $arg2 .')';
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
            return self::keyMatch2($key1, $key2);
        };
    }

    // ------------------------------------------------------------------------------

    /**
     * keyMatch2 determines whether key1 matches the pattern of key2 (similar to RESTful path),
     * key2 can contain a *. For example, "/foo/bar" matches "/foo/*", "/resource1" matches "/:resource"
     *
     * @param string $key1 the first argument.
     * @param string $key2 the second argument.
     * @return bool whether key1 matches key2.
     */
    public static function keyMatch2(string $key1, string $key2) : bool
    {
        $key2 = str_replace('/*', '/.*', $key2);

        while (true)
        {
            if (strpos($key2, '/:') === false)
            {
                break;
            }

            $patn = self::getRegexPattern('(.*):[^/]+(.*)');

            $tmps = preg_replace($patn, '$1[^/]+$2', $key2);

            $key2 = '^'. $tmps .'$';
        }

        $pattern = self::getRegexPattern($key2);

        return !!preg_match($pattern, $key1);
    }

    // ------------------------------------------------------------------------------
}
