<?php declare(strict_types=1);

namespace Lia\SeoSuite\Service\Setup;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class UninstallService
{
    const PROPERTY_GROUP_AND_MANUFACTURER_CUSTOM_FIELDS = [
        "filterName",
        "filterPriority"
    ];
    const PROPERTY_OPTION_CUSTOM_FIELDS = [
        "optionValue"
    ];

    /**
     * @throws Exception
     */
    public static function uninstall(Connection $connection): void
    {
        $query = "";

        foreach (self::PROPERTY_GROUP_AND_MANUFACTURER_CUSTOM_FIELDS as $customField) {
            $query .= "UPDATE `property_group_translation` SET `custom_fields` = JSON_REMOVE(`property_group_translation`.`custom_fields`,'$.$customField');";
            $query .= "UPDATE `product_manufacturer_translation` SET `custom_fields` = JSON_REMOVE(`product_manufacturer_translation`.`custom_fields`,'$.$customField');";
        }
        foreach (self::PROPERTY_OPTION_CUSTOM_FIELDS as $customField) {
            $query .= "UPDATE `property_group_option_translation` SET `custom_fields` = JSON_REMOVE(`property_group_option_translation`.`custom_fields`,'$.$customField');";
        }

        $connection->executeStatement($query);
    }
}
