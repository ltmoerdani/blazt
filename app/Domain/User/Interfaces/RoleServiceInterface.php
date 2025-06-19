<?php

namespace App\Domain\User\Interfaces;

use App\Domain\User\Models\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Role Service Interface
 *
 * Following SOLID principles - Dependency Inversion
 * Interface for role management operations
 */
interface RoleServiceInterface
{
    /**
     * Get all roles with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllRoles(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get active roles only
     *
     * @return Collection
     */
    public function getActiveRoles(): Collection;

    /**
     * Create new role
     *
     * @param array $data
     * @return Role
     */
    public function createRole(array $data): Role;

    /**
     * Update existing role
     *
     * @param Role $role
     * @param array $data
     * @return Role
     */
    public function updateRole(Role $role, array $data): Role;

    /**
     * Delete role
     *
     * @param Role $role
     * @return bool
     */
    public function deleteRole(Role $role): bool;

    /**
     * Assign role to user
     *
     * @param User $user
     * @param string|Role $role
     * @return void
     */
    public function assignRoleToUser(User $user, $role): void;

    /**
     * Remove role from user
     *
     * @param User $user
     * @param string|Role $role
     * @return void
     */
    public function removeRoleFromUser(User $user, $role): void;

    /**
     * Get users by role
     *
     * @param string $roleSlug
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUsersByRole(string $roleSlug, int $perPage = 15): LengthAwarePaginator;

    /**
     * Sync permissions to role
     *
     * @param Role $role
     * @param array $permissionIds
     * @return void
     */
    public function syncRolePermissions(Role $role, array $permissionIds): void;
}
