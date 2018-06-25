<?php namespace God\Persist\Watcher;

/**
 * ------------------------------------------------------------------------------------
 * God Persist Watcher
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/19
 */
class Watcher
{

    // ------------------------------------------------------------------------------

    /**
     * @var callable
     */
    private $callback;

    // ------------------------------------------------------------------------------

    /**
     * SetUpdateCallback sets the callback function that the watcher will call
     * when the policy in DB has been changed by other instances.
     * A classic callback is Enforcer.loadPolicy().
     *
     * @param callable $runnable  'the callback function, will be called when policy is updated.
     */
    public function setUpdateCallback(callable $runnable) : void
    {
        $this->callback = $runnable;
    }

    // ------------------------------------------------------------------------------

    /**
     * Update calls the update callback of other instances to synchronize their policy.
     * It is usually called after changing the policy in DB, like Enforcer.savePolicy(),
     * Enforcer.addPolicy(), Enforcer.removePolicy(), etc.
     */
    public function update() : void
    {
        $func = $this->callback;

        $func();
    }

    // ------------------------------------------------------------------------------

}
