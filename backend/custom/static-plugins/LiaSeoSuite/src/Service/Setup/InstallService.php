<?php declare(strict_types=1);

namespace Lia\SeoSuite\Service\Setup;

use Doctrine\DBAL\Connection;
use Lia\SeoSuite\Service\SlugService;
use Shopware\Core\Content\Category\Aggregate\CategoryTranslation\CategoryTranslationDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturerTranslation\ProductManufacturerTranslationDefinition;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOptionTranslation\PropertyGroupOptionTranslationDefinition;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupTranslation\PropertyGroupTranslationDefinition;

class InstallService
{
    public static function initAttributes(Connection $connection): void
    {
        $resultsOptions = [];
        $query = "SELECT HEX(`product_manufacturer_id`) as `id`, HEX(`language_id`) as `languageId`, `name`, `custom_fields` FROM  `product_manufacturer_translation`;";
        $resultsOptions[ProductManufacturerTranslationDefinition::ENTITY_NAME]['id_column'] = 'product_manufacturer_id';
        $resultsOptions[ProductManufacturerTranslationDefinition::ENTITY_NAME]['rows'] = $connection->executeQuery($query)->fetchAllAssociative();

        $query = "SELECT HEX(`property_group_id`) as `id`, HEX(`language_id`) as `languageId`, `name`, `custom_fields` FROM  `property_group_translation`;";
        $resultsOptions[PropertyGroupTranslationDefinition::ENTITY_NAME]['id_column'] = 'property_group_id';
        $resultsOptions[PropertyGroupTranslationDefinition::ENTITY_NAME]['rows'] = $connection->executeQuery($query)->fetchAllAssociative();

        $query = "SELECT HEX(`property_group_option_id`) as `id`, HEX(`language_id`) as `languageId`, `name`, `custom_fields` FROM  `property_group_option_translation`;";
        $resultsOptions[PropertyGroupOptionTranslationDefinition::ENTITY_NAME]['id_column'] = 'property_group_option_id';
        $resultsOptions[PropertyGroupOptionTranslationDefinition::ENTITY_NAME]['rows'] = $connection->executeQuery($query)->fetchAllAssociative();

        foreach ($resultsOptions as $table => $result) {
            foreach ($result['rows'] as $row) {
                if (empty($row['name'])) {
                    continue;
                }

                $key = SlugService::slug($row['name']);
                $customFields = json_decode($row['custom_fields'] !== null ? $row['custom_fields'] : '{}', true);

                if($table === PropertyGroupOptionTranslationDefinition::ENTITY_NAME) {
                    $customFields['optionValue'] = $key;
                } else {
                    $customFields['filterName'] = $key;
                    $customFields['filterPriority'] = 0;
                }

                $connection->createQueryBuilder()
                    ->update($table)
                    ->set('custom_fields', ':customFields')
                    ->where($result['id_column'] . " = UNHEX(:id)")
                    ->andWhere("language_id = UNHEX(:languageId)")
                    ->setParameter('customFields', json_encode($customFields))
                    ->setParameter('key', $key)
                    ->setParameter('id', $row['id'])
                    ->setParameter('languageId', $row['languageId'])
                    ->execute();
            }
        }
    }

    public static function installCategoryCustomFields(Connection $connection): void
    {
        $query = "SELECT HEX(`category_id`) as `id`, HEX(`language_id`) as `languageId`, `custom_fields` FROM  `category_translation`;";
        $resultsOptions[CategoryTranslationDefinition::ENTITY_NAME]['id_column'] = 'category_id';
        $resultsOptions[CategoryTranslationDefinition::ENTITY_NAME]['rows'] = $connection->executeQuery($query)->fetchAllAssociative();

        foreach ($resultsOptions as $table => $result) {
            foreach ($result['rows'] as $row) {
                if (empty($row['name'])) {
                    continue;
                }

                $customFields = json_decode($row['custom_fields'] !== null ? $row['custom_fields'] : '{}', true);
                $customFields['seoFilterPropertyIds'] = [];

                $connection->createQueryBuilder()
                    ->update($table)
                    ->set('custom_fields', ':customFields')
                    ->where($result['id_column'] . " = UNHEX(:id)")
                    ->andWhere("language_id = UNHEX(:languageId)")
                    ->setParameter('customFields', json_encode($customFields))
                    ->setParameter('id', $row['id'])
                    ->setParameter('languageId', $row['languageId'])
                    ->execute();
            }
        }
    }
}