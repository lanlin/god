<?php namespace God\Config;

/**
 * ------------------------------------------------------------------------------------
 * God Config Consts
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/20
 */
class Consts
{

    // ------------------------------------------------------------------------------

    /**
     * section short character
     */
    const R = 'r';
    const P = 'p';
    const G = 'g';
    const E = 'e';
    const M = 'm';

    // ------------------------------------------------------------------------------

    /**
     * section maps
     */
    const SECTION_MAP =
    [
        self::R => 'request_definition',
        self::P => 'policy_definition',
        self::G => 'role_definition',
        self::E => 'policy_effect',
        self::M => 'matchers'
    ];

    // ------------------------------------------------------------------------------

    /**
     * config string delimiter characters
     */
    const CONFIG_SPLIT = '::';

    // ------------------------------------------------------------------------------

    /**
     * default section
     */
    const DEFAULT_SECTION = 'default';

    // ------------------------------------------------------------------------------

    /**
     * default comment character
     */
    const DEFAULT_COMMENT = '#';

    // ------------------------------------------------------------------------------

    /**
     * default comment sem
     */
    const DEFAULT_COMMENT_SEM = ';';

    // ------------------------------------------------------------------------------

    /**
     * php regex match delimiter character
     *
     * demo: ;[abc]+;
     */
    const REGEX_DELIMITER = ';';

    // ------------------------------------------------------------------------------

    /**
     * the default maximized allowed RBAC hierarchy level.
     */
    const MAX_HIERARCHY_LEVEL = 10;

    // ------------------------------------------------------------------------------

    /**
     * the line break that beed be keeped
     *
     * @warning do not change this
     */
    const LINE_BREAK_KEEPED = "\n";

    // ------------------------------------------------------------------------------

    /**
     * the line break that need be replaced
     *
     * @warning do not change this
     */
    const LINE_BREAK_REPLACED = "\r";

    // ------------------------------------------------------------------------------

    /**
     * implode string by this delimiter
     *
     * @warning do not change this (include a space here)
     */
    const IMPLODE_DELIMITER = ', ';

    // ------------------------------------------------------------------------------

}
