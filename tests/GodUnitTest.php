<?php namespace GodTests;

use God\God;
use God\Enforcer\Core;
use God\Persist\Adapter\File\Adapter as FileAdapter;
use PHPUnit\Framework\TestCase;

/**
 * ----------------------------------------------------------------------------------
 * God Tests
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/06/19
 */
class GodUnitTest extends TestCase
{

    // ------------------------------------------------------------------------------

    public function testKeyMatchModelInMemory1()
    {
        $m = Core::newModel();
        $m->addDef('r', 'r', 'sub, obj, act');
        $m->addDef('p', 'p', 'sub, obj, act');
        $m->addDef('e', 'e', 'some(where (p.eft == allow))');
        $m->addDef('m', 'm', 'r.sub == p.sub && keyMatch(r.obj, p.obj) && regexMatch(r.act, p.act)');

        $a = new FileAdapter(TestUtil::$path.'keymatch_policy.csv');
        $e = new God($m, $a);

        TestUtil::testEnforce($e, 'alice', '/alice_data/resource1', 'GET', true);
        TestUtil::testEnforce($e, 'alice', '/alice_data/resource1', 'POST', true);
        TestUtil::testEnforce($e, 'alice', '/alice_data/resource2', 'GET', true);
        TestUtil::testEnforce($e, 'alice', '/alice_data/resource2', 'POST', false);
        TestUtil::testEnforce($e, 'alice', '/bob_data/resource1', 'GET', false);
        TestUtil::testEnforce($e, 'alice', '/bob_data/resource1', 'POST', false);
        TestUtil::testEnforce($e, 'alice', '/bob_data/resource2', 'GET', false);
        TestUtil::testEnforce($e, 'alice', '/bob_data/resource2', 'POST', false);

        TestUtil::testEnforce($e, 'bob', '/alice_data/resource1', 'GET', false);
        TestUtil::testEnforce($e, 'bob', '/alice_data/resource1', 'POST', false);
        TestUtil::testEnforce($e, 'bob', '/alice_data/resource2', 'GET', true);
        TestUtil::testEnforce($e, 'bob', '/alice_data/resource2', 'POST', false);
        TestUtil::testEnforce($e, 'bob', '/bob_data/resource1', 'GET', false);
        TestUtil::testEnforce($e, 'bob', '/bob_data/resource1', 'POST', true);
        TestUtil::testEnforce($e, 'bob', '/bob_data/resource2', 'GET', false);
        TestUtil::testEnforce($e, 'bob', '/bob_data/resource2', 'POST', true);

        TestUtil::testEnforce($e, 'cathy', '/cathy_data', 'GET', true);
        TestUtil::testEnforce($e, 'cathy', '/cathy_data', 'POST', true);
        TestUtil::testEnforce($e, 'cathy', '/cathy_data', 'DELETE', false);

        $e = new God($m);
        $a->loadPolicy($e->getModel());

        TestUtil::testEnforce($e, 'alice', '/alice_data/resource1', 'GET', true);
        TestUtil::testEnforce($e, 'alice', '/alice_data/resource1', 'POST', true);
        TestUtil::testEnforce($e, 'alice', '/alice_data/resource2', 'GET', true);
        TestUtil::testEnforce($e, 'alice', '/alice_data/resource2', 'POST', false);
        TestUtil::testEnforce($e, 'alice', '/bob_data/resource1', 'GET', false);
        TestUtil::testEnforce($e, 'alice', '/bob_data/resource1', 'POST', false);
        TestUtil::testEnforce($e, 'alice', '/bob_data/resource2', 'GET', false);
        TestUtil::testEnforce($e, 'alice', '/bob_data/resource2', 'POST', false);

        TestUtil::testEnforce($e, 'bob', '/alice_data/resource1', 'GET', false);
        TestUtil::testEnforce($e, 'bob', '/alice_data/resource1', 'POST', false);
        TestUtil::testEnforce($e, 'bob', '/alice_data/resource2', 'GET', true);
        TestUtil::testEnforce($e, 'bob', '/alice_data/resource2', 'POST', false);
        TestUtil::testEnforce($e, 'bob', '/bob_data/resource1', 'GET', false);
        TestUtil::testEnforce($e, 'bob', '/bob_data/resource1', 'POST', true);
        TestUtil::testEnforce($e, 'bob', '/bob_data/resource2', 'GET', false);
        TestUtil::testEnforce($e, 'bob', '/bob_data/resource2', 'POST', true);

        TestUtil::testEnforce($e, 'cathy', '/cathy_data', 'GET', true);
        TestUtil::testEnforce($e, 'cathy', '/cathy_data', 'POST', true);
        TestUtil::testEnforce($e, 'cathy', '/cathy_data', 'DELETE', false);
    }

