<?php namespace GodTests;

use God\God;
use PHPUnit\Framework\TestCase;

/**
 * ----------------------------------------------------------------------------------
 * God Tests Management Api
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/06/19
 */
class ManagementAPIUnitTest extends TestCase
{

    // ------------------------------------------------------------------------------

    public function testGetPolicyAPI()
    {
        $e = new God(TestUtil::$path.'rbac_model.conf', TestUtil::$path.'rbac_policy.csv');

        TestUtil::testGetPolicy($e, [
            ['alice', 'data1', 'read'],
            ['bob', 'data2', 'write'],
            ['data2_admin', 'data2', 'read'],
            ['data2_admin', 'data2', 'write']
        ]);

        TestUtil::testGetFilteredPolicy($e, 0, [['alice', 'data1', 'read']], 'alice');
        TestUtil::testGetFilteredPolicy($e, 0, [[ 'bob', 'data2', 'write']], 'bob');
        TestUtil::testGetFilteredPolicy($e, 0, [[ 'data2_admin', 'data2', 'read'], ['data2_admin', 'data2', 'write']], 'data2_admin');
        TestUtil::testGetFilteredPolicy($e, 1, [[ 'alice', 'data1', 'read']], 'data1');
        TestUtil::testGetFilteredPolicy($e, 1, [[ 'bob', 'data2', 'write'], ['data2_admin', 'data2', 'read'], ['data2_admin', 'data2', 'write']], 'data2');
        TestUtil::testGetFilteredPolicy($e, 2, [[ 'alice', 'data1', 'read'], ['data2_admin', 'data2', 'read']], 'read');
        TestUtil::testGetFilteredPolicy($e, 2, [[ 'bob', 'data2', 'write'], ['data2_admin', 'data2', 'write']], 'write');

        TestUtil::testGetFilteredPolicy($e, 0, [['data2_admin', 'data2', 'read'], ['data2_admin', 'data2', 'write']], 'data2_admin', 'data2');

        // Note: '' (empty string) in fieldValues means matching all values.
        TestUtil::testGetFilteredPolicy($e, 0, [['data2_admin', 'data2', 'read']], 'data2_admin', '', 'read');
        TestUtil::testGetFilteredPolicy($e, 1, [['bob', 'data2', 'write'], ['data2_admin', 'data2', 'write']], 'data2', 'write');

        TestUtil::testHasPolicy($e, ['alice', 'data1', 'read'], true);
        TestUtil::testHasPolicy($e, ['bob', 'data2', 'write'], true);
        TestUtil::testHasPolicy($e, ['alice', 'data2', 'read'], false);
        TestUtil::testHasPolicy($e, ['bob', 'data3', 'write'], false);

        TestUtil::testGetGroupingPolicy($e, [['alice', 'data2_admin']]);

        TestUtil::testGetFilteredGroupingPolicy($e, 0, [['alice', 'data2_admin']], 'alice');
        TestUtil::testGetFilteredGroupingPolicy($e, 0, [], 'bob');
        TestUtil::testGetFilteredGroupingPolicy($e, 1, [], 'data1_admin');
        TestUtil::testGetFilteredGroupingPolicy($e, 1, [['alice', 'data2_admin']], 'data2_admin');
        // Note: '' (empty string) in fieldValues means matching all values.
        TestUtil::testGetFilteredGroupingPolicy($e, 0, [['alice', 'data2_admin']], '', 'data2_admin');

        TestUtil::testHasGroupingPolicy($e, ['alice', 'data2_admin'], true);
        TestUtil::testHasGroupingPolicy($e, ['bob', 'data2_admin'], false);
    }

    // ------------------------------------------------------------------------------

    public function testModifyPolicyAPI()
    {
        $e = new God(TestUtil::$path.'rbac_model.conf', TestUtil::$path.'rbac_policy.csv');

        TestUtil::testGetPolicy($e, [
            ['alice', 'data1', 'read'],
            ['bob', 'data2', 'write'],
            ['data2_admin', 'data2', 'read'],
            ['data2_admin', 'data2', 'write']
        ]);

        $e->removePolicy('alice', 'data1', 'read');
        $e->removePolicy('bob', 'data2', 'write');
        $e->removePolicy('alice', 'data1', 'read');
        $e->addPolicy('eve', 'data3', 'read');
        $e->addPolicy('eve', 'data3', 'read');

        $namedPolicy = ['eve', 'data3', 'read'];
        $e->removeNamedPolicy('p', $namedPolicy);
        $e->addNamedPolicy('p', $namedPolicy);

        TestUtil::testGetPolicy($e, [
            ['data2_admin', 'data2', 'read'],
            ['data2_admin', 'data2', 'write'],
            ['eve', 'data3', 'read']
        ]);

        $e->removeFilteredPolicy(1, 'data2');

        TestUtil::testGetPolicy($e, [['eve', 'data3', 'read']]);
    }

    // ------------------------------------------------------------------------------

    public function testModifyGroupingPolicyAPI()
    {
        $e = new God(TestUtil::$path.'rbac_model.conf', TestUtil::$path.'rbac_policy.csv');

        TestUtil::testGetRoles($e, 'alice', ['data2_admin']);
        TestUtil::testGetRoles($e, 'bob', []);
        TestUtil::testGetRoles($e, 'eve',[] );
        TestUtil::testGetRoles($e, 'non_exist', []);

        $e->removeGroupingPolicy('alice', 'data2_admin');
        $e->addGroupingPolicy('bob', 'data1_admin');
        $e->addGroupingPolicy('eve', 'data3_admin');

        $namedGroupingPolicy = ['alice', 'data2_admin'];

        TestUtil::testGetRoles($e, 'alice', []);
        $e->addNamedGroupingPolicy('g', $namedGroupingPolicy);
        TestUtil::testGetRoles($e, 'alice', ['data2_admin']);
        $e->removeNamedGroupingPolicy('g', $namedGroupingPolicy);

        TestUtil::testGetRoles($e, 'alice', []);
        TestUtil::testGetRoles($e, 'bob', ['data1_admin']);
        TestUtil::testGetRoles($e, 'eve',[ 'data3_admin']);
        TestUtil::testGetRoles($e, 'non_exist', []);

        TestUtil::testGetUsers($e, 'data1_admin', ['bob']);
        TestUtil::testGetUsers($e, 'data2_admin', []);
        TestUtil::testGetUsers($e, 'data3_admin', ['eve']);

        $e->removeFilteredGroupingPolicy(0, 'bob');

        TestUtil::testGetRoles($e, 'alice', []);
        TestUtil::testGetRoles($e, 'bob', []);
        TestUtil::testGetRoles($e, 'eve', ['data3_admin']);
        TestUtil::testGetRoles($e, 'non_exist', []);

        TestUtil::testGetUsers($e, 'data1_admin', []);
        TestUtil::testGetUsers($e, 'data2_admin', []);
        TestUtil::testGetUsers($e, 'data3_admin', ['eve']);
    }

    // ------------------------------------------------------------------------------

}
