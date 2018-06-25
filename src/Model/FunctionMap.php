<?php namespace God\Model;

use God\Rbac\RoleManager;
use God\Util\FunctionFactory;
use God\Util\Functions\IPMatchFunc;
use God\Util\Functions\KeyMatchFunc;
use God\Util\Functions\KeyMatch2Func;
use God\Util\Functions\KeyMatch3Func;
use God\Util\Functions\RegexMatchFunc;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * ------------------------------------------------------------------------------------
 * God Model Function Map
 * ------------------------------------------------------------------------------------
 *
 * FunctionMap represents the collection of Function.
 *
 * @author lanlin
 * @change 2018/06/13
 */
class FunctionMap
{

    // ------------------------------------------------------------------------------

    /**
     * AbstractFunction represents a function that is used in the matchers, used to get attributes in ABAC.
     */
    public $fm = [];

    // ------------------------------------------------------------------------------

    /**
     * generateGFunction is the factory method of the g(_, _) function.
     *
     * @param string $name the name of the g(_, _) function, can be "g", "g2", ..
     * @param RoleManager $rm the role manager used by the function.
     * @return ExpressionFunction the function.
     */
    public static function generateGFunction(string $name, RoleManager $rm = null) : ExpressionFunction
    {
        $func = new FunctionFactory($name, $rm);

        return $func->getFunction();
    }

    // ------------------------------------------------------------------------------

    /**
     * addFunction adds an expression function.
     *
     * @param string $name the name of the new function.
     * @param ExpressionFunction $func the function.
     */
    public function addFunction(string $name, ExpressionFunction $func) : void
    {
        $this->fm[$name] = $func;
    }

    // ------------------------------------------------------------------------------

    /**
     * loadFunctionMap loads an initial function map.
     *
     * @return FunctionMap the constructor of FunctionMap.
     */
    public static function loadFunctionMap() : FunctionMap
    {
        $fm = new FunctionMap();

        $fm->fm = [];

        $fm->addFunction('ipMatch', (new IPMatchFunc())->getFunction());
        $fm->addFunction('keyMatch', (new KeyMatchFunc())->getFunction());
        $fm->addFunction('keyMatch2', (new KeyMatch2Func())->getFunction());
        $fm->addFunction('keyMatch3', (new KeyMatch3Func())->getFunction());
        $fm->addFunction('regexMatch', (new RegexMatchFunc())->getFunction());

        return $fm;
    }

    // ------------------------------------------------------------------------------

}
