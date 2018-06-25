
## God Permission System

"God said, Let there be light, and there was light."

"God will decide if you have permission."

"God bless you!"


## Install

```shell
composer require lanlin/god
```


## The Origin

God is written after referring to the Casbin(golang) project, thanks for their hard work.

For more detail, please refer to the documentation of [Casbin](https://github.com/casbin/casbin).


## Supported models

1. [**ACL (Access Control List)**](https://en.wikipedia.org/wiki/Access_control_list)
2. **ACL with [superuser](https://en.wikipedia.org/wiki/Superuser)**
3. **ACL without users**: especially useful for systems that don't have authentication or user log-ins.
3. **ACL without resources**: some scenarios may target for a type of resources instead of an individual resource by using permissions like ``write-article``, ``read-log``. It doesn't control the access to a specific article or log.
4. **[RBAC (Role-Based Access Control)](https://en.wikipedia.org/wiki/Role-based_access_control)**
5. **RBAC with resource roles**: both users and resources can have roles (or groups) at the same time.
6. **RBAC with domains/tenants**: users can have different role sets for different domains/tenants.
7. **[ABAC (Attribute-Based Access Control)](https://en.wikipedia.org/wiki/Attribute-Based_Access_Control)**: syntax sugar like ``resource.Owner`` can be used to get the attribute for a resource.
8. **[RESTful](https://en.wikipedia.org/wiki/Representational_state_transfer)**: supports paths like ``/res/*``, ``/res/:id`` and HTTP methods like ``GET``, ``POST``, ``PUT``, ``DELETE``.
9. **Deny-override**: both allow and deny authorizations are supported, deny overrides the allow.
10. **Priority**: the policy rules can be prioritized like firewall rules.


## How it works?

does "Who" as "Role" or "Group" from "Where" do "Operator" to "What" will got "How"?

| Identifier | Description |
|------------|-------------|
| r          | Request (r = sub, obj, act)                       |
| p          | Policy (p = sub, obj, act, eft)                   |
| g          | Group or Role (g = _, _)                          |
| e          | Policy Efftect (e = some(where (p.eft == allow))) |
| m          | Matchers (m = r.obj == p.obj)                     |
| sub        | Subject (Who)                                     |
| dom        | Domain  (Where)                                   |
| obj        | Object  (What)                                    |
| act        | Action  (Operator)                                |
| eft        | Efftect (How) (allow, deny, indeterminate)        |

An access control model is abstracted into a CONF file based on the **PERM metamodel (Policy, Effect, Request, Matchers)**.

So switching or upgrading the authorization mechanism for a project is just as simple as modifying a configuration.

You can customize your own access control model by combining the available models.

For example, you can get RBAC roles and ABAC attributes together inside one model and share one set of policy rules.

The most basic and simplest model in Casbin is ACL. ACL's model CONF is:

```ini
# Request definition
[request_definition]
r = sub, obj, act

# Policy definition
[policy_definition]
p = sub, obj, act

# Policy effect
[policy_effect]
e = some(where (p.eft == allow))

# Matchers
[matchers]
m = r.sub == p.sub && r.obj == p.obj && r.act == p.act
```

An example policy for ACL model is like:

```
p, alice, data1, read
p, bob, data2, write
```

It means:

- alice can read data1
- bob can write data2


## Demo

```php
use God\God;

$God = new God('path/to/model.conf', 'path/to/policy.csv');

$God->allows('you', 'evil book', 'read');  // Does God allows you to do this?

new God();

new God(string $modelPath);

new God(string $modelPath, string $policyFile);

new God(string $modelPath, Adapter $adapter);

new God(Model $model);

new God(Model $model, Adapter $adapter);
```

For more usage demos, please view the tests.


## License

This project is licensed under the MIT license.
