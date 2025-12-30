<?php declare(strict_types=1);

namespace Lia\ArticleReview\Service\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class ArticleReviewEmailTask extends ScheduledTask
{

    public static function getTaskName(): string
    {
        return 'lia.articleReview.email';
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultInterval(): int
    {
        return 86400;
    }
}