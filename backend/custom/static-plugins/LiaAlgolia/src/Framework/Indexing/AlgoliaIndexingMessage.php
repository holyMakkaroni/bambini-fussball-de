<?php declare(strict_types=1);

namespace Lia\Algolia\Framework\Indexing;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\MessageQueue\AsyncMessageInterface;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

class AlgoliaIndexingMessage implements AsyncMessageInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly IndexingDto $data,
        private readonly ?IndexerOffset $offset,
        private readonly Context $context,
        private readonly string $languageId,
        private readonly string $salesChannelId,
        private readonly string $navigationCategoryId,
    ) {
    }

    public function getData(): IndexingDto
    {
        return $this->data;
    }

    public function getOffset(): ?IndexerOffset
    {
        return $this->offset;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }
    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function getNavigationCategoryId(): string
    {
        return $this->navigationCategoryId;
    }
}