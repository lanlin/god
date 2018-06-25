<?php namespace God\Persist\Helper;

use God\Model\Model;
use God\Config\Consts;
use God\Persist\Helper as HelperInterface;

/**
 * ------------------------------------------------------------------------------------
 * God Helper
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/19
 */
class Helper extends Fields implements HelperInterface
{

    // ------------------------------------------------------------------------------

    /**
     * load line by string (file load)
     *
     * @param string                        $line
     * @param \God\Model\Model $model
     */
    public static function loadPolicyLine(string $line, Model $model) : void
    {
        $line = trim($line);

        if (empty($line) || $line[0] === Consts::DEFAULT_COMMENT || $line[0] === Consts::DEFAULT_COMMENT_SEM)
        {
            return;
        }

        $tokens = explode(Consts::IMPLODE_DELIMITER, $line);

        $key = $tokens[0];   // fisrt elem
        $sec = $key[0];      // first letter

        $model->model[$sec][$key]->policy[] = array_slice($tokens, 1);
    }

    // ------------------------------------------------------------------------------

    /**
     * load line by array (database load)
     *
     * @param array                         $line
     * @param \God\Model\Model $model
     */
    public static function loadPolicyLine2(array $line, Model $model) : void
    {
        self::validateFields($line);  // default database fields check

        $sec = $line[self::SEC];
        $key = $line[self::KEY];

        $tokens = [];
        $fields = self::FIELDS;

        foreach ($fields as $field)
        {
            if ($field === self::SEC || $field === self::KEY)
            {
                continue;
            }

            if (!empty($line[$field]))
            {
                $tokens[] = trim($line[$field]);
            }
        }

        $model->model[$sec][$key]->policy[] = $tokens;
    }

    // ------------------------------------------------------------------------------

}
