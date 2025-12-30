<?php declare(strict_types=1);

namespace Lia\Algolia\Factory;

use Algolia\AlgoliaSearch\SearchClient;
use Lia\Algolia\Service\ConfigService;

class ClientFactory
{
    protected string $salesChannelId;

    public function __construct(
        private readonly ConfigService $configService
    ) {}

    public function createClient(): SearchClient
    {
        return SearchClient::create($this->configService->getApplicationId($this->salesChannelId), $this->configService->getAdminApiKey($this->salesChannelId));
    }

    public function setSalesChannelId(string $salesChannelId): void
    {
        $this->salesChannelId = $salesChannelId;
    }
}