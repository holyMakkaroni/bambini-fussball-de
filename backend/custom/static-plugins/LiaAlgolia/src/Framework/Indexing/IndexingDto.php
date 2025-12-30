<?php declare(strict_types=1);

namespace Lia\Algolia\Framework\Indexing;

class IndexingDto
{
    protected array $ids;

    public function __construct(
        array $ids,
        protected string $index,
        protected string $entity,
        protected ?string $localeCode = null
    ) {
        $this->ids = array_values($ids);
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getLocaleCode(): ?string
    {
        return strtok($this->localeCode, '-');
    }
}