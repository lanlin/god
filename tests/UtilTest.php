<?php namespace GodTests;

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
        $this->assertEquals('r_attr.value == p_attr', Util::escapeAssertion('r.attr.value == p.attr'));
        $this->assertEquals('r_attp.value || p_attr', Util::escapeAssertion('r.attp.value || p.attr'));
        $this->assertEquals('r_attp.value &&p_attr', Util::escapeAssertion('r.attp.value &&p.attr'));
        $this->assertEquals('r_attp.value >p_attr', Util::escapeAssertion('r.attp.value >p.attr'));
        $this->assertEquals('r_attp.value <p_attr', Util::escapeAssertion('r.attp.value <p.attr'));
        $this->assertEquals('r_attp.value -p_attr', Util::escapeAssertion('r.attp.value -p.attr'));
        $this->assertEquals('r_attp.value +p_attr', Util::escapeAssertion('r.attp.value +p.attr'));
        $this->assertEquals('r_attp.value *p_attr', Util::escapeAssertion('r.attp.value *p.attr'));
        $this->assertEquals('r_attp.value /p_attr', Util::escapeAssertion('r.attp.value /p.attr'));
        $this->assertEquals('!r_attp.value /p_attr', Util::escapeAssertion('!r.attp.value /p.attr'));
        $this->assertEquals('g(r_sub, p_sub) == p_attr', Util::escapeAssertion('g(r.sub, p.sub) == p.attr'));
        $this->assertEquals('g(r_sub,p_sub) == p_attr', Util::escapeAssertion('g(r.sub,p.sub) == p.attr'));
        $this->assertEquals('(r_attp.value || p_attr)p_u', Util::escapeAssertion('(r.attp.value || p.attr)p.u'));
    }

    // ------------------------------------------------------------------------------

    /**
     * test remove comments
     *
     * @throws \Exception
     */
    public function testRemoveComments()
    {
        $this->assertEquals('r.act == p.act', Util::removeComments('r.act == p.act # comments'));
        $this->assertEquals('r.act == p.act', Util::removeComments('r.act == p.act#comments'));
        $this->assertEquals('r.act == p.act', Util::removeComments('r.act == p.act###'));
        $this->assertEquals('', Util::removeComments('### comments'));
        $this->assertEquals('r.act == p.act', Util::removeComments('r.act == p.act'));
    }

    // ------------------------------------------------------------------------------

}
