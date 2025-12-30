<?php
namespace Lia\Algolia\Core\Content\FilterPage;

use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\System\SalesChannel\SalesChannelCollection;
use Lia\Algolia\Core\Content\FilterPage\Translation\FilterPageTranslationEntity;

class FilterPageEntity extends Entity
{
    protected ?string $metaRobots = null;
    protected ?string $categoryId = null;
    protected ?CategoryEntity $category = null;

    protected ?SalesChannelCollection $salesChannels = null;
    protected ?PropertyGroupOptionCollection $properties = null;

    protected $translated = null;

    public function getMetaRobots(): ?string
    {
        return $this->metaRobots;
    }

    public function setMetaRobots(?string $metaRobots): void
    {
        $this->metaRobots = $metaRobots;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(?string $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getCategory(): ?CategoryEntity
    {
        return $this->category;
    }

    public function setCategory(?CategoryEntity $category): void
    {
        $this->category = $category;
    }

    public function getSalesChannels(): ?SalesChannelCollection
    {
        return $this->salesChannels;
    }

    public function setSalesChannels(?SalesChannelCollection $salesChannels): void
    {
        $this->salesChannels = $salesChannels;
    }

    public function getProperties(): ?PropertyGroupOptionCollection
    {
        return $this->properties;
    }

    public function setProperties(?PropertyGroupOptionCollection $properties): void
    {
        $this->properties = $properties;
    }

    public function getTranslated(): array
    {
        return $this->translated;
    }

    public function setTranslated(?FilterPageTranslationEntity $translated): void
    {
        $this->translated = $translated;
    }
}
