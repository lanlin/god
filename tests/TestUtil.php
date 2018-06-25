<?php namespace GodTests;

use God\God;
use God\Util\Util;
use PHPUnit\Framework\TestCase;

/**
 * ----------------------------------------------------------------------------------
 * God Tests Utils
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/06/19
 */
class TestUtil extends TestCase
{

    // ------------------------------------------------------------------------------

    public static $path =  ROOT . '/tests/Examples/';

    // ------------------------------------------------------------------------------

    public static function toJson($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    // ------------------------------------------------------------------------------

    public static function testEnforce(God $e, string $sub, string $obj, string $act, bool $res)
    {
        self::assertEquals($res, $e->allows($sub, $obj, $act));
    }

    // ------------------------------------------------------------------------------

    public static function testEnforceWithoutUsers(GOd $e, string $obj, string $act, bool $res)
    {
        self::assertEquals($res, $e->allows($obj, $act));
    }

    // ------------------------------------------------------------------------------

    public static function testDomainEnforce(God $e, string $sub, string $dom, string $obj, string $act, bool $res)
    {
        self::assertEquals($res, $e->allows($sub, $dom, $obj, $act));
    }

    // ------------------------------------------------------------------------------

    public static function testGetPolicy(God $e, array $res)
    {
        $myRes1 = $e->getPolicy();
        $myRes = self::toJson($myRes1);

        Util::logPrint('Policy: ' . $myRes);

        if (!Util::array2DEquals($res, $myRes1))
        {
            self::fail('Policy: ' . $myRes . ', supposed to be ' . self::toJson($res));
        }
    }

    // ------------------------------------------------------------------------------

    public static function testGetFilteredPolicy(God $e, int $fieldIndex, array $res, ...$fieldValues)
    {
        $myRes1 = $e->getFilteredPolicy($fieldIndex, $fieldValues);
        $myRes = self::toJson($myRes1);

        Util::logPrint('Policy for ' . Util::paramsToString($fieldValues) . ': ' . $myRes);

        if (!Util::array2DEquals($res, $myRes1))
        {
            self::fail('Policy for ' . Util::paramsToString($fieldValues) . ': ' . $myRes . ', supposed to be ' . self::toJson($res));
        }
    }

    // ------------------------------------------------------------------------------

    public static function testGetGroupingPolicy(God $e, array $res)
    {
        $myRes1 = $e->getGroupingPolicy();
        $myRes = self::toJson($myRes1);

        Util::logPrint('Grouping policy: ' . $myRes);

        if (!Util::array2DEquals($res, $myRes1))
        {
            self::fail('Grouping policy: ' . $myRes . ', supposed to be ' . self::toJson($res));
        }
    }

    // ------------------------------------------------------------------------------

    public static function testGetFilteredGroupingPolicy(God $e, int $fieldIndex, array $res, ...$fieldValues)
    {
        $myRes1 = $e->getFilteredGroupingPolicy($fieldIndex, $fieldValues);
        $myRes = self::toJson($myRes1);

        Util::logPrint('Grouping policy for ' . Util::paramsToString($fieldValues) . ': ' . $myRes);

        if (!Util::array2DEquals($res, $myRes1))
        {
            self::fail('Grouping policy for ' . Util::paramsToString($fieldValues) . ': ' . $myRes . ', supposed to be ' . self::toJson($res));
        }
    }

    // ------------------------------------------------------------------------------

    public static function testHasPolicy(God $e, array $policy, bool $res)
    {
        $myRes = $e->hasPolicy($policy);

        Util::logPrint('Has policy ' . Util::arrayToString($policy) . ': ' . $myRes);

        if ($res != $myRes)
        {
            self::fail('Has policy ' . Util::arrayToString($policy) . ': ' . $myRes . ', supposed to be ' . $res);
        }
    }

    // ------------------------------------------------------------------------------

    public static function testHasGroupingPolicy(God $e, array $policy, bool $res)
    {
        $myRes = $e->hasGroupingPolicy($policy);

        Util::logPrint('Has grouping policy ' . Util::arrayToString($policy) . ': ' . $myRes);

        if ($res != $myRes)
        {
            self::fail('Has grouping policy ' . Util::arrayToString($policy) . ': ' . $myRes . ', supposed to be ' . $res);
        }
    }

    // ------------------------------------------------------------------------------

    public static function testGetRoles(God $e, string $name, array $res)
    {
        $myRes1 = $e->getRolesForUser($name);
        $myRes = self::toJson($myRes1);

        Util::logPrint('Roles for ' . $name . ': ' . $myRes);

        if (!Util::setEquals($res, $myRes1))
        {
            self::fail('Roles for ' . $name . ': ' . $myRes . ', supposed to be ' . self::toJson($res));
        }
    }

    // ------------------------------------------------------------------------------

    public static function testGetUsers(God $e, string $name, array $res)
    {
        $myRes1 = $e->getUsersForRole($name);
        $myRes = self::toJson($myRes1);

        Util::logPrint('Users for ' . $name . ': ' . $myRes);

        if (!Util::setEquals($res, $myRes1))
        {
            self::fail('Users for ' . $name . ': ' . $myRes . ', supposed to be ' . self::toJson($res));
        }
    }

    // ------------------------------------------------------------------------------

    public static function testHasRole(God $e, string $name, string $role, bool $res)
    {
        $myRes = $e->hasRoleForUser($name, $role);

        Util::logPrint($name . ' has role ' . $role . ': ' . $myRes);

        if ($res != $myRes)
        {
            self::fail($name . ' has role ' . $role . ': ' . $myRes . ', supposed to be ' . $res);
        }
    }

    // ------------------------------------------------------------------------------

    public static function testGetPermissions(God $e, string $name, array $res)
    {
        $myRes1 = $e->getPermissionsForUser($name);
        $myRes = self::toJson($myRes1);

        Util::logPrint('Permissions for ' . $name . ': ' . $myRes);

        if (!Util::array2DEquals($res, $myRes1))
        {
            self::fail('Permissions for ' . $name . ': ' . $myRes . ', supposed to be ' . self::toJson($res));
        }
    }

    // ------------------------------------------------------------------------------

    public static function testHasPermission(God $e, string $name, array $permission, bool $res)
    {
        $myRes = $e->hasPermissionForUser($name, $permission);

        Util::logPrint($name . ' has permission ' . Util::arrayToString($permission) . ': ' . $myRes);

        if ($res != $myRes)
        {
            self::fail($name . ' has permission ' . Util::arrayToString($permission) . ': ' . $myRes . ', supposed to be ' . $res);
        }
    }

    // ------------------------------------------------------------------------------

    public static function testGetRolesInDomain(God $e, string $name, string $domain, array $res)
    {
        $myRes1 = $e->getRolesForUserInDomain($name, $domain);
        $myRes = self::toJson($myRes1);

        Util::logPrint('Roles for ' . $name . ' under ' . $domain . ': ' . $myRes);

        if (!Util::setEquals($res, $myRes1))
        {
            self::fail('Roles for ' . $name . ' under ' . $domain . ': ' . $myRes . ', supposed to be ' . self::toJson($res));
        }
    }

    // ------------------------------------------------------------------------------

    public static function testGetPermissionsInDomain(God $e, string $name, string $domain, array $res)
    {
        $myRes1 = $e->getPermissionsForUserInDomain($name, $domain);
        $myRes = self::toJson($myRes1);

        Util::logPrint('Permissions for ' . $name . ' under ' . $domain . ': ' . $myRes);

        if (!Util::array2DEquals($res, $myRes1))
        {
            self::fail('Permissions for ' . $name . ' under ' . $domain . ': ' . $myRes . ', supposed to be ' . self::toJson($res));
        }
    }

    // ------------------------------------------------------------------------------

}
