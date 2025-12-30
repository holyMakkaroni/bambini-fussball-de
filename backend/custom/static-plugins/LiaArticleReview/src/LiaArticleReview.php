<?php declare(strict_types=1);

namespace Lia\ArticleReview;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class LiaArticleReview extends Plugin
{
    const BUNDLE_NAME = 'LiaArticleReview';

    const MAIL_TYPES_AND_TEMPLATES = [
        [
            'table' => 'mail_template_type',
            'technicalName' => 'lia.article.review.email',
            'availableEntities' => [
                'customer' => 'customer',
                'salesChannel' => 'salesChannel',
                'order' => 'order'
            ],
            'translations' => [
                [
                    'code' => 'en-GB',
                    'name' => 'Lia article review'
                ],
                [
                    'code' => 'de-DE',
                    'name' => 'Lia Artikelbewertung'
                ],
            ],
            'templates' => [
                [
                    'table' => 'mail_template',
                    'translations' => [
                        [
                            'code' => 'en-GB',
                            'senderName' => '{{ salesChannel.name }}',
                            'subject' => 'Your opinion is important to us! Please rate your purchase at {{ salesChannel.name }}',
                            'description' => 'Lia article review email'
                        ],
                        [
                            'code' => 'de-DE',
                            'senderName' => '{{ salesChannel.name }}',
                            'subject' => 'Ihre Meinung ist uns wichtig! Bitte bewerten Sie Ihren Einkauf bei {{ salesChannel.name }}',
                            'description' => 'Lia Artikelbewertungs Email'
                        ]
                    ]
                ]
            ]
        ]
    ];

    /**
     * Uninstall
     */
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container->get('Doctrine\DBAL\Connection');

        foreach(self::MAIL_TYPES_AND_TEMPLATES as $mailTypeTemplates)
        {
            $queryBuilder = $connection->createQueryBuilder();

            $currentTypeId = $queryBuilder->select($mailTypeTemplates['table'] . '.id')
                ->from($mailTypeTemplates['table'])
                ->where($mailTypeTemplates['table'] . '.technical_name = :technical_name')
                ->setParameter('technical_name', $mailTypeTemplates['technicalName'])
                ->executeQuery()
                ->fetchOne();

            $queryBuilder->delete('mail_template')
                ->where('mail_template.mail_template_type_id = :mail_template_type_id')
                ->setParameter('mail_template_type_id', $currentTypeId)
                ->executeQuery();

            $queryBuilder->delete($mailTypeTemplates['table'])
                ->where($mailTypeTemplates['table'] . '.id = :id')
                ->setParameter('id', $currentTypeId)
                ->executeQuery();
        }
    }
}
