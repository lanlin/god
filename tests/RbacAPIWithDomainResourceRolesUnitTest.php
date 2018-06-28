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
class RbacAPIWithDomainResourceRolesUnitTest extends TestCase
{

    // ------------------------------------------------------------------------------

    public function testDomainResourceRoles()
    {
        $e = new God(
            TestUtil::$path.'rbac_with_domain_resource_roles_model.conf',
            TestUtil::$path.'rbac_with_domain_resource_roles_policy.csv'
        );

        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data1', 'read', true);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data1', 'write', true);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data2', 'read', false);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

}