    // ------------------------------------------------------------------------------

    public function testKeyMatchModelInMemoryDeny()
    {
        $m = Core::newModel();
        $m->addDef('r', 'r', 'sub, obj, act');
        $m->addDef('p', 'p', 'sub, obj, act');
        $m->addDef('e', 'e', '!some(where (p.eft == deny))');
        $m->addDef('m', 'm', 'r.sub == p.sub && keyMatch(r.obj, p.obj) && regexMatch(r.act, p.act)');

        $a = new FileAdapter(TestUtil::$path.'keymatch_policy.csv');
        $e = new God($m, $a);

        TestUtil::testEnforce($e, 'alice', '/alice_data/resource2', 'POST', true);
    }

    // ------------------------------------------------------------------------------

    public function testRBACModelInMemoryIndeterminate()
    {
        $m = Core::newModel();
        $m->addDef('r', 'r', 'sub, obj, act');
        $m->addDef('p', 'p', 'sub, obj, act');
        $m->addDef('g', 'g', '_, _');
        $m->addDef('e', 'e', 'some(where (p.eft == allow))');
        $m->addDef('m', 'm', 'g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act');

        $e = new God($m);

        $e->addPermissionForUser('alice', 'data1', 'invalid');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', false);
    }

    // ------------------------------------------------------------------------------

