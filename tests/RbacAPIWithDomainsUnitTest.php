<?php namespace GodTests;

use God\God;
use PHPUnit\Framework\TestCase;

/**
 * ------------------------------------------------------------------------------------
 * God Test RBAC
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/19
 */
class RbacAPIWithDomainsUnitTest extends TestCase
{
    // ------------------------------------------------------------------------------

    public function testRoleAPIWithDomains()
    {
        $e = new God(TestUtil::$path.'rbac_with_domains_model.conf', TestUtil::$path.'rbac_with_domains_policy.csv');

        TestUtil::testGetRolesInDomain($e, 'alice', 'domain1', ['admin']);
        TestUtil::testGetRolesInDomain($e, 'bob', 'domain1', []);
        TestUtil::testGetRolesInDomain($e, 'admin', 'domain1', []);
        TestUtil::testGetRolesInDomain($e, 'non_exist', 'domain1', []);


        TestUtil::testGetRolesInDomain($e, 'alice', 'domain2', []);
        TestUtil::testGetRolesInDomain($e, 'bob', 'domain2', ['admin']);
        TestUtil::testGetRolesInDomain($e, 'admin', 'domain2', []);
        TestUtil::testGetRolesInDomain($e, 'non_exist', 'domain2', []);

        $e->deleteRoleForUserInDomain('alice', 'admin', 'domain1');
        $e->addRoleForUserInDomain('bob', 'admin', 'domain1');

        TestUtil::testGetRolesInDomain($e, 'alice', 'domain1', []);
        TestUtil::testGetRolesInDomain($e, 'bob', 'domain1', ['admin']);
        TestUtil::testGetRolesInDomain($e, 'admin', 'domain1', []);
        TestUtil::testGetRolesInDomain($e, 'non_exist', 'domain1', []);

        TestUtil::testGetRolesInDomain($e, 'alice', 'domain2', []);
        TestUtil::testGetRolesInDomain($e, 'bob', 'domain2', ['admin']);
        TestUtil::testGetRolesInDomain($e, 'admin', 'domain2', []);
        TestUtil::testGetRolesInDomain($e, 'non_exist', 'domain2', []);
    }

    // ------------------------------------------------------------------------------

    public function testPermissionAPIInDomain()
    {
        $e = new God(TestUtil::$path.'rbac_with_domains_model.conf', TestUtil::$path.'rbac_with_domains_policy.csv');

        TestUtil::testGetPermissionsInDomain($e, 'alice', 'domain1', []);
        TestUtil::testGetPermissionsInDomain($e, 'bob', 'domain1', []);
        TestUtil::testGetPermissionsInDomain($e, 'admin', 'domain1', [['admin', 'domain1', 'data1', 'read'], ['admin', 'domain1', 'data1', 'write']]);
        TestUtil::testGetPermissionsInDomain($e, 'non_exist', 'domain1', []);

        TestUtil::testGetPermissionsInDomain($e, 'alice', 'domain2', []);
        TestUtil::testGetPermissionsInDomain($e, 'bob', 'domain2', []);
        TestUtil::testGetPermissionsInDomain($e, 'admin', 'domain2', [['admin', 'domain2', 'data2', 'read'], ['admin', 'domain2', 'data2', 'write']]);
        TestUtil::testGetPermissionsInDomain($e, 'non_exist', 'domain2', []);
    }

    // ------------------------------------------------------------------------------

}
