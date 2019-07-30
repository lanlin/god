<?php namespace God\Enforcer;

use God\Util\Util;
use God\Config\Consts;
use God\Model\Model;
use God\Model\FunctionMap;
use God\Effect\Effect;
use God\Effect\Effector;
use God\Effect\DefaultEffector;
use God\Persist\Watcher;
use God\Persist\Adapter;
use God\Persist\AdapterFiltered;
use God\Rbac\RoleManager;
use God\Rbac\DefaultRoleManager;
use God\Exception\GodException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage as Express;

/**
 * ------------------------------------------------------------------------------------
 * God Core Enforcer
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2019/07/30
 */
class Core
{

    // ------------------------------------------------------------------------------

    /** @var Effector */
    private $eft;

    /** @var bool */
    private $enabled;

    /** @var array */
    private $functions = [];

    // ------------------------------------------------------------------------------

    /** @var FunctionMap */
    protected $fm;

    /** @var RoleManager */
    protected $rm;

    /** @var Adapter */
    protected $adapter;

    /** @var Model */
    protected $model;

    /** @var string */
    protected $modelPath;

    /** @var Watcher */
    protected $watcher;

    /** @var bool */
    protected $autoSave;

    /** @var bool */
    protected $autoBuildRoleLinks;

    // ------------------------------------------------------------------------------

    /**
     * initialize default setting
     */
    public function initialize() : void
    {
        $this->eft = new DefaultEffector();
        $this->rm  = new DefaultRoleManager(Consts::MAX_HIERARCHY_LEVEL);

        $this->watcher  = null;
        $this->enabled  = true;
        $this->autoSave = true;

        $this->autoBuildRoleLinks = true;
    }

    // ------------------------------------------------------------------------------

    /**
     * newModel creates a model.
     *
     * @param mixed ...$params
     * @return Model an empty model.
     */
    public static function newModel(...$params) : Model
    {
        $model = new Model();
        $count = $params ? count($params) : 0;

        if ($count === 1)
        {
            $model->loadModelFromText($params[0]);
        }

        if ($count === 2 && $params[0])
        {
            $model->loadModel($params[0]);
        }

        return $model;
    }

    // ------------------------------------------------------------------------------

    /**
     * loadModel reloads the model from the model CONF file.
     * Because the policy is attached to a model, so the policy is invalidated
     * and needs to be reloaded by calling LoadPolicy().
     */
    public function loadModel() : void
    {
        $model = $this->newModel();

        $model->loadModel($this->modelPath);
        $model->printModel();

        $this->fm = FunctionMap::loadFunctionMap();
    }

    // ------------------------------------------------------------------------------

    /**
     * getModel gets the current model.
     *
     * @return Model the model of the enforcer.
     */
    public function getModel() : Model
    {
        return $this->model;
    }

    // ------------------------------------------------------------------------------

    /**
     * setModel sets the current model.
     *
     * @param Model $model the model.
     */
    public function setModel(Model $model) : void
    {
        $this->model = $model;

        $this->fm = FunctionMap::loadFunctionMap();
    }

    // ------------------------------------------------------------------------------

    /**
     * getAdapter gets the current adapter.
     *
     * @return Adapter the adapter of the enforcer.
     */
    public function getAdapter() : Adapter
    {
        return $this->adapter;
    }

    // ------------------------------------------------------------------------------

    /**
     * setAdapter sets the current adapter.
     *
     * @param Adapter $adapter the adapter.
     */
    public function setAdapter(Adapter $adapter) : void
    {
        $this->adapter = $adapter;
    }

    // ------------------------------------------------------------------------------

    /**
     * setWatcher sets the current watcher.
     *
     * @param Watcher $watcher the watcher.
     */
    public function setWatcher(Watcher $watcher): void
    {
        $this->watcher = $watcher;

        $this->watcher->setUpdateCallback(function ()
        {
            $this->loadPolicy();
        });
    }

    // ------------------------------------------------------------------------------

    /**
     * SetRoleManager sets the current role manager.
     *
     * @param RoleManager $rm the role manager.
     */
    public function setRoleManager(RoleManager $rm) : void
    {
        $this->rm = $rm;
    }

    // ------------------------------------------------------------------------------

    /**
     * setEffector sets the current effector.
     *
     * @param Effector $eft the effector.
     */
    public function setEffector(Effector $eft) : void
    {
        $this->eft = $eft;
    }

    // ------------------------------------------------------------------------------

    /**
     * clearPolicy clears all policy.
     */
    public function clearPolicy() : void
    {
        $this->model->clearPolicy();
    }

