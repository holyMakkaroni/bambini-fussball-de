<?php declare(strict_types=1);

namespace Lia\ArticleReview\Service;

use DateTime;
use Lia\ArticleReview\LiaArticleReview;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;

class ReviewService
{
    private EntityRepository $orderRepository;

    private ConfigService $configService;

    public function __construct(
        EntityRepository $orderRepository,
        ConfigService $configService
    )
    {
        $this->orderRepository = $orderRepository;
        $this->configService = $configService;
    }

    /**
     * @throws \Exception
     */
    public function sendEmails()
    {
        $orders = $this->getOrders();
    }

    /**
     * @throws \Exception
     */
    private function getOrders(): EntityCollection
    {
        $context = Context::createDefaultContext();
        $daysAfterShipping = $this->configService->getDaysAfterShipping();
        $now = new DateTime(date(Defaults::STORAGE_DATE_FORMAT));
        $now->modify('-' . $daysAfterShipping . 'day');

        $orderCriteria = new Criteria();
        $orderCriteria->addFilter(new EqualsFilter('customFields.' . LiaArticleReview::BUNDLE_NAME .'.agreeRatingConsent', 'true'));
        $orderCriteria->addFilter(new RangeFilter('customFields.' . LiaArticleReview::BUNDLE_NAME .'.shipped_at', [
            RangeFilter::LTE => date(Defaults::STORAGE_DATE_TIME_FORMAT, $now->getTimestamp())
        ]));
        $orderCriteria->addFilter(new EqualsFilter('customFields.' . LiaArticleReview::BUNDLE_NAME .'.email_send_at', null));

        return $this->orderRepository->search($orderCriteria, $context)->getEntities();
    }
}