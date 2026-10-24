<?php

namespace App\Support;

use App\Models\Marketing\Company;

final class MarketingChannelCatalog
{
    public const OTHER_NETWORKS = ['TikTok', 'LinkedIn', 'YouTube', 'Otros canales'];

    public static function all(): array
    {
        return [
            ['key' => 'whatsapp', 'name' => 'WhatsApp Business', 'network' => 'WhatsApp', 'description' => 'Mensajes y atención a clientes', 'detail' => 'Configura el número oficial de tu empresa con WhatsApp Cloud API y consulta sus conversaciones.'],
            ['key' => 'meta', 'name' => 'Meta Ads', 'network' => 'Meta Ads', 'description' => 'Publicidad y campañas digitales', 'detail' => 'Registra tu cuenta publicitaria para organizar sus campañas. El registro no sincroniza anuncios ni resultados automáticamente.'],
            ['key' => 'instagram', 'name' => 'Instagram', 'network' => 'Instagram', 'description' => 'Contenido y comunidad', 'detail' => 'Registra el perfil de tu empresa para identificarlo en sus campañas. El registro no sincroniza publicaciones ni mensajes.'],
            ['key' => 'facebook', 'name' => 'Facebook', 'network' => 'Facebook', 'description' => 'Página y captación de clientes', 'detail' => 'Registra la página de tu empresa y asígnala a sus campañas. El registro no sincroniza publicaciones ni formularios de clientes.'],
            ['key' => 'chatgpt', 'name' => 'ChatGPT', 'network' => 'ChatGPT', 'description' => 'Asistente de campañas y contenido', 'detail' => 'Registra la cuenta o espacio de trabajo que utiliza tu equipo. El registro no conecta una API ni genera contenido automáticamente.'],
            ['key' => 'x', 'name' => 'X', 'network' => 'X', 'description' => 'Cuenta y publicaciones', 'detail' => 'Registra la cuenta de tu empresa para organizar sus campañas. El registro no sincroniza publicaciones ni mensajes.'],
            ['key' => 'google', 'name' => 'Google Ads', 'network' => 'Google Ads', 'description' => 'Anuncios y conversiones', 'detail' => 'Registra tu cuenta publicitaria y asígnala a campañas. El registro no sincroniza anuncios ni conversiones automáticamente.'],
            ['key' => 'other', 'name' => 'Otros canales', 'network' => 'Otros canales', 'description' => 'Más redes para tus campañas', 'detail' => 'Registra una cuenta de TikTok, LinkedIn, YouTube u otro canal para organizar las redes de tus campañas.'],
        ];
    }

    public static function configurationsFor(?Company $company): array
    {
        $saved = data_get($company?->settings, 'marketing_channels', []);
        if (! is_array($saved)) {
            return [];
        }

        $configurations = [];
        foreach (self::all() as $channel) {
            $configuration = $saved[$channel['key']] ?? null;
            if ($channel['key'] === 'whatsapp' || ! is_array($configuration)) {
                continue;
            }

            $configurations[$channel['key']] = [
                'account_name' => is_string($configuration['account_name'] ?? null) ? $configuration['account_name'] : '',
                'account_identifier' => is_string($configuration['account_identifier'] ?? null) ? $configuration['account_identifier'] : '',
                'profile_url' => is_string($configuration['profile_url'] ?? null) ? $configuration['profile_url'] : null,
                'network' => $channel['key'] === 'other'
                    ? (in_array($configuration['network'] ?? null, self::OTHER_NETWORKS, true) ? $configuration['network'] : null)
                    : $channel['network'],
            ];
        }

        return $configurations;
    }

    public static function configuredKeysFor(?Company $company): array
    {
        if (! $company) {
            return [];
        }

        $configurations = self::configurationsFor($company);
        $hasWhatsApp = $company->whatsappNumbers->contains(fn ($number) => $number->status === 'active' && $number->hasCloudConfiguration());

        return collect(self::all())->filter(function (array $channel) use ($configurations, $hasWhatsApp): bool {
            if ($channel['key'] === 'whatsapp') {
                return $hasWhatsApp;
            }

            $configuration = $configurations[$channel['key']] ?? [];

            return filled($configuration['account_name'] ?? null)
                && filled($configuration['account_identifier'] ?? null)
                && filled($configuration['network'] ?? null);
        })->pluck('key')->all();
    }
}
