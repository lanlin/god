<?php namespace God\Model;

use God\Util\Util;
use God\Config\Consts;
use God\Config\Config;

/**
 * ------------------------------------------------------------------------------------
 * God Model
 * ------------------------------------------------------------------------------------
 *
 * Model represents the whole access control model.
 *
 * @author lanlin
 * @change 2018/06/13
 */
class Model extends Policy
{

    // ------------------------------------------------------------------------------

    /**
     * @var array
     */
    public $model = [];

    // ------------------------------------------------------------------------------

    /**
     * addDef adds an assertion to the model.
     *
     * @param string $sec the section, "p" or "g".
     * @param string $key the policy type, "p", "p2", .. or "g", "g2", ..
     * @param string $value the policy rule, separated by ", ".
     * @return bool succeeds or not.
     */
    public function addDef(string $sec, string $key, string $value) : bool
    {
        $ast = new Assertion();

        $ast->key   = $key;
        $ast->value = $value;

        if ($ast->value === '') { return false; }

        if ($sec === Consts::R || $sec === Consts::P)
        {
            $ast->tokens = explode(Consts::IMPLODE_DELIMITER, $ast->value);

            foreach ($ast->tokens as $k => $val)
            {
                $ast->tokens[$k] = $key .'_'. $val;
            }
        }
        else
        {
            $value = Util::escapeAssertion($ast->value);

            $ast->value = Util::removeComments($value);
        }

        if (!isset($this->model[$sec]))
        {
            $this->model[$sec] = [];
        }

        $this->model[$sec][$key] = $ast;

        return true;
    }

    // ------------------------------------------------------------------------------

    /**
     * loadModel loads the model from model CONF file.
     *
     * @param string $path the path of the model file.
     */
    public function loadModel(string $path) : void
    {
        $cfg = Config::newConfig($path);

        $this->loadSection($this, $cfg, Consts::R);
        $this->loadSection($this, $cfg, Consts::P);
        $this->loadSection($this, $cfg, Consts::E);
        $this->loadSection($this, $cfg, Consts::M);
        $this->loadSection($this, $cfg, Consts::G);
    }

    // ------------------------------------------------------------------------------

    /**
     * loadModelFromText loads the model from the text.
     *
     * @param string $text the model text.
     */
    public function loadModelFromText(string $text) : void
    {
        $cfg = Config::newConfigFromText($text);

        $this->loadSection($this, $cfg, Consts::R);
        $this->loadSection($this, $cfg, Consts::P);
        $this->loadSection($this, $cfg, Consts::E);
        $this->loadSection($this, $cfg, Consts::M);
        $this->loadSection($this, $cfg, Consts::G);
    }

    // ------------------------------------------------------------------------------

    /**
     * saveModelToText saves the model to the text.
     *
     * @return string the model text.
     */
    public function saveModelToText() : string
    {
        $g = $this->saveSectionToText(Consts::G);
        $g = str_replace($g, '.', '_');

        $res  = '';
        $res .= $this->saveSectionToText(Consts::R);
        $res .= Consts::LINE_BREAK_KEEPED;
        $res .= $this->saveSectionToText(Consts::P);
        $res .= Consts::LINE_BREAK_KEEPED;
        $res .= $g;

        if (!$g) { $res .= Consts::LINE_BREAK_KEEPED; }

        $res .= $this->saveSectionToText(Consts::E);
        $res .= Consts::LINE_BREAK_KEEPED;
        $res .= $this->saveSectionToText(Consts::M);

        return $res;
    }

    // ------------------------------------------------------------------------------

    /**
     * printModel prints the model to the log.
     */
    public function printModel() : void
    {
        Util::logPrint('Model:');

        foreach ($this->model as $key => $val)
        {
            foreach ($val as $key2 => $val2)
            {
                Util::logPrintf('%s.%s: %s', $key, $key2, $val2->value);
            }
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * @param Model  $model
     * @param Config $cfg
     * @param string $sec
     * @param string $key
     * @return bool
     */
    private function loadAssertion(Model $model, Config $cfg, string $sec, string $key) : bool
    {
        $value = $cfg->getString(Consts::SECTION_MAP[$sec] .Consts::CONFIG_SPLIT. $key);

        return $model->addDef($sec, $key, $value);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param \God\Model\Model   $model
     * @param \God\Config\Config $cfg
     * @param string                          $sec
     */
    private function loadSection(Model $model, Config $cfg, string $sec) : void
    {
        $i = 1;

        while (true)
        {
            $key  = $i === 1 ? $sec : $sec.$i;  // key, key1, key2...
            $temp = $this->loadAssertion($model, $cfg, $sec, $key);

            if (!$temp) { break; }

            $i++;
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * saveSectionToText saves the section to the text.
     *
     * @param string $sec
     * @return string the section text.
     */
    private function saveSectionToText(string $sec) : string
    {
        $res = "[" . Consts::SECTION_MAP[$sec] . "]\n";

        $section = $this->model[$sec];

        if ($section === null) { return ''; }

        foreach ($section as $key => $val)
        {
            $val = str_replace($val, '_', '.');

            $res .= "{$key} = {$val}\n";
        }

        return $res;
    }

    // ------------------------------------------------------------------------------

}
