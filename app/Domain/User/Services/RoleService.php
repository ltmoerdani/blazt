<?php

namespace App\Domain\User\Services;

use App\Domain\User\Interfaces\RoleServiceInterface;
use App\Domain\User\Models\Role;
use App\Domain\User\Exceptions\RoleException;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Role Service Implementation
 *
 * Following DDD principles and SOLID principles
 * Handles all role-related business logic
 */
class RoleService implements RoleServiceInterface
{
    /**
     * Get all roles with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllRoles(int $perPage = 15): LengthAwarePaginator
    {
        return Role::with(['permissions', 'users'])
                   ->orderBy('name')
                   ->paginate($perPage);
    }

    /**
     * Get active roles only
     *
     * @return Collection
     */
    public function getActiveRoles(): Collection
    {
        return Role::active()
                   ->orderBy('name')
                   ->get();
    }

    /**
     * Create new role
     *
     * @param array $data
     * @return Role
     * @throws Exception
     */
    public function createRole(array $data): Role
    {
        try {
            DB::beginTransaction();

            // Generate slug if not provided
            if (!isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Ensure slug is unique
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Role::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            $role = Role::create($data);

            DB::commit();

            Log::info('Role created successfully', ['role_id' => $role->id, 'role_name' => $role->name]);

            return $role;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create role', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Update existing role
     *
     * @param Role $role
     * @param array $data
     * @return Role
     * @throws Exception
     */
    public function updateRole(Role $role, array $data): Role
    {
        try {
            DB::beginTransaction();

            // Update slug if name changed
            if (isset($data['name']) && $data['name'] !== $role->name) {
                if (!isset($data['slug'])) {
                    $data['slug'] = Str::slug($data['name']);
                }

                // Ensure slug is unique (excluding current role)
                $originalSlug = $data['slug'];
                $counter = 1;
                while (Role::where('slug', $data['slug'])->where('id', '!=', $role->id)->exists()) {
                    $data['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $role->update($data);

            DB::commit();

            Log::info('Role updated successfully', ['role_id' => $role->id, 'role_name' => $role->name]);

            return $role->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update role', ['error' => $e->getMessage(), 'role_id' => $role->id]);
            throw $e;
        }
    }

    /**
     * Delete role
     *
     * @param Role $role
     * @return bool
     * @throws Exception
     */
    public function deleteRole(Role $role): bool
    {
        try {
            DB::beginTransaction();

            // Check if role has users assigned
            if ($role->users()->count() > 0) {
                throw RoleException::roleHasAssignedUsers();
            }

            $roleId = $role->id;
            $roleName = $role->name;

            $role->delete();

            DB::commit();

            Log::info('Role deleted successfully', ['role_id' => $roleId, 'role_name' => $roleName]);

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete role', ['error' => $e->getMessage(), 'role_id' => $role->id]);
            throw $e;
        }
    }

    /**
     * Assign role to user
     *
     * @param User $user
     * @param string|Role $role
     * @return void
     * @throws Exception
     */
    public function assignRoleToUser(User $user, $role): void
    {
        try {
            DB::beginTransaction();

            if (is_string($role)) {
                $role = Role::where('slug', $role)->firstOrFail();
            }

            if (!$user->hasRole($role->slug)) {
                $user->roles()->attach($role);
                Log::info('Role assigned to user', ['user_id' => $user->id, 'role_id' => $role->id]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign role to user', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            throw $e;
        }
    }

    /**
     * Remove role from user
     *
     * @param User $user
     * @param string|Role $role
     * @return void
     * @throws Exception
     */
    public function removeRoleFromUser(User $user, $role): void
    {
        try {
            DB::beginTransaction();

            if (is_string($role)) {
                $role = Role::where('slug', $role)->firstOrFail();
            }

            $user->roles()->detach($role);

            DB::commit();

            Log::info('Role removed from user', ['user_id' => $user->id, 'role_id' => $role->id]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove role from user', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            throw $e;
        }
    }

    /**
     * Get users by role
     *
     * @param string $roleSlug
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUsersByRole(string $roleSlug, int $perPage = 15): LengthAwarePaginator
    {
        return User::whereHas('roles', function ($query) use ($roleSlug) {
            $query->where('slug', $roleSlug)->where('is_active', true);
        })->paginate($perPage);
    }

    /**
     * Sync permissions to role
     *
     * @param Role $role
     * @param array $permissionIds
     * @return void
     * @throws Exception
     */
    public function syncRolePermissions(Role $role, array $permissionIds): void
    {
        try {
            DB::beginTransaction();

            $role->permissions()->sync($permissionIds);

            DB::commit();

            Log::info('Role permissions synced', ['role_id' => $role->id, 'permission_count' => count($permissionIds)]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to sync role permissions', ['error' => $e->getMessage(), 'role_id' => $role->id]);
            throw $e;
        }
    }
}
