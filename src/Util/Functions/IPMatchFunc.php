<?php namespace God\Util\Functions;

use God\Config\Consts;
use God\Util\AbstractFunction;
use God\Exception\GodException;

/**
 * ------------------------------------------------------------------------------------
 * God IP Match
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/13
 */
class IPMatchFunc extends AbstractFunction
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $name = 'ipMatch';

    // ------------------------------------------------------------------------------

    /**
     * @return callable
     */
    protected function getCompiler() : callable
    {
        return function ($arg1, $arg2)
        {
            return 'ipMatch('. $arg1 .Consts::IMPLODE_DELIMITER. $arg2 .')';
        };
    }

    // ------------------------------------------------------------------------------

    /**
     * @return callable
     */
    protected function getEvaluator() : callable
    {
        return function ($args, $ip1, $ip2)
        {
            return self::ipMatch($ip1, $ip2);
        };
    }

    // ------------------------------------------------------------------------------

    /**
     * ipMatch determines whether IP address ip1 matches the pattern of IP address ip2, ip2 can be an IP address or a CIDR pattern.
     * For example, "192.168.2.123" matches "192.168.2.0/24"
     *
     * @param string $ip1 the first argument.
     * @param string $ip2 the second argument.
     * @return bool whether ip1 matches ip2.
     */
    private static function ipMatch(string $ip1, string $ip2) : bool
    {
        if (!filter_var($ip1, FILTER_VALIDATE_IP))
        {
            throw new GodException('invalid argument: ip1 in IPMatch() function is not an IP address.');
        }

        if ($ip1 === $ip2)
        {
            return true;
        }

        if (strpos($ip2, '/' ) === false)
        {
            $ip2 .= '/32';
        }

        // $ip2 is in IP/CIDR format eg 127.0.0.1/24
        list($ip2, $netmask) = explode( '/', $ip2, 2 );

        $ipDecimal    = ip2long($ip1);
        $rangeDecimal = ip2long($ip2);

        $wildcardDecimal = pow( 2, ( 32 - $netmask ) ) - 1;
        $netmaskDecimal  = ~ $wildcardDecimal;

        return ( ($ipDecimal & $netmaskDecimal) == ($rangeDecimal & $netmaskDecimal) );
    }

    // ------------------------------------------------------------------------------
}
