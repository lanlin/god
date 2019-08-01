<?php namespace GodTests;

use God\God;
use God\Persist\Adapter\MySQL\AdapterFiltered as Adapter;
use PHPUnit\Framework\TestCase;

/**
 * ------------------------------------------------------------------------------------
 * God MySQL Adapter Test
 * ------------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2019/07/31
 */
class MySQLTest extends TestCase
{

    // ------------------------------------------------------------------------------

    /**
     * get pdo
     *
     * @return \PDO
     */
    public function getDB()
    {
        $dbHost   = "192.168.0.133";
        $dbPort   = 3306;
        $dbName   = "rap_db";
        $username = "root";
        $password = "I6NM*2Jft1DVex5WRn!nU!@Vwg*s2evJ";

        try
        {
            return new \PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName}", $username, $password);
        }
        catch (\Exception $e)
        {
            echo $e->getMessage() . "\n\n";

            self::fail('Unable to connect to Database');
        }
    }

    // ------------------------------------------------------------------------------

    public function initPolicy()
    {
        // Because the DB is empty at first,
        // so we need to load the policy from the file adapter (.CSV) first.
        $e = new God(TestUtil::$path.'rbac_model.conf', TestUtil::$path.'rbac_policy.csv');
        $a = new Adapter($this->getDB());

        // This is a trick to save the current policy to the DB.
        // We can't call e.savePolicy() because the adapter in the enforcer is still the file adapter.
        // The current policy means the policy in the God enforcer (aka in memory).
        $a->savePolicy($e->getModel());

        // Clear the current policy.
        $e->clearPolicy();

        TestUtil::testGetPolicy($e, []);

        // Load the policy from DB.
        $a->loadPolicy($e->getModel());

        TestUtil::testGetPolicy($e, [["alice", "data1", "read"], ["bob", "data2", "write"], ["data2_admin", "data2", "read"], ["data2_admin", "data2", "write"]]);
    }

    // ------------------------------------------------------------------------------

    public function testAdapter()
    {
         $this->initPolicy();

        // Note: you don't need to look at the above code
        // if you already have a working DB with policy inside.

        // Now the DB has policy, so we can provide a normal use case.
        // Create an adapter and an enforcer.
        // NewEnforcer() will load the policy automatically.
        $a = new Adapter($this->getDB());
        $e = new God(TestUtil::$path.'rbac_model.conf', $a);

        TestUtil::testGetPolicy($e, [["alice", "data1", "read"], ["bob", "data2", "write"], ["data2_admin", "data2", "read"], ["data2_admin", "data2", "write"]]);

        // AutoSave is enabled by default.
        // Now we disable it.
        $e->enableAutoSave(false);

        // Because AutoSave is disabled, the policy change only affects the policy in God enforcer,
        // it doesn't affect the policy in the storage.
        $e->addPolicy("alice", "data1", "write");

        // Reload the policy from the storage to see the effect.
        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        // This is still the original policy.
        TestUtil::testGetPolicy($e, [["alice", "data1", "read"], ["bob", "data2", "write"], ["data2_admin", "data2", "read"], ["data2_admin", "data2", "write"]]);

        // Now we enable the AutoSave.
        $e->enableAutoSave(true);

        // Because AutoSave is enabled, the policy change not only affects the policy in God enforcer,
        // but also affects the policy in the storage.
        $e->addPolicy("alice", "data1", "write");

        // Reload the policy from the storage to see the effect.
        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        // The policy has a new rule: {"alice", "data1", "write"}.
        TestUtil::testGetPolicy($e, [["alice", "data1", "read"], ["bob", "data2", "write"], ["data2_admin", "data2", "read"], ["data2_admin", "data2", "write"], ["alice", "data1", "write"]]);

        // Remove the added rule.
        $e->removePolicy("alice", "data1", "write");

        try
        {
            $a->removePolicy("p", "p", ["alice", "data1", "write"]);
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected RemovePolicy() to be successful; got {$msg}");
        }

        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        TestUtil::testGetPolicy($e, [["alice", "data1", "read"], ["bob", "data2", "write"], ["data2_admin", "data2", "read"], ["data2_admin", "data2", "write"]]);

        // Remove "data2_admin" related policy rules via a filter.
        // Two rules: {"data2_admin", "data2", "read"}, {"data2_admin", "data2", "write"} are deleted.
        $e->removeFilteredPolicy(0, "data2_admin");

        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        TestUtil::testGetPolicy($e, [["alice", "data1", "read"], ["bob", "data2", "write"]]);

        $e->removeFilteredPolicy(1, "data1");

        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        TestUtil::testGetPolicy($e, [["bob", "data2", "write"]]);

        $e->removeFilteredPolicy(2, "write");

        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        TestUtil::testGetPolicy( $e, []);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteFilteredAdapter()
    {
        $a = new Adapter($this->getDB());
        $e = new God(TestUtil::$path."rbac_tenant_service.conf", $a);

        $e->addPolicy("domain1", "alice", "data3", "read", "accept", "service1");
        $e->addPolicy("domain1", "alice", "data3", "write", "accept", "service2");

        // Reload the policy from the storage to see the effect.
        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        // The policy has a new rule: {"alice", "data1", "write"}.
        TestUtil::testGetPolicy($e, [["domain1", "alice", "data3", "read", "accept", "service1"], ["domain1", "alice", "data3", "write", "accept", "service2"]]);

        // test RemoveFiltered Policy with "" fileds
        $e->removeFilteredPolicy(0, "domain1", "", "", "read");

        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        TestUtil::testGetPolicy($e, [["domain1", "alice", "data3", "write", "accept", "service2"]]);

        $e->removeFilteredPolicy(0, "domain1", "", "", "", "", "service2");

        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        TestUtil::testGetPolicy($e, []);
    }

    // ------------------------------------------------------------------------------

    public function TestFilteredAdapter()
    {
        // Now the DB has policy, so we can provide a normal use case.
        // Create an adapter and an enforcer.
        // NewEnforcer() will load the policy automatically.
        $a = new Adapter($this->getDB());
        $e = new God(TestUtil::$path."rbac_model.conf", $a);

        // Load filtered policies from the database.
        $e->addPolicy("alice", "data1", "write");
        $e->addPolicy("bob", "data2", "write");

        // Reload the filtered policy from the storage.
        $filter = ["v0" => "bob"];

        try
        {
            $e->loadFilteredPolicy($filter);
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadFilteredPolicy() to be successful; got {$msg}");
        }

        // Only bob's policy should have been loaded
        TestUtil::testGetPolicy($e, [["bob", "data2", "write"]]);

        // Verify that alice's policy remains intact in the database.
        $filter = ["v0" => "alice"];

        try
        {
            $e->loadFilteredPolicy($filter);
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadFilteredPolicy() to be successful; got {$msg}");
        }

        // Only alice's policy should have been loaded,
        TestUtil::testGetPolicy($e, [["alice", "data1", "write"]]);

        // Test safe handling of savePolicy when using filtered policies.
        try
        {
            $e->savePolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected savePolicy() to be successful; got {$msg}");
        }

        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        try
        {
            $e->savePolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected savePolicy() to be successful; got {$msg}");
        }

        $e->removeFilteredPolicy(2, "write");

        try
        {
            $e->loadPolicy();
        }
        catch (\Exception $e)
        {
            $msg = $e->getMessage();
            self::fail("Expected loadPolicy() to be successful; got {$msg}");
        }

        TestUtil::testGetPolicy($e, []);
    }

    // ------------------------------------------------------------------------------

}
