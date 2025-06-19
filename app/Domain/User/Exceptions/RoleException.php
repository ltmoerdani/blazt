<?php

namespace App\Domain\User\Exceptions;

use Exception;

/**
 * Role Exception
 *
 * Custom exception for role-related operations
 */
class RoleException extends Exception
{
    /**
     * Exception when trying to delete role with assigned users
     */
    public static function roleHasAssignedUsers(): self
    {
        return new self('Cannot delete role that has users assigned');
    }

    /**
     * Exception when role not found
     */
    public static function roleNotFound(string $identifier): self
    {
        return new self("Role not found: {$identifier}");
    }

    /**
     * Exception when trying to create role with duplicate slug
     */
    public static function duplicateSlug(string $slug): self
    {
        return new self("Role with slug '{$slug}' already exists");
    }
}
