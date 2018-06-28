<?php namespace GodTests;

use God\God;
use PHPUnit\Framework\TestCase;

/**
 * ------------------------------------------------------------------------------------
 * God Test RBAC
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/28
 */
class RbacAPIWithResourceRolesUnitTest extends TestCase
{

    // ------------------------------------------------------------------------------

    public function testResourceRoles()
    {
        $e = new God(TestUtil::$path.'rbac_with_resource_roles_model.conf', TestUtil::$path.'rbac_with_resource_roles_policy.csv');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', true);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', true);
        TestUtil::testEnforce($e, 'alice', 'data3', 'write', true);
    }

    // ------------------------------------------------------------------------------

}
