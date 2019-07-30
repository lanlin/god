<?php namespace GodTests;

use God\God;
use God\Util\Util;
use PHPUnit\Framework\TestCase;

/**
 * ----------------------------------------------------------------------------------
 * God Util Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2019/07/30
 */
class UtilTest extends TestCase
{

    // ------------------------------------------------------------------------------

    /**
     * test escape assertion
     *
     * @throws \Exception
     */
    public function testEscapeAssertion()
    {
        assertEquals('r_attr.value == p_attr', Util::escapeAssertion('r.attr.value == p.attr'));
        assertEquals('r_attp.value || p_attr', Util::escapeAssertion('r.attp.value || p.attr'));
        assertEquals('r_attp.value &&p_attr', Util::escapeAssertion('r.attp.value &&p.attr'));
        assertEquals('r_attp.value >p_attr', Util::escapeAssertion('r.attp.value >p.attr'));
        assertEquals('r_attp.value <p_attr', Util::escapeAssertion('r.attp.value <p.attr'));
        assertEquals('r_attp.value -p_attr', Util::escapeAssertion('r.attp.value -p.attr'));
        assertEquals('r_attp.value +p_attr', Util::escapeAssertion('r.attp.value +p.attr'));
        assertEquals('r_attp.value *p_attr', Util::escapeAssertion('r.attp.value *p.attr'));
        assertEquals('r_attp.value /p_attr', Util::escapeAssertion('r.attp.value /p.attr'));
        assertEquals('!r_attp.value /p_attr', Util::escapeAssertion('!r.attp.value /p.attr'));
        assertEquals('g(r_sub, p_sub) == p_attr', Util::escapeAssertion('g(r.sub, p.sub) == p.attr'));
        assertEquals('g(r_sub,p_sub) == p_attr', Util::escapeAssertion('g(r.sub,p.sub) == p.attr'));
        assertEquals('(r_attp.value || p_attr)p_u', Util::escapeAssertion('(r.attp.value || p.attr)p.u'));
    }

    // ------------------------------------------------------------------------------

    /**
     * test remove comments
     *
     * @throws \Exception
     */
    public function testRemoveComments()
    {
        assertEquals('r.act == p.act', Util::removeComments('r.act == p.act # comments'));
        assertEquals('r.act == p.act', Util::removeComments('r.act == p.act#comments'));
        assertEquals('r.act == p.act', Util::removeComments('r.act == p.act###'));
        assertEquals('', Util::removeComments('### comments'));
        assertEquals('r.act == p.act', Util::removeComments('r.act == p.act'));
    }

    // ------------------------------------------------------------------------------

}
