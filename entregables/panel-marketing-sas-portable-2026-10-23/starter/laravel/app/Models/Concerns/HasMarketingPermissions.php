<?php

namespace App\Models\Concerns;

trait HasMarketingPermissions
{
    public function initializeHasMarketingPermissions(): void
    {
        $this->mergeCasts([
            'menu_permissions' => 'array',
            'active' => 'boolean',
        ]);
    }

    public function canNavigateTo(string $permission): bool
    {
        if (! str_starts_with($permission, 'marketing.')) {
            return false;
        }

        $attributes = $this->getAttributes();

        if (array_key_exists('active', $attributes) && ! (bool) $this->getAttribute('active')) {
            return false;
        }

        if (($this->getAttribute('role') ?? '') === 'superadmin') {
            return true;
        }

        $permissions = $this->getAttribute('menu_permissions');

        if ($permissions === null) {
            return true;
        }

        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?: [];
        }

        return in_array($permission, (array) $permissions, true);
    }
}
