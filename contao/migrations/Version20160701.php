<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-news
 * @license    LGPL-3.0+
 * @filesource
 */

namespace DoctrineMigrations\AvisotaMessageElementNews;

use Avisota\Contao\Entity\MessageContent;
use Contao\Database;
use Contao\Doctrine\ORM\EntityHelper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20160701
 *
 * @package DoctrineMigrations\AvisotaMessageElementNews
 */
class Version20160701 extends AbstractMigration
{

    /**
     * Migrate up.
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('orm_avisota_message_content')) {
            return;
        }

        $table = $schema->getTable('orm_avisota_message_content');

        if ($table->hasColumn('newsId')) {
            $this->migrateFromStringToArray('newsId');
        }
    }

    /**
     * Migrate down.
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }

    /**
     * Mograte from string to array.
     *
     * @param $column
     */
    protected function migrateFromStringToArray($column)
    {
        $database = Database::getInstance();
        $result   = $database->prepare("SELECT * FROM orm_avisota_message_content WHERE $column>0")
            ->execute();
        
        if ($result->count() < 1) {
            return;
        }

        while ($result->next()) {
            if (is_array(unserialize($result->$column))) {
                continue;
            }

            $database->prepare("UPDATE orm_avisota_message_content %s WHERE id=?")
                ->set(array($column => serialize((array) $result->$column)))
                ->execute($result->id);
        }
    }
}