    public function testRBACModelInMemory1()
    {
        $m = Core::newModel();
        $m->addDef('r', 'r', 'sub, obj, act');
        $m->addDef('p', 'p', 'sub, obj, act');
        $m->addDef('g', 'g', '_, _');
        $m->addDef('e', 'e', 'some(where (p.eft == allow))');
        $m->addDef('m', 'm', 'g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act');

        $e = new God($m);

        $e->addPermissionForUser('alice', 'data1', 'read');
        $e->addPermissionForUser('bob', 'data2', 'write');
        $e->addPermissionForUser('data2_admin', 'data2', 'read');
        $e->addPermissionForUser('data2_admin', 'data2', 'write');
        $e->addRoleForUser('alice', 'data2_admin');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', true);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testRBACModelInMemory2()
    {
        $text =
		    "[request_definition]\n"
            . "r = sub, obj, act\n"
            . "\n"
            . "[policy_definition]\n"
            . "p = sub, obj, act\n"
            . "\n"
            . "[role_definition]\n"
            . "g = _, _\n"
            . "\n"
            . "[policy_effect]\n"
            . "e = some(where (p.eft == allow))\n"
            . "\n"
            . "[matchers]\n"
            . "m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act\n";

        $m = Core::newModel($text);
        // The above is the same as:
        // $m = Core::newModel();
        // $m->loadModelFromText($text);

        $e = new God($m);

        $e->addPermissionForUser('alice', 'data1', 'read');
        $e->addPermissionForUser('bob', 'data2', 'write');
        $e->addPermissionForUser('data2_admin', 'data2', 'read');
        $e->addPermissionForUser('data2_admin', 'data2', 'write');
        $e->addRoleForUser('alice', 'data2_admin');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', true);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testNotUsedRBACModelInMemory()
    {
        $m = Core::newModel();
        $m->addDef('r', 'r', 'sub, obj, act');
        $m->addDef('p', 'p', 'sub, obj, act');
        $m->addDef('g', 'g', '_, _');
        $m->addDef('e', 'e', 'some(where (p.eft == allow))');
        $m->addDef('m', 'm', 'g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act');

        $e = new God($m);

        $e->addPermissionForUser('alice', 'data1', 'read');
        $e->addPermissionForUser('bob', 'data2', 'write');

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

    public function testReloadPolicy()
    {
        $e = new God(TestUtil::$path.'rbac_model.conf', TestUtil::$path.'rbac_policy.csv');

        $e->loadPolicy();

        TestUtil::testGetPolicy($e, [['alice', 'data1', 'read'], ['bob', 'data2', 'write'], ['data2_admin', 'data2', 'read'], ['data2_admin', 'data2', 'write']]);
    }

    // ------------------------------------------------------------------------------

    public function testSavePolicy()
    {
        $e = new God(TestUtil::$path.'rbac_model.conf', TestUtil::$path.'rbac_policy.csv');

        $e->savePolicy();
    }

    // ------------------------------------------------------------------------------

    public function testClearPolicy()
    {
        $e = new God(TestUtil::$path.'rbac_model.conf', TestUtil::$path.'rbac_policy.csv');

        $e->clearPolicy();
    }

    // ------------------------------------------------------------------------------

    public function testEnableEnforce()
    {
        $e = new God(TestUtil::$path.'basic_model.conf', TestUtil::$path.'basic_policy.csv');

        $e->enableEnforce(false);

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', true);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', true);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', true);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);

        $e->enableEnforce(true);

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

    public function testEnableLog()
    {
        $e = new God(TestUtil::$path.'basic_model.conf', TestUtil::$path.'basic_policy.csv', true);
        // The log is enabled by default, so the above is the same with:
        // $e = new God(TestUtil::$path.'basic_model.conf', TestUtil::$path.'basic_policy.csv');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);

        // The log can also be enabled or disabled at run-time.
        $e->enableLog(false);

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

    public function testEnableAutoSave()
    {
        $e = new God(TestUtil::$path.'basic_model.conf', TestUtil::$path.'basic_policy.csv');

        $e->enableAutoSave(false);

        // Because AutoSave is disabled, the policy change only affects the policy in God enforcer,
        // it doesn't affect the policy in the storage.
        $e->removePolicy('alice', 'data1', 'read');

        // Reload the policy from the storage to see the effect.
        $e->loadPolicy();

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);

        $e->enableAutoSave(true);

        // Because AutoSave is enabled, the policy change not only affects the policy in God enforcer,
        // but also affects the policy in the storage.
        $e->removePolicy('alice', 'data1', 'read');

        // However, the file adapter doesn't implement the AutoSave feature, so enabling it has no effect at all here.

        // Reload the policy from the storage to see the effect.
        $e->loadPolicy();

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true); // Will not be false here.
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testInitWithAdapter()
    {
        $adapter = new FileAdapter(TestUtil::$path.'basic_policy.csv');

        $e = new God(TestUtil::$path.'basic_model.conf', $adapter);

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

    public function testRoleLinks()
    {
        $e = new God(TestUtil::$path.'rbac_model.conf');
        $e->enableAutoBuildRoleLinks(false);
        $e->buildRoleLinks();
        $e->allows('user501', 'data9', 'read');
    }

    // ------------------------------------------------------------------------------

    public function testGetAndSetModel()
    {
        $e = new God(TestUtil::$path.'basic_model.conf', TestUtil::$path.'basic_policy.csv');
        $e2 = new God(TestUtil::$path.'basic_with_root_model.conf', TestUtil::$path.'basic_policy.csv');

        TestUtil::testEnforce($e, 'root', 'data1', 'read', false);

        $e->setModel($e2->getModel());

        TestUtil::testEnforce($e, 'root', 'data1', 'read', true);
    }

    // ------------------------------------------------------------------------------

    public function testGetAndSetAdapterInMem()
    {
        $e = new God(TestUtil::$path.'basic_model.conf', TestUtil::$path.'basic_policy.csv');
        $e2 = new God(TestUtil::$path.'basic_model.conf', TestUtil::$path.'basic_inverse_policy.csv');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);

        $a2 = $e2->getAdapter();
        $e->setAdapter($a2);
        $e->loadPolicy();

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testSetAdapterFromFile()
    {
        $e = new God(TestUtil::$path.'basic_model.conf');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', false);

        $a = new FileAdapter(TestUtil::$path.'basic_policy.csv');
        $e->setAdapter($a);
        $e->loadPolicy();

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
    }

    // ------------------------------------------------------------------------------

    public function testInitEmpty()
    {
        $e = new God();

        $m = Core::newModel();
        $m->addDef('r', 'r', 'sub, obj, act');
        $m->addDef('p', 'p', 'sub, obj, act');
        $m->addDef('e', 'e', 'some(where (p.eft == allow))');
        $m->addDef('m', 'm', 'r.sub == p.sub && keyMatch(r.obj, p.obj) && regexMatch(r.act, p.act)');

        $a = new FileAdapter(TestUtil::$path.'keymatch_policy.csv');

        $e->setModel($m);
        $e->setAdapter($a);
        $e->loadPolicy();

        TestUtil::testEnforce($e, 'alice', '/alice_data/resource1', 'GET', true);
    }

    // ------------------------------------------------------------------------------

}
