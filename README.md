
### God Permission System

God is written after referring to the Casbin project, thanks for their hard work.

For more detail, please refer to the documentation of [Casbin](https://github.com/casbin/casbin).

"God said, Let there be light, and there was light."

"God will decide if you have permission."

"God bless you!"


### Internal Identifier List

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


### Demo

```php
$God = new God();

$God->allows('you', 'evil book', 'read');  // Does God allows you to do this?

new God();

new God(string $modelPath);

new God(string $modelPath, string $policyFile);

new God(string $modelPath, Adapter $adapter);

new God(Model $model);

new God(Model $model, Adapter $adapter);
```

### License

This project is licensed under the MIT license.
