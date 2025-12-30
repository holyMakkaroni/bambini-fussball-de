<?php declare(strict_types=1);

namespace Lia\ArticleReview\Migration;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Lia\ArticleReview\LiaArticleReview;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1695986269Install extends MigrationStep
{
    public string $basePath = '';

    public function getCreationTimestamp(): int
    {
        return 1695986269;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        foreach (LiaArticleReview::MAIL_TYPES_AND_TEMPLATES as $mailTypeTemplates)
        {
            if($this->checkMailTypeExist($connection, $mailTypeTemplates)) {
                return;
            }

            $mailTemplateTypeId = $this->createMailTemplateType($connection, $mailTypeTemplates);
            $this->createMailTemplate($connection, $mailTemplateTypeId, $mailTypeTemplates);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    /**
     * @throws Exception
     */
    private function getLanguageIdByLocale(Connection $connection, string $locale): ?string
    {
        $queryBuilder = $connection->createQueryBuilder();

        $languageId = $queryBuilder->select('language.id')
            ->from('language')
            ->innerJoin('language', 'locale', 'locale', 'locale.id = language.locale_id')
            ->where('locale.code = :code')
            ->setParameter('code', $locale)
            ->fetchOne();

        if (empty($languageId)) {
            return null;
        }

        return $languageId;
    }

    /**
     * @throws Exception
     */
    private function checkMailTypeExist(Connection $connection, array $mailTypeTemplates): bool
    {
        $queryBuilder = $connection->createQueryBuilder();

        return $queryBuilder->select($mailTypeTemplates['table'] . '.technical_name')
            ->from($mailTypeTemplates['table'])
            ->where($mailTypeTemplates['table'] . '.technical_name = :technical_name')
            ->setParameter('technical_name', $mailTypeTemplates['technicalName'])
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * @throws Exception
     */
    private function createMailTemplateType(Connection $connection, array $mailTypeTemplates): string
    {
        $queryBuilder = $connection->createQueryBuilder();
        $mailTemplateTypeId = Uuid::randomHex();

        $queryBuilder->insert($mailTypeTemplates['table'])
            ->values([
                'id' => '?',
                'technical_name' => '?',
                'available_entities' => '?',
                'created_at' => '?',
            ])
            ->setParameter(0, Uuid::fromHexToBytes($mailTemplateTypeId))
            ->setParameter(1, $mailTypeTemplates['technicalName'])
            ->setParameter(2, json_encode($mailTypeTemplates['availableEntities']))
            ->setParameter(3, (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT))
            ->executeQuery();

        if(!empty($mailTypeTemplates['translations']))
        {
            foreach($mailTypeTemplates['translations'] as $translation)
            {
                $localeId = $this->getLanguageIdByLocale($connection, $translation['code']);
                $queryBuilder->insert('mail_template_type_translation')
                    ->values([
                        'mail_template_type_id' => '?',
                        'language_id'  => '?',
                        'name'  => '?',
                        'created_at'  => '?'
                    ])
                ->setParameter(0, Uuid::fromHexToBytes($mailTemplateTypeId))
                ->setParameter(1, $localeId)
                ->setParameter(2, $translation['name'])
                ->setParameter(3, (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT))
                ->executeQuery();
            }
        }

        return $mailTemplateTypeId;
    }

    /**
     * @throws Exception
     */
    private function createMailTemplate(Connection $connection, string $mailTemplateTypeId, array $mailTypeTemplates): void
    {
        if(!empty($mailTypeTemplates['templates']))
        {
            $queryBuilder = $connection->createQueryBuilder();
            $mailTemplateId = Uuid::randomHex();

            foreach($mailTypeTemplates['templates'] as $template)
            {
                $queryBuilder->insert($template['table'])
                    ->values([
                        'id' => '?',
                        'mail_template_type_id' => '?',
                        'system_default' => '?',
                        'created_at' => '?'
                    ])
                    ->setParameter(0, Uuid::fromHexToBytes($mailTemplateId))
                    ->setParameter(1, Uuid::fromHexToBytes($mailTemplateTypeId))
                    ->setParameter(2, 0)
                    ->setParameter(3, (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT))
                    ->executeQuery();

                if(!empty($template['translations']))
                {
                    foreach ($template['translations'] as $translation)
                    {
                        $localeId = $this->getLanguageIdByLocale($connection, $translation['code']);

                        $queryBuilder->insert('mail_template_translation')
                            ->values([
                                'mail_template_id' => '?',
                                'language_id' => '?',
                                'sender_name' => '?',
                                'subject' => '?',
                                'description' => '?',
                                'content_html' => '?',
                                'content_plain' => '?',
                                'created_at' => '?'
                            ])
                            ->setParameter(0, Uuid::fromHexToBytes($mailTemplateId))
                            ->setParameter(1, $localeId)
                            ->setParameter(2, $translation['senderName'])
                            ->setParameter(3, $translation['subject'])
                            ->setParameter(4, $translation['description'])
                            ->setParameter(5, $this->getEmailContent($translation['code'], 'html'))
                            ->setParameter(6, $this->getEmailContent($translation['code'], 'plain'))
                            ->setParameter(7, (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT))
                            ->executeQuery();
                    }
                }
            }
        }
    }

    private function getEmailContent(string $locale, string $type): string
    {
        return @file_get_contents(__DIR__ . '/../Resources/emails/' . $locale . '/' . $type . '.twig');
    }
}
