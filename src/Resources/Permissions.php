<?php

declare(strict_types=1);

namespace Cognee\Resources;

/**
 * Permissions resource for managing permissions, roles, and tenants.
 */
class Permissions extends AbstractResource
{
    /**
     * Grant dataset permissions to a principal (user or role).
     *
     * @param string $principalId Principal ID (user or role)
     * @param array<string, mixed> $permissions Permissions to grant
     * @return bool
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function grantDatasetPermission(string $principalId, array $permissions): bool
    {
        $this->post("api/v1/permissions/datasets/{$principalId}", $permissions);

        return true;
    }

    /**
     * Create a new role.
     *
     * @param array<string, mixed> $data Role data (name, description, permissions)
     * @return array<string, mixed>
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function createRole(array $data): array
    {
        return $this->post('api/v1/permissions/roles', $data);
    }

    /**
     * Assign a user to a role.
     *
     * @param string $userId User ID
     * @param string $roleId Role ID
     * @return bool
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function assignUserToRole(string $userId, string $roleId): bool
    {
        $this->post("api/v1/permissions/users/{$userId}/roles", ['role_id' => $roleId]);

        return true;
    }

    /**
     * Create a new tenant.
     *
     * @param array<string, mixed> $data Tenant data (name, description, etc.)
     * @return array<string, mixed>
     * @throws \Cognee\Exceptions\CogneeException
     */
    public function createTenant(array $data): array
    {
        return $this->post('api/v1/permissions/tenants', $data);
    }
}
