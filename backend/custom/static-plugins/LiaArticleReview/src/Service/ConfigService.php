<?php declare(strict_types=1);

namespace Lia\ArticleReview\Service;

use Lia\ArticleReview\LiaArticleReview;
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

    public function getAgreeConsentByDefault(string $salesChannelId = null): bool
    {
        return $this->systemConfigService->get(LiaArticleReview::BUNDLE_NAME . '.config.agreeConsentByDefault', $salesChannelId);
    }

    public function getDaysAfterShipping(string $salesChannelId = null): int
    {
        return $this->systemConfigService->get(LiaArticleReview::BUNDLE_NAME . '.config.daysAfterShipping', $salesChannelId);
    }

    public function getDeliveryState(string $salesChannelId = null): string
    {
        return $this->systemConfigService->get(LiaArticleReview::BUNDLE_NAME . '.config.deliveryState', $salesChannelId);
    }

    public function getEmailTemplate(string $salesChannelId = null): string
    {
        return $this->systemConfigService->get(LiaArticleReview::BUNDLE_NAME . '.config.emailTemplate', $salesChannelId);
    }

    public function getBccRecipients(string $salesChannelId = null): string
    {
        return $this->systemConfigService->get(LiaArticleReview::BUNDLE_NAME . '.config.bccRecipients', $salesChannelId);
    }
}