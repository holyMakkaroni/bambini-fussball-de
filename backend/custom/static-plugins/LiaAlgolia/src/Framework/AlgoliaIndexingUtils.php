<?php declare(strict_types=1);

namespace Lia\Algolia\Framework;

use Doctrine\DBAL\Connection;
use JsonException as JsonExceptionAlias;
use Shopware\Core\Content\Product\ProductEntity;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AlgoliaIndexingUtils
{
    /**
     * @description strip html tags from text and truncate to 32766 characters
     */
    public static function stripText(string|null $text): string
    {
        if(!$text) {
            return '';
        }

        $text = strip_tags($text);

        if (mb_strlen($text) >= 32766) {
            return mb_substr($text, 0, 32766);
        }

        return $text;
    }

    public static function getProductInheriteValues(array $product, array $parentProduct, string $field, $translated = false)
    {
        if($product) {
            if($translated) {
                return self::getTranslation($product, $field) ?? self::getTranslation($parentProduct, $field);
            }
            return $product[$field] ?? self::getParentValue($parentProduct, $field);
        }

        if($translated) {
            return self::getTranslation($parentProduct, $field);
        }

        return self::getParentValue($parentProduct, $field);
    }

    public static function getParentValue(array $entity, string $field)
    {
        if(!$entity) {
            return '';
        }

        return $entity[$field];
    }

    public static function getTranslation(array $entity, string $field)
    {
        if(!$entity) {
            return '';
        }

        return $entity['translated'][$field];
    }
}