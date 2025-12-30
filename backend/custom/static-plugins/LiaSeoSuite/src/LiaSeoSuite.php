<?php declare(strict_types=1);

namespace Lia\SeoSuite;

use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Lia\SeoSuite\Service\Setup\UninstallService;

class LiaSeoSuite extends Plugin
{
    const PARAM_PREFIX = 'lia';

    /**
     * @throws Exception
     */
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container->get('Doctrine\DBAL\Connection');

        UninstallService::uninstall($connection);
    }
}
