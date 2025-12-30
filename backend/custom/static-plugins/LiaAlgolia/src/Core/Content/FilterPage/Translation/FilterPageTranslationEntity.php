<?php
namespace Lia\Algolia\Core\Content\FilterPage\Translation;

use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class FilterPageTranslationEntity extends TranslationEntity
{
    protected ?string $name = null;
    protected ?string $seoText = null;
    protected ?string $metaTitle = null;
    protected ?string $metaDescription = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getSeoText(): ?string
    {
        return $this->seoText;
    }

    public function setSeoText(?string $seoText): void
    {
        $this->seoText = $seoText;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }
}