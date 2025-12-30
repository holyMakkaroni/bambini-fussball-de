<?php declare(strict_types=1);

namespace Lia\Algolia\Framework\Command;

use Lia\Algolia\Framework\Indexing\AlgoliaIndexer;
use Lia\Algolia\Service\SalesChannelService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Command\ConsoleProgressTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'algolia:index',
    description: 'Index all entities into algolia',
)]
class AlgoliaIndexingCommand extends Command
{
    use ConsoleProgressTrait;

    public function __construct(
        private readonly AlgoliaIndexer  $algoliaIndexer,
        private readonly MessageBusInterface $messageBus,
        private readonly SalesChannelService $salesChannelService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('no-queue', null, InputOption::VALUE_NONE, 'Do not use the queue for indexing');
        $this->addOption('sales-channel-ids', null, InputOption::VALUE_OPTIONAL, 'Sales channel ids to index');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $salesChannelOption = $input->getOption('sales-channel-ids');
        $salesChannelIds = $salesChannelOption ? explode(',', $salesChannelOption) : [];

        /* @var $salesChannels SalesChannelEntity[] */
        $salesChannels = $this->salesChannelService->getAllSalesChannels($salesChannelIds);

        foreach ($salesChannels as $salesChannel) {
            $languages = $salesChannel->getLanguages()->getElements();

            foreach ($languages as $language) {
                $stopwatch = new Stopwatch();
                $stopwatch->start('algolia-indexing-' . $language->getId());

                $entities = [];
                $offset = null;

                $progressBar = new ProgressBar($output);
                $progressBar->start();

                while ($message = $this->algoliaIndexer->iterate($language, $salesChannel, $offset, $entities)) {
                    $offset = $message->getOffset();

                    $step = \count($message->getData()->getIds());

                    if ($input->getOption('no-queue')) {
                        $this->algoliaIndexer->__invoke($message);

                        $progressBar->advance($step);

                        continue;
                    }

                    $this->messageBus->dispatch($message);

                    $progressBar->advance($step);
                }

                $progressBar->finish();

                $stopwatch->stop('algolia-indexing-' . $language->getId());
            }
        }

        return self::SUCCESS;
    }
}