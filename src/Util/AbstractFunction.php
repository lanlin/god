<?php namespace God\Util;

use God\Config\Consts;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * ------------------------------------------------------------------------------------
 * God Util Abstract Function
 * ------------------------------------------------------------------------------------
 *
 * @link http://www.symfonychina.com/doc/current/components/expression_language.html
 *
 * @author lanlin
 * @change 2018/06/19
 */
abstract class AbstractFunction
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $name;

    // ------------------------------------------------------------------------------

    /**
     * get expression function
     *
     * @return \Symfony\Component\ExpressionLanguage\ExpressionFunction
     */
    public function getFunction() : ExpressionFunction
    {
        return new ExpressionFunction(
            $this->name,
            $this->getCompiler(),
            $this->getEvaluator()
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * get regex pattern
     *
     * @param string $pattern
     * @return string
     */
    public static function getRegexPattern(string $pattern)
    {
        return Consts::REGEX_DELIMITER . $pattern . Consts::REGEX_DELIMITER;
    }

    // ------------------------------------------------------------------------------

    /**
     * get compiler function
     *
     * @return callable
     */
    protected function getCompiler() : callable
    {
        return function ()
        {
            return sprintf(
            '\%s(%s)',
                $this->name,
                implode(Consts::IMPLODE_DELIMITER, func_get_args())
            );
        };
    }

    // ------------------------------------------------------------------------------

    /**
     * get evaluator function
     *
     * @return callable
     */
    abstract protected function getEvaluator() : callable;

    // ------------------------------------------------------------------------------

}
