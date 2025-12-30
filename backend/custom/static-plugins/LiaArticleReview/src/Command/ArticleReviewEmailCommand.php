<?php declare(strict_types=1);

namespace Lia\ArticleReview\Command;

use Lia\ArticleReview\Service\ScheduledTask\ArticleReviewEmailTaskHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArticleReviewEmailCommand extends Command
{
    private ArticleReviewEmailTaskHandler $articleReviewEmailTaskHandler;

    public function __construct(
        ArticleReviewEmailTaskHandler $articleReviewEmailTaskHandler,
        string $name = null
    )
    {
        $this->articleReviewEmailTaskHandler = $articleReviewEmailTaskHandler;
        parent::__construct($name);
    }

    // Command name
    protected static $defaultName = 'livingactive:article-review:email:send';

    // Provides a description, printed out in bin/console
    protected function configure(): void
    {
        $this->setDescription('Check orders and send automatically the email review email');
    }

    // Actual code executed in the command

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->articleReviewEmailTaskHandler->run();
        return 0;
    }
}