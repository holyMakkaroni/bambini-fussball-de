<?php declare(strict_types=1);

namespace Lia\Algolia\Framework\Indexing\Event;

use Lia\Algolia\Framework\AbstractAlgoliaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IterableQuery;

class AlgoliaIndexIteratorEvent
{
    public function __construct(
        public readonly AbstractAlgoliaDefinition $abstractAlgoliaDefinition,
        public IterableQuery $iterator,
    ) {
    }
}