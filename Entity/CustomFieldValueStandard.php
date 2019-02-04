<?php

declare(strict_types=1);

/*
 * @copyright   2019 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CustomObjectsBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use MauticPlugin\CustomObjectsBundle\Entity\CustomField;
use MauticPlugin\CustomObjectsBundle\Entity\CustomItem;
use MauticPlugin\CustomObjectsBundle\Entity\CustomFieldValueInterface;

abstract class CustomFieldValueStandard implements CustomFieldValueInterface
{
    /**
     * @var CustomField
     */
    protected $customField;

    /**
     * @var CustomItem
     */
    protected $customItem;

    /**
     * Flag to know whether to update this entity manually or let EntityManager to handle it.
     *
     * @var boolean
     */
    protected $updateManually = false;

    /**
     * @param CustomField $customField
     * @param CustomItem  $customItem
     */
    public function __construct(CustomField $customField, CustomItem $customItem)
    {
        $this->customField = $customField;
        $this->customItem  = $customItem;
    }

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function addReferenceColumns(ClassMetadataBuilder $builder): void
    {
        $builder->createManyToOne('customField', CustomField::class)
            ->addJoinColumn('custom_field_id', 'id', false, false, 'CASCADE')
            ->makePrimaryKey()
            ->fetchExtraLazy()
            ->build();

        $builder->createManyToOne('customItem', CustomItem::class)
            ->addJoinColumn('custom_item_id', 'id', false, false, 'CASCADE')
            ->makePrimaryKey()
            ->fetchExtraLazy()
            ->build();
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('customField', new Assert\NotBlank());
        $metadata->addPropertyConstraint('customItem', new Assert\NotBlank());
    }

    /**
     * @return string
     */
    public function getId()
    {
        return "{$this->customField->getId()}_{$this->customItem->getId()}";
    }

    /**
     * @return CustomField
     */
    public function getCustomField()
    {
        return $this->customField;
    }

    /**
     * @return CustomItem
     */
    public function getCustomItem()
    {
        return $this->customItem;
    }

    public function updateThisEntityManually()
    {
        $this->updateManually = true;
    }

    /**
     * @return bool
     */
    public function shouldBeUpdatedManually()
    {
        return $this->updateManually;
    }
}