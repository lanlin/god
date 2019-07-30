<?php namespace God\Config;

use God\Exception\GodConfigException;

/**
 * ------------------------------------------------------------------------------------
 * God Config
 * ------------------------------------------------------------------------------------
 *
 * @tips Remove all "\r" character from the buffer string, & get a line with "\n".
 *
 * @author lanlin
 * @change 2019/07/30
 */
class Config
{

    // ------------------------------------------------------------------------------

    /**
     * configs
     *
     * @var array
     */
    private $data = [];

    // ------------------------------------------------------------------------------

    /**
     * newConfig create an empty configuration representation from file.
     *
     * @param string $confName the path of the model file.
     * @return Config the constructor of Config.
     */
    public static function newConfig(string $confName) : Config
    {
        $c = new self();

        try
        {
            $c->parse($confName);
        }
        catch (\Throwable $e)
        {
            throw new GodConfigException($e->getMessage(), $e->getCode(), $e);
        }

        return $c;
    }

    // ------------------------------------------------------------------------------

    /**
     * newConfigFromText create an empty configuration representation from text.
     *
     * @param string $text the model text.
     * @return Config the constructor of Config.
     */
    public static function newConfigFromText(string $text) : Config
    {
        $c = new self();

        try
        {
            $c->parseBuffer($text);
        }
        catch (\Throwable $e)
        {
            throw new GodConfigException($e->getMessage(), $e->getCode(), $e);
        }

        return $c;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $key
     * @return bool
     */
    public function getBool(string $key) : bool
    {
        return (bool) $this->get($key);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $key
     * @return int
     */
    public function getInt(string $key) : int
    {
        return (int) $this->get($key);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $key
     * @return float
     */
    public function getFloat(string $key) : float
    {
        return (float) $this->get($key);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $key
     * @return string
     */
    public function getString(string $key) : string
    {
        return $this->get($key);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $key
     * @return array
     */
    public function getStrings(string $key) : array
    {
         $v = $this->get($key);

        return $v === '' ? [] : explode(',', $v);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $key
     * @param string $value
     * @throws GodConfigException
     */
    public function set(string $key, string $value) : void
    {
        if (strlen($key) === 0)
        {
            throw new GodConfigException('key is empty');
        }

        $keys = explode(Consts::CONFIG_SPLIT, strtolower($key));

        $option  = $keys[0] ?? '';
        $section = '';

        if (count($keys) >= 2)
        {
            $option  = $keys[1];
            $section = $keys[0];
        }

        $this->addConfig($section, $option, $value);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $key
     * @return string
     */
    public function get(string $key) : string
    {
        $keys = explode(Consts::CONFIG_SPLIT, strtolower($key));

        $option  = $keys[0];
        $section = Consts::DEFAULT_SECTION;

        if (count($keys) >= 2)
        {
            $option  = $keys[1];
            $section = $keys[0];
        }

        $ok = isset($this->data[$section][$option]);

        return $ok ? $this->data[$section][$option] : '';
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private function startsWith(string $haystack, string $needle) : bool
    {
        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private function endsWith(string $haystack, string $needle) : bool
    {
        $length = strlen($needle);

        return $length === 0 || (substr($haystack, -$length) === $needle);
    }

    // ------------------------------------------------------------------------------

    /**
     * addConfig adds a new section->key:value to the configuration.
     *
     * @param string $section
     * @param string $option
     * @param string $value
     */
    private function addConfig(string $section, string $option, string $value)
    {
        if ($section === '')
        {
            $section = Consts::DEFAULT_SECTION;
        }

        if (!isset($this->data[$section]))
        {
            $this->data[$section] = [];
        }

        $this->data[$section][$option] = $value;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $fname
     */
    private function parse(string $fname) : void
    {
        $content = file_get_contents($fname);

        $this->parseBuffer($content);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param string $strBuffer
     * @throws GodConfigException
     */
    private function parseBuffer(string $strBuffer) : void
    {
        $section   = '';
        $lineNum   = 0;
        $strBuffer = str_replace(Consts::LINE_BREAK_REPLACED, '', $strBuffer);

        while (true)
        {
            $line = $lineNum === 0 ?
            strtok($strBuffer, Consts::LINE_BREAK_KEEPED) : strtok(Consts::LINE_BREAK_KEEPED);

            if ($line === false) { break; }

            $line = trim($line);
            $secA = $line === '';
            $secB = $this->startsWith($line, Consts::DEFAULT_COMMENT);
            $secC = $this->startsWith($line, Consts::DEFAULT_COMMENT_SEM);

            $lineNum++;

            if ($secA || $secB || $secC) { continue; }

            if ($this->startsWith($line, '[') && $this->endsWith($line, ']'))
            {
                $section = substr($line, 1, strlen($line) - 2);
                continue;
            }

            $optionVal = explode('=', $line, 2);

            if (count($optionVal) !== 2)
            {
                $fmt = 'parse the content error : line %d , %s = ? ';
                $msg = sprintf($fmt, $lineNum, $optionVal[0]);

                throw new GodConfigException($msg);
            }

            $this->addConfig($section, trim($optionVal[0]), trim($optionVal[1]));
        }
    }

    // ------------------------------------------------------------------------------

}
