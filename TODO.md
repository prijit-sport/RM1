# TODO

## Task: Fix Intelephense error in ManagerOrAdmin middleware

- [ ] Gather context by inspecting `app/Http/Middleware/ManagerOrAdmin.php` and related User/Role relationship.
- [ ] Create an edit plan to remove invalid `$user->load('role')` call or adjust model/relationship usage.
- [ ] Apply code fix in `ManagerOrAdmin.php`.
- [ ] Verify no other callsites rely on the removed method.
- [ ] Run quick syntax/static checks (optional) and ensure middleware works.