    // ------------------------------------------------------------------------------

    /**
     * loadPolicy reloads the policy from file/database.
     */
    public function loadPolicy() : void
    {
        $this->model->clearPolicy();

        $this->adapter->loadPolicy($this->model);

        $this->model->printPolicy();

        if ($this->autoBuildRoleLinks)
        {
            $this->buildRoleLinks();
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * isFiltered returns true if the loaded policy has been filtered.
     *
     * @return bool if the loaded policy has been filtered.
     */
    public function isFiltered() : bool
    {
        $check = $this->adapter instanceof AdapterFiltered;

        /** @var AdapterFiltered $adapterFiltered */
        $adapterFiltered = $this->adapter;

        return $check ? $adapterFiltered->isFiltered() : false;
    }

    // ------------------------------------------------------------------------------

    /**
     * loadFilteredPolicy reloads a filtered policy from file/database.
     *
     * @param mixed $filter the filter used to specify which type of policy should be loaded.
     */
    public function loadFilteredPolicy($filter) : void
    {
        $this->model->clearPolicy();

        if (!$this->adapter instanceof AdapterFiltered)
        {
            throw new GodException('filtered policies are not supported by this adapter');
        }

        /** @var AdapterFiltered $adapterFiltered */
        $adapterFiltered = $this->adapter;

        $adapterFiltered->loadFilteredPolicy($this->model, $filter);

        $this->model->PrintPolicy();

        if ($this->autoBuildRoleLinks)
        {
            $this->buildRoleLinks();
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * savePolicy saves the current policy (usually after changed with
     * God API) back to file/database.
     */
    public function savePolicy() : void
    {
        if ($this->isFiltered())
        {
            throw new GodException('cannot save a filtered policy');
        }

        $this->adapter->savePolicy($this->model);

        if ($this->watcher !== null)
        {
            $this->watcher->update();
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * enableEnforce changes the enforcing state of God, when God is
     * disabled, all access will be allowed by the enforce() function.
     *
     * @param bool $enable whether to enable the enforcer.
     */
    public function enableEnforce(bool $enable) : void
    {
        $this->enabled = $enable;
    }

    // ------------------------------------------------------------------------------

    /**
     * enableLog changes whether to print God log to the standard output.
     *
     * @param bool $enable whether to enable God's log.
     */
    public function enableLog(bool $enable) : void
    {
        Util::$enableLog = $enable;
    }

    // ------------------------------------------------------------------------------

    /**
     * enableAutoSave controls whether to save a policy rule automatically to
     * the adapter when it is added or removed.
     *
     * @param bool $autoSave whether to enable the AutoSave feature.
     */
    public function enableAutoSave(bool $autoSave) : void
    {
        $this->autoSave = $autoSave;
    }

    // ------------------------------------------------------------------------------

    /**
     * enableAutoBuildRoleLinks controls whether to save a policy rule
     * automatically to the adapter when it is added or removed.
     *
     * @param bool $autoBuildRoleLinks whether to automatically build the role links.
     */
    public function enableAutoBuildRoleLinks(bool $autoBuildRoleLinks) : void
    {
        $this->autoBuildRoleLinks = $autoBuildRoleLinks;
    }

    // ------------------------------------------------------------------------------

    /**
     * buildRoleLinks manually rebuild the
     * role inheritance relations.
     */
    public function buildRoleLinks() : void
    {
        $this->rm->clear();

        $this->model->buildRoleLinks($this->rm);
    }

    // ------------------------------------------------------------------------------

    /**
     * God decides whether a "subject" can access a "object" with
     * the operation "action", input parameters are usually: (sub, obj, act).
     *
     * @param mixed ...$rvals the request needs to be mediated, usually an array
     *              of strings, can be class instances if ABAC is used.
     * @return bool whether to allow the request.
     */
    public function allows(...$rvals) : bool
    {
        if (!$this->enabled) { return true; }

        $express = new Express();

        $this->loadFunctions($express);

        $policyObj = $this->model->model[Consts::P][Consts::P];
        $hasPolicy = count($policyObj->policy);

        $reqStr = 'Request: ';
        $result = $hasPolicy ? $this->sectionHasPolicy($rvals, $express) : $this->sectionNoPolicy($rvals, $express);

        foreach ($rvals as $key => $rval)
        {
            $check   = $key !== count($rvals) - 1;
            $reqStr .= $check ? sprintf('%s, ', $rval) : sprintf('%s', $rval);
        }

        $reqStr .= sprintf(' ---> %s', $result);

        Util::logPrint($reqStr);

        return $result;
    }

    // ------------------------------------------------------------------------------

    /**
     * load fucntions
     *
     * @param \Symfony\Component\ExpressionLanguage\ExpressionLanguage $express
     */
    private function loadFunctions(Express $express)
    {
        foreach ($this->fm->fm as $key => $func)
        {
            $this->functions[$key] = $func;
        }

        if (isset($this->model->model[Consts::G]))
        {
            foreach ($this->model->model[Consts::G] as $key => $ast)
            {
                $this->functions[$key] = FunctionMap::generateGFunction($key, $ast->rm);
            }
        }

        foreach ($this->functions as $func)
        {
            $express->addFunction($func);
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * when no policy
     *
     * @param array                                                    $rvals
     * @param \Symfony\Component\ExpressionLanguage\ExpressionLanguage $express
     * @return bool
     */
    private function sectionNoPolicy(array $rvals, Express $express)
    {
        $parameters     = [];
        $policyEffects  = [];
        $matcherResults = [];

        $policyObj  = $this->model->model[Consts::P][Consts::P];
        $effectObj  = $this->model->model[Consts::E][Consts::E];
        $requestObj = $this->model->model[Consts::R][Consts::R];
        $expression = $this->model->model[Consts::M][Consts::M]->value;

        foreach ($requestObj->tokens as $key => $token)
        {
            $parameters[$token] = $rvals[$key];
        }

        foreach ($policyObj->tokens as $key => $token)
        {
            $parameters[$token] = '';
        }

        $result = $express->evaluate($expression, $parameters);

        $policyEffects[0] = $result ? Effect::Allow : Effect::Indeterminate;

        return $this->eft->mergeEffects($effectObj->value, $policyEffects, $matcherResults);
    }

    // ------------------------------------------------------------------------------

    /**
     * when has policy
     *
     * @param array                                                    $rvals
     * @param \Symfony\Component\ExpressionLanguage\ExpressionLanguage $express
     * @return bool
     * @throws \God\Exception\GodException
     */
    private function sectionHasPolicy(array $rvals, Express $express)
    {
        $policyEffects  = [];
        $matcherResults = [];

        $policyObj  = $this->model->model[Consts::P][Consts::P];
        $effectObj  = $this->model->model[Consts::E][Consts::E];
        $requestObj = $this->model->model[Consts::R][Consts::R];
        $expression = $this->model->model[Consts::M][Consts::M]->value;

        foreach ($policyObj->policy as $key => $pvals)
        {
            $parameters = [];

            foreach ($requestObj->tokens as $j => $token)
            {
                $parameters[$token] = $rvals[$j];
            }

            foreach ($policyObj->tokens as $j => $token)
            {
                $parameters[$token] = $pvals[$j];
            }

            $result = $express->evaluate($expression, $parameters);

            $status = $this->setPolicyEffects($policyEffects, $matcherResults, $key, $result);

            if (!$status) { continue; }

            $this->checkPolicyLeft($policyEffects, $parameters, $key);

            if ($effectObj->value === ('priority(p_eft) || deny')) { break; }
        }

        return $this->eft->mergeEffects($effectObj->value, $policyEffects, $matcherResults);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param array $policyEffects
     * @param array $parameters
     * @param int   $key
     */
    private function checkPolicyLeft(array &$policyEffects, array $parameters, int $key)
    {
        if (!isset($parameters[Consts::P.'_eft']))
        {
            return;
        }

        $eft = (string) $parameters[Consts::P.'_eft'];

        switch ($eft)
        {
            case 'deny':  $policyEffects[$key] = Effect::Deny; break;
            case 'allow': $policyEffects[$key] = Effect::Allow; break;
            default:      $policyEffects[$key] = Effect::Indeterminate;
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * @param array $policyEffects
     * @param array $matcherResults
     * @param int   $key
     * @param       $result
     * @return bool
     * @throws \God\Exception\GodException
     */
    private function setPolicyEffects(array &$policyEffects, array &$matcherResults, int $key, $result)
    {
        if (!is_bool($result) && !is_int($result))
        {
            throw new GodException('matcher result should be bool or int');
        }

        if (is_bool($result) && !$result)
        {
            $policyEffects[$key] = Effect::Indeterminate;

            return false;
        }

        if (is_int($result))
        {
            $matcherResults[$key] = $result;

            if ($result === 0)
            {
                $policyEffects[$key] = Effect::Indeterminate;

                return false;
            }
        }

        $policyEffects[$key] = Effect::Allow;

        return true;
    }

    // ------------------------------------------------------------------------------

}
