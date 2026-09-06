<?php

namespace App\Services\Provisioning;

use App\Models\Tenant;

interface TenantProvisioner
{
    /**
     * Provisions a brand-new, empty database + user for a tenant.
     * $desiredSlug: an optional manually-chosen database/user name (ASCII);
     * when omitted, one is derived from $storeName, falling back to a random
     * name if the store's name doesn't reduce to anything usable in ASCII.
     * Returns ['db_host','db_port','db_database','db_username','db_password'].
     * Throws \RuntimeException with an Arabic message on failure.
     */
    public function provision(string $storeName, ?string $desiredSlug = null): array;

    /**
     * Permanently destroys a tenant's database + database user.
     * Throws \RuntimeException with an Arabic message on failure — callers
     * must not remove the tenant's registry row unless this succeeds.
     */
    public function deprovision(Tenant $tenant): void;
}
