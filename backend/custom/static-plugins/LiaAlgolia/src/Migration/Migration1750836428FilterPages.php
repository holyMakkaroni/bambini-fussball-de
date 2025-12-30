<?php declare(strict_types=1);

namespace Lia\Algolia\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1750836428FilterPages extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1750836428;
    }

    public function update(Connection $connection): void
    {
        // Main entity
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS `lia_algolia_filter_page` (
                `id` BINARY(16) NOT NULL,
                `category_id` BINARY(16),
                `meta_robots` VARCHAR(255) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk.lia_algolia_filter_page.category_id` FOREIGN KEY (`category_id`) 
                    REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Translations
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS `lia_algolia_filter_page_translation` (
                `lia_algolia_filter_page_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) NULL,
                `seo_text` LONGTEXT NULL,
                `meta_title` VARCHAR(255) NULL,
                `meta_description` VARCHAR(255) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`lia_algolia_filter_page_id`, `language_id`),
                CONSTRAINT `fk.lia_algolia_filter_page_translation.language_id` FOREIGN KEY (`language_id`)
                    REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.lia_algolia_filter_page_translation.page_id` FOREIGN KEY (`lia_algolia_filter_page_id`)
                    REFERENCES `lia_algolia_filter_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // SalesChannel Mapping
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS `lia_algolia_filter_page_sales_channel` (
                `filter_page_id` BINARY(16) NOT NULL,
                `sales_channel_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`filter_page_id`, `sales_channel_id`),
                CONSTRAINT `fk.lia_algolia_filter_page_sales_channel.filter_page_id` FOREIGN KEY (`filter_page_id`)
                    REFERENCES `lia_algolia_filter_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.lia_algolia_filter_page_sales_channel.sales_channel_id` FOREIGN KEY (`sales_channel_id`)
                    REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Property Mapping
        $connection->executeStatement("
            CREATE TABLE IF NOT EXISTS `lia_algolia_filter_page_property` (
                `filter_page_id` BINARY(16) NOT NULL,
                `property_group_option_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`filter_page_id`, `property_group_option_id`),
                CONSTRAINT `fk.lia_algolia_filter_page_property.filter_page_id` FOREIGN KEY (`filter_page_id`)
                    REFERENCES `lia_algolia_filter_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.lia_algolia_filter_page_property.option_id` FOREIGN KEY (`property_group_option_id`)
                    REFERENCES `property_group_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
}
