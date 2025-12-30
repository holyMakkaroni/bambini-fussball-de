<?php declare(strict_types=1);

namespace Lia\ArticleReview\Service\ScheduledTask;

use Lia\ArticleReview\Service\ReviewService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: ArticleReviewEmailTask::class)]
class ArticleReviewEmailTaskHandler extends ScheduledTaskHandler
{
    private ReviewService $emailReviewService;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        ReviewService $emailReviewService
    )
    {
        parent::__construct($scheduledTaskRepository);
        $this->emailReviewService = $emailReviewService;
    }

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $this->emailReviewService->sendEmails();
        echo 'Running logic automatically send review emails';
    }
}