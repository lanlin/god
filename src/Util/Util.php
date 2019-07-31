<?php namespace God\Util;

use God\Config\Consts;
use God\Exception\GodException;

/**
 * ------------------------------------------------------------------------------------
 * God Util
 * ------------------------------------------------------------------------------------
 *
 * Default log with error_log path.
 *
 * @author lanlin
 * @change 2018/06/14
 */
class Util
{

    // ------------------------------------------------------------------------------

    /**
     * @var
     */
    public static $logger;

    // ------------------------------------------------------------------------------

    /**
     * @var bool
     */
    public static $enableLog = false;

    // ------------------------------------------------------------------------------

    /**
     * @param \stdClass $logger
     */
    public static function setLogger(\stdClass $logger)
    {
        self::$logger = $logger;
    }

    // ------------------------------------------------------------------------------

    /**
     * logPrint prints the log.
     *
     * @param string $v the log.
     */
    public static function logPrint(string $v) : void
    {
        if (self::$enableLog)
        {
            self::getLoggerObj()->log(self::$logger::INFO, $v);
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * logPrintf prints the log with the format.
     *
     * @param string $format the format of the log.
     * @param mixed ...$v the log.
     */
    public static function logPrintf(string $format, ...$v) : void
    {
        if (self::$enableLog)
        {
            $tmp = sprintf($format, ...$v);

            self::getLoggerObj()->log(self::$logger::INFO, $tmp);
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * escapeAssertion escapes the dots in the assertion,
     * because the expression evaluation doesn't support such variable names.
     *
     * @param string $s the value of the matcher and effect assertions.
     * @return string the escaped value.
     */
    public static function escapeAssertion(string $s) : string
    {
        // replace the first dot, because the string doesn't start with "m="
        // and is not covered by the regex.
        if (strpos($s, Consts::R) === 0 || strpos($s, Consts::P) === 0)
        {
            $s = preg_replace('@\.@', '_', $s, 1);
        }

        $regex = '@(\|| |=|\)|\(|&|<|>|,|\+|-|!|\*|\/)('.Consts::R.'|'.Consts::P.')(\.)@';

        return preg_replace($regex, '$1$2_', $s);
    }

    // ------------------------------------------------------------------------------

    /**
     * removeComments removes the comments starting with # in the text.
     *
     * @param string $s a line in the model.
     * @return string the line without comments.
     */
    public static function removeComments(string $s) : string
    {
        $pos = strpos($s, Consts::DEFAULT_COMMENT);

        if ($pos === false) { return $s; }

        return trim(substr($s, 0, $pos));
    }

    // ------------------------------------------------------------------------------

    /**
     * arrayRemoveDuplicates removes any duplicated elements in a string array.
     *
     * @param array $s the array.
     * @return bool the array without duplicates.
     */
    public static function arrayRemoveDuplicates(array $s) : bool
    {
        return true;
    }

    // ------------------------------------------------------------------------------

    /**
     * arrayTostring gets a printable string for a string array.
     *
     * @param array $s the array.
     * @return string the string joined by the array elements.
     */
    public static function arrayToString(array $s) : string
    {
        return implode(Consts::IMPLODE_DELIMITER, $s);
    }

    // ------------------------------------------------------------------------------

    /**
     * paramsTostring gets a printable string for variable number of parameters.
     *
     * @param array $s the parameters.
     * @return string the string joined by the parameters.
     */
    public static function paramsToString(array $s) : string
    {
        return implode(Consts::IMPLODE_DELIMITER, $s);
    }

    // ------------------------------------------------------------------------------

    /**
     * arrayEquals determines whether two string arrays are identical.
     *
     * @warning numeric index only, do not use for associative array
     *
     * @param array $a the first array.
     * @param array $b the second array.
     * @return bool whether a equals to b.
     */
    public static function arrayEquals(array $a, array $b) : bool
    {
        $a === null && $a = [];
        $b === null && $b = [];

        if (count($a) !== count($b))
        {
            return false;
        }

        foreach ($a as $k => $v)
        {
            if ($v !== $b[$k])
            {
                return false;
            }
        }

        return true;
    }

    // ------------------------------------------------------------------------------

    /**
     * array2DEquals determines whether two 2-dimensional string arrays are identical.
     *
     * @warning numeric index only, do not use for associative array
     *
     * @param array $a the first 2-dimensional array.
     * @param array $b the second 2-dimensional array.
     * @return bool whether a equals to b.
     */
    public static function array2DEquals(array $a, array $b) : bool
    {
        $a === null && $a = [];
        $b === null && $b = [];

        if (count($a) !== count($b))
        {
            return false;
        }

        foreach ($a as $k => $v)
        {
            if (!self::arrayEquals($v, $b[$k]))
            {
                return false;
            }
        }

        return true;
    }

    // ------------------------------------------------------------------------------

    /**
     * setEquals determines whether two string sets are identical.
     *
     * @warning numeric index only, do not use for associative array
     *
     * @param array $a the first set.
     * @param array $b the second set.
     * @return bool whether a equals to b.
     */
    public static function setEquals(array $a, array $b) : bool
    {
        $a === null && $a = [];
        $b === null && $b = [];

        if (count($a) !== count($b))
        {
            return false;
        }

        sort($a);
        sort($b);

        foreach ($a as $k => $v)
        {
            if ($v !== $b[$k])
            {
                return false;
            }
        }

        return true;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return \Monolog\Logger
     * @throws \Exception
     */
    private static function getLoggerObj()
    {
        if (self::$logger)
        {
            return self::$logger;
        }

        if (!class_exists('\\Monolog\\Handler\\ErrorLogHandler'))
        {
            throw new GodException('Monolog not found!');
        }

        $handler      = new \Monolog\Handler\ErrorLogHandler();
        self::$logger = new \Monolog\Logger('god_logger');

        self::$logger->pushHandler($handler);
        self::$logger->addInfo('God logger is now ready');

        return self::$logger;
    }

    // ------------------------------------------------------------------------------

}
