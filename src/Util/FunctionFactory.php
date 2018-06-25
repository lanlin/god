<?php namespace God\Util;

use God\Rbac\RoleManager;

/**
 * ------------------------------------------------------------------------------------
 * God Util Function Factory
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/19
 */
class FunctionFactory extends AbstractFunction
{

    // ------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $name;

    // ------------------------------------------------------------------------------

    /**
     * @var \God\Rbac\RoleManager
     */
    private $rm = null;

    // ------------------------------------------------------------------------------

    /**
     * AbstrctFunctions constructor.
     *
     * @param string                             $name
     * @param \God\Rbac\RoleManager $rm
     */
    public function __construct(string $name, RoleManager $rm = null)
    {
        $this->rm   = $rm;
        $this->name = $name;
    }

    // ------------------------------------------------------------------------------

    /**
     * get evaluator
     *
     * @return \Closure
     */
    protected function getEvaluator() : callable
    {
        return function ($args, $name1, $name2, $name3 = null)
        {
            if ($this->rm === null)
            {
                return ($name1 === $name2);
            }

            $domain = $name3 ?? null;

            return $this->rm->hasLink($name1, $name2, $domain);
        };
    }

    // ------------------------------------------------------------------------------

}
