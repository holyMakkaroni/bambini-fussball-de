<?php declare(strict_types=1);

namespace Lia\SeoSuite\Migration;

use Doctrine\DBAL\Connection;
use Lia\SeoSuite\Service\Setup\InstallService;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1692278687Install extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1692278687;
    }

    public function update(Connection $connection): void
    {
        InstallService::initAttributes($connection);
    }
}
