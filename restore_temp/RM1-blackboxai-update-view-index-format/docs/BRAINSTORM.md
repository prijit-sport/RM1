# Brainstorm Plan (Middleware load() fix)

## Goal

Fix intelephense warning: **Undefined method `load`** in `app/Http/Middleware/ManagerOrAdmin.php`.

## Information gathered

- `ManagerOrAdmin` middleware directly calls: `$user->load('role');`
- In Laravel Eloquent, `load()` exists on `Model` instances.
- The project’s `User` model extends `Illuminate\Foundation\Auth\User` (Eloquent model) and should support `load()`.
- Intelephense reports undefined `load`, which typically happens when the static type of `$user` is unknown or not inferred as an Eloquent model.
- The project already uses `$this->load('role')` inside `User::hasRole()`, so runtime support is likely correct.

## Plan

1. Remove explicit `$user->load('role')` from middleware, and rely on the relationship via `$user->role` (and/or `relationLoaded` check).

