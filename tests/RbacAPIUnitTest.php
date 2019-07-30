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
class RbacAPIUnitTest extends TestCase
{

    // ------------------------------------------------------------------------------

    public function testRoleAPI()
    {
        $e = new God(TestUtil::$path.'rbac_model.conf', TestUtil::$path.'rbac_policy.csv');

        TestUtil::testGetRoles($e, 'alice', ['data2_admin']);
        TestUtil::testGetRoles($e, 'bob', []);
        TestUtil::testGetRoles($e, 'data2_admin', []);
        TestUtil::testGetRoles($e, 'non_exist', []);

        TestUtil::testHasRole($e, 'alice', 'data1_admin', false);
        TestUtil::testHasRole($e, 'alice', 'data2_admin', true);

        $e->addRoleForUser('alice', 'data1_admin');

        TestUtil::testGetRoles($e, 'alice', ['data1_admin', 'data2_admin']);
        TestUtil::testGetRoles($e, 'bob', []);
        TestUtil::testGetRoles($e, 'data2_admin', []);

        $e->deleteRoleForUser('alice', 'data1_admin');

        TestUtil::testGetRoles($e, 'alice', ['data2_admin']);
        TestUtil::testGetRoles($e, 'bob', []);
        TestUtil::testGetRoles($e, 'data2_admin', []);

        $e->deleteRolesForUser('alice');

        TestUtil::testGetRoles($e, 'alice', []);
        TestUtil::testGetRoles($e, 'bob', []);
        TestUtil::testGetRoles($e, 'data2_admin', []);

        $e->addRoleForUser('alice', 'data1_admin');
        $e->deleteUser('alice');

        TestUtil::testGetRoles($e, 'alice', []);
        TestUtil::testGetRoles($e, 'bob', []);
        TestUtil::testGetRoles($e, 'data2_admin', []);

        $e->addRoleForUser('alice', 'data2_admin');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', true);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);

        $e->deleteRole('data2_admin');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testPermissionAPI()
    {
        $e = new God(TestUtil::$path.'basic_without_resources_model.conf', TestUtil::$path.'basic_without_resources_policy.csv');

        TestUtil::testEnforceWithoutUsers($e, 'alice', 'read', true);
        TestUtil::testEnforceWithoutUsers($e, 'alice', 'write', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'read', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'write', true);

        TestUtil::testGetPermissions($e, 'alice', [['alice', 'read']]);
        TestUtil::testGetPermissions($e, 'bob', [['bob', 'write']]);

        TestUtil::testHasPermission($e, 'alice', ['read'], true);
        TestUtil::testHasPermission($e, 'alice', ['write'], false);
        TestUtil::testHasPermission($e, 'bob', ['read'], false);
        TestUtil::testHasPermission($e, 'bob', ['write'], true);

        $e->deletePermission('read');

        TestUtil::testEnforceWithoutUsers($e, 'alice', 'read', false);
        TestUtil::testEnforceWithoutUsers($e, 'alice', 'write', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'read', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'write', true);

        $e->addPermissionForUser('bob', 'read');

        TestUtil::testEnforceWithoutUsers($e, 'alice', 'read', false);
        TestUtil::testEnforceWithoutUsers($e, 'alice', 'write', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'read', true);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'write', true);

        $e->deletePermissionForUser('bob', 'read');

        TestUtil::testEnforceWithoutUsers($e, 'alice', 'read', false);
        TestUtil::testEnforceWithoutUsers($e, 'alice', 'write', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'read', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'write', true);

        $e->deletePermissionsForUser('bob');

        TestUtil::testEnforceWithoutUsers($e, 'alice', 'read', false);
        TestUtil::testEnforceWithoutUsers($e, 'alice', 'write', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'read', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'write', false);
    }


    // ------------------------------------------------------------------------------

    public function testImplicitRoleAPI()
    {
        $e = new God("examples/rbac_model.conf", "examples/rbac_with_hierarchy_policy.csv");

        $b = ["admin", "data1_admin", "data2_admin"];
        $c = $e->getImplicitRolesForUser("alice");

        foreach ($b as $k => $v)
        {
            $this->assertEquals($v, $c[$k]);
        }
    }

    // ------------------------------------------------------------------------------

    public function testImplicitPermissionAPI()
    {
        $e = new God("examples/rbac_model.conf", "examples/rbac_with_hierarchy_policy.csv");

        $b =
        [
            ["alice", "data1", "read"],
            ["data1_admin", "data1", "read"],
            ["data1_admin", "data1", "write"],
            ["data2_admin", "data2", "read"],
            ["data2_admin", "data2", "write"]
        ];

        $c = $e->getImplicitPermissionsForUser("alice");

        foreach ($b as $k1 => $v1)
        {
            foreach ($v1 as $k2 => $v2)
            {
                $this->assertEquals($v2, $c[$k1][$k2]);
            }
        }
     }

    // ------------------------------------------------------------------------------

}
