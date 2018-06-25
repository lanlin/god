<?php namespace God\Persist\Helper;

use God\Exception\GodException;

/**
 * ------------------------------------------------------------------------------------
 * God Fields Helper
 * ------------------------------------------------------------------------------------
 *
 * Define database default policy fields
 *
 * @author lanlin
 * @change 2018/06/22
 */
class Fields
{

    // ------------------------------------------------------------------------------

    /**
     * policy section field
     */
    const SEC = 'sec';

    // ------------------------------------------------------------------------------

    /**
     * policy key field
     */
    const KEY = 'key';

    // ------------------------------------------------------------------------------

    /**
     * database default fields
     */
    const FIELDS = [self::SEC, self::KEY, 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'];

    // ------------------------------------------------------------------------------

    /**
     * format values with fields
     *
     * @param array $value
     * @param int   $start
     * @return array
     */
    public static function formatValues(array $value, int $start = 0)
    {
        $temp  = [];
        $value = array_values($value);

        foreach ($value as $key => $val)
        {
            if ($val === '' || $val === null)
            {
                continue;
            }

            $field = self::FIELDS[$key + $start + 2];

            $temp[$field] = $val;
        }

        return $temp;
    }

    // ------------------------------------------------------------------------------

    /**
     * concat fields before save to database
     *
     * @param array $line
     * @param bool  $isMulti
     * @return array
     */
    public static function concatFields(array $line, bool $isMulti = false) : array
    {
        $line = $isMulti ? $line : [$line];

        $temp = array_fill_keys(self::FIELDS, '');

        foreach ($line as $key => $arr)
        {
            $line[$key] = array_merge($temp, $arr);

            self::validateFields($line[$key]);
        }

        return $isMulti ? $line : end($line);
    }

    // ------------------------------------------------------------------------------

    /**
     * validate if fields matched
     *
     * @param array $line
     * @throws \God\Exception\GodException
     */
    public static function validateFields(array $line)
    {
        unset($line['_id']);

        $lineKy = array_keys($line);

        if (array_diff($lineKy, self::FIELDS))
        {
            throw new GodException('invalid field found');
        }

        if (!$line[self::SEC] || !$line[self::KEY])
        {
            throw new GodException('invalid value for sec or key');
        }
    }

    // ------------------------------------------------------------------------------

}
