<?php declare(strict_types=1);

namespace Lia\Algolia\Service;

use Lia\Algolia\LiaAlgolia;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ConfigService
{
    private SystemConfigService $systemConfigService;

    public function __construct(
        SystemConfigService $systemConfigService
    )
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getApplicationId(string $salesChannelId): string
    {
        return $this->systemConfigService->get(LiaAlgolia::BUNDLE_NAME . '.config.applicationId', $salesChannelId);
    }

    public function getSearchApiKey(string $salesChannelId): string
    {
        return $this->systemConfigService->get(LiaAlgolia::BUNDLE_NAME . '.config.searchApiKey', $salesChannelId);
    }

    public function getAdminApiKey(string $salesChannelId): string
    {
        return $this->systemConfigService->get(LiaAlgolia::BUNDLE_NAME . '.config.adminApiKey', $salesChannelId);
    }
}