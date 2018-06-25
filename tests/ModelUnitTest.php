<?php namespace GodTests;

use God\God;
use PHPUnit\Framework\TestCase;

/**
 * ------------------------------------------------------------------------------------
 * God Test Model
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/06/19
 */
class ModelUnitTest extends TestCase
{

    // ------------------------------------------------------------------------------

    public function testBasicModel()
    {
        $e = new God(TestUtil::$path.'basic_model.conf', TestUtil::$path.'basic_policy.csv');

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

    public function testBasicModelNoPolicy()
    {
        $e = new God(TestUtil::$path.'basic_model.conf');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', false);
    }

    // ------------------------------------------------------------------------------

    public function testBasicModelWithRoot()
    {
        $e = new God(TestUtil::$path.'basic_with_root_model.conf', TestUtil::$path.'basic_policy.csv');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);
        TestUtil::testEnforce($e, 'root', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'root', 'data1', 'write', true);
        TestUtil::testEnforce($e, 'root', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'root', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testBasicModelWithRootNoPolicy()
    {
        $e = new God(TestUtil::$path.'basic_with_root_model.conf');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', false);
        TestUtil::testEnforce($e, 'root', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'root', 'data1', 'write', true);
        TestUtil::testEnforce($e, 'root', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'root', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testBasicModelWithoutUsers()
    {
        $e = new God(TestUtil::$path.'basic_without_users_model.conf', TestUtil::$path.'basic_without_users_policy.csv');

        TestUtil::testEnforceWithoutUsers($e, 'data1', 'read', true);
        TestUtil::testEnforceWithoutUsers($e, 'data1', 'write', false);
        TestUtil::testEnforceWithoutUsers($e, 'data2', 'read', false);
        TestUtil::testEnforceWithoutUsers($e, 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testBasicModelWithoutResources()
    {
        $e = new God(TestUtil::$path.'basic_without_resources_model.conf', TestUtil::$path.'basic_without_resources_policy.csv');

        TestUtil::testEnforceWithoutUsers($e, 'alice', 'read', true);
        TestUtil::testEnforceWithoutUsers($e, 'alice', 'write', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'read', false);
        TestUtil::testEnforceWithoutUsers($e, 'bob', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testRBACModel()
    {
        $e = new God(TestUtil::$path.'rbac_model.conf', TestUtil::$path.'rbac_policy.csv');

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

    public function testRBACModelWithResourceRoles()
    {
        $e = new God(TestUtil::$path.'rbac_with_resource_roles_model.conf', TestUtil::$path.'rbac_with_resource_roles_policy.csv');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', true);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', true);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testRBACModelWithDomains()
    {
        $e = new God(TestUtil::$path.'rbac_with_domains_model.conf', TestUtil::$path.'rbac_with_domains_policy.csv');

        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data1', 'read', true);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data1', 'write', true);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data2', 'read', false);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data2', 'write', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data1', 'read', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data1', 'write', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data2', 'read', true);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testRBACModelWithDomainsAtRuntime()
    {
        $e = new God(TestUtil::$path.'rbac_with_domains_model.conf');

        $e->addPolicy('admin', 'domain1', 'data1', 'read');
        $e->addPolicy('admin', 'domain1', 'data1', 'write');
        $e->addPolicy('admin', 'domain2', 'data2', 'read');
        $e->addPolicy('admin', 'domain2', 'data2', 'write');

        $e->addGroupingPolicy('alice', 'admin', 'domain1');
        $e->addGroupingPolicy('bob', 'admin', 'domain2');

        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data1', 'read', true);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data1', 'write', true);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data2', 'read', false);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data2', 'write', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data1', 'read', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data1', 'write', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data2', 'read', true);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data2', 'write', true);

        // Remove all policy rules related to domain1 and data1.
        $e->removeFilteredPolicy(1, 'domain1', 'data1');

        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data1', 'read', false);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data1', 'write', false);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data2', 'read', false);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data2', 'write', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data1', 'read', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data1', 'write', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data2', 'read', true);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data2', 'write', true);

        // Remove the specified policy rule.
        $e->removePolicy('admin', 'domain2', 'data2', 'read');

        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data1', 'read', false);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data1', 'write', false);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data2', 'read', false);
        TestUtil::testDomainEnforce($e, 'alice', 'domain1', 'data2', 'write', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data1', 'read', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data1', 'write', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data2', 'read', false);
        TestUtil::testDomainEnforce($e, 'bob', 'domain2', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testRBACModelWithDeny()
    {
        $e = new God(TestUtil::$path.'rbac_with_deny_model.conf', TestUtil::$path.'rbac_with_deny_policy.csv');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);
    }

    // ------------------------------------------------------------------------------

    public function testRBACModelWithOnlyDeny()
    {
        $e = new God(TestUtil::$path.'rbac_with_not_deny_model.conf', TestUtil::$path.'rbac_with_deny_policy.csv');

        TestUtil::testEnforce($e, 'alice', 'data2', 'write', false);
    }

    // ------------------------------------------------------------------------------

    public function testRBACModelWithCustomData()
    {
        $e = new God(TestUtil::$path.'rbac_model.conf', TestUtil::$path.'rbac_policy.csv');

        // You can add custom data to a grouping policy, God will ignore it. It is only meaningful to the caller.
        // This feature can be used to store information like whether 'bob' is an end user (so no subject will inherit 'bob')
        // For God, it is equivalent to: e.addGroupingPolicy('bob', 'data2_admin')
        $e->addGroupingPolicy('bob', 'data2_admin', 'custom_data');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', true);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', true);

        // You should also take the custom data as a parameter when deleting a grouping policy.
        // e.removeGroupingPolicy('bob', 'data2_admin') won't work.
        // Or you can remove it by using removeFilteredGroupingPolicy().
        $e->removeGroupingPolicy('bob', 'data2_admin', 'custom_data');

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

    public function testKeyMatchModel()
    {
        $e = new God(TestUtil::$path.'keymatch_model.conf', TestUtil::$path.'keymatch_policy.csv');

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

    public function testKeyMatch2Model()
    {
         $e = new God(TestUtil::$path.'keymatch2_model.conf', TestUtil::$path.'keymatch2_policy.csv');

        TestUtil::testEnforce($e, 'alice', '/alice_data', 'GET', false);
        TestUtil::testEnforce($e, 'alice', '/alice_data/resource1', 'GET', true);
        TestUtil::testEnforce($e, 'alice', '/alice_data2/myid', 'GET', false);
        TestUtil::testEnforce($e, 'alice', '/alice_data2/myid/using/res_id', 'GET', true);
    }

    // ------------------------------------------------------------------------------

    public function testIPMatchModel()
    {
        $e = new God(TestUtil::$path.'ipmatch_model.conf', TestUtil::$path.'ipmatch_policy.csv');

        TestUtil::testEnforce($e, '192.168.2.123', 'data1', 'read', true);
        TestUtil::testEnforce($e, '192.168.2.123', 'data1', 'write', false);
        TestUtil::testEnforce($e, '192.168.2.123', 'data2', 'read', false);
        TestUtil::testEnforce($e, '192.168.2.123', 'data2', 'write', false);

        TestUtil::testEnforce($e, '192.168.0.123', 'data1', 'read', false);
        TestUtil::testEnforce($e, '192.168.0.123', 'data1', 'write', false);
        TestUtil::testEnforce($e, '192.168.0.123', 'data2', 'read', false);
        TestUtil::testEnforce($e, '192.168.0.123', 'data2', 'write', false);

        TestUtil::testEnforce($e, '10.0.0.5', 'data1', 'read', false);
        TestUtil::testEnforce($e, '10.0.0.5', 'data1', 'write', false);
        TestUtil::testEnforce($e, '10.0.0.5', 'data2', 'read', false);
        TestUtil::testEnforce($e, '10.0.0.5', 'data2', 'write', true);

        TestUtil::testEnforce($e, '192.168.0.1', 'data1', 'read', false);
        TestUtil::testEnforce($e, '192.168.0.1', 'data1', 'write', false);
        TestUtil::testEnforce($e, '192.168.0.1', 'data2', 'read', false);
        TestUtil::testEnforce($e, '192.168.0.1', 'data2', 'write', false);
    }

    // ------------------------------------------------------------------------------

    public function testPriorityModel()
    {
        $e = new God(TestUtil::$path.'priority_model.conf', TestUtil::$path.'priority_policy.csv');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', true);
        TestUtil::testEnforce($e, 'alice', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'read', false);
        TestUtil::testEnforce($e, 'alice', 'data2', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'read', false);
        TestUtil::testEnforce($e, 'bob', 'data1', 'write', false);
        TestUtil::testEnforce($e, 'bob', 'data2', 'read', true);
        TestUtil::testEnforce($e, 'bob', 'data2', 'write', false);
    }

    // ------------------------------------------------------------------------------

    public function testPriorityModelIndeterminate()
    {
        $e = new God(TestUtil::$path.'priority_model.conf', TestUtil::$path.'priority_indeterminate_policy.csv');

        TestUtil::testEnforce($e, 'alice', 'data1', 'read', false);
    }

    // ------------------------------------------------------------------------------

}
