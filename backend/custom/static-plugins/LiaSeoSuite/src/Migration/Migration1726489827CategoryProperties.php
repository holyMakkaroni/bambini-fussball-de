<?php declare(strict_types=1);

namespace Lia\SeoSuite\Migration;

use Doctrine\DBAL\Connection;
use Lia\SeoSuite\Service\Setup\InstallService;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1726489827CategoryProperties extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1726489827;
    }

    public function update(Connection $connection): void
    {
        InstallService::installCategoryCustomFields($connection);
    }
}
