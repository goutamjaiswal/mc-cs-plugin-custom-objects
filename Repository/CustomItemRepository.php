<?php

declare(strict_types=1);

/*
 * @copyright   2018 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CustomObjectsBundle\Repository;

use MauticPlugin\CustomObjectsBundle\Entity\CustomObject;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\CustomObjectsBundle\Entity\CustomItem;
use Doctrine\ORM\Query\Expr\Join;
use MauticPlugin\CustomObjectsBundle\Entity\CustomItemXrefCustomItem;
use Mautic\CoreBundle\Entity\CommonRepository;

class CustomItemRepository extends CommonRepository
{
    use DbalQueryTrait;

    /**
     * @param CustomObject $customObject
     * @param Lead         $contact
     *
     * @return int
     */
    public function countItemsLinkedToContact(CustomObject $customObject, Lead $contact): int
    {
        $queryBuilder = $this->createQueryBuilder('ci', 'ci.id');
        $queryBuilder->select($queryBuilder->expr()->countDistinct('ci.id'));
        $queryBuilder->innerJoin('ci.contactReferences', 'cixctct');
        $queryBuilder->where('ci.customObject = :customObjectId');
        $queryBuilder->andWhere('cixctct.contact = :contactId');
        $queryBuilder->setParameter('customObjectId', $customObject->getId());
        $queryBuilder->setParameter('contactId', $contact->getId());

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param CustomObject $customObject
     * @param CustomItem   $customItem
     *
     * @return int
     */
    public function countItemsLinkedToAnotherItem(CustomObject $customObject, CustomItem $customItem): int
    {
        $queryBuilder = $this->createQueryBuilder('ci', 'ci.id');
        $queryBuilder->select($queryBuilder->expr()->countDistinct('ci.id'));
        $queryBuilder->innerJoin(
            CustomItemXrefCustomItem::class,
            'cixci',
            Join::WITH,
            'ci.id = cixci.customItemLower OR ci.id = cixci.customItemHigher'
        );
        $queryBuilder->where('ci.customObject = :customObjectId');
        $queryBuilder->andWhere('ci.id != :customItemId');
        $queryBuilder->andWhere($queryBuilder->expr()->orX(
            $queryBuilder->expr()->eq('cixci.customItemLower', ':customItemId'),
            $queryBuilder->expr()->eq('cixci.customItemHigher', ':customItemId')
        ));
        $queryBuilder->setParameter('customObjectId', $customObject->getId());
        $queryBuilder->setParameter('customItemId', $customItem->getId());

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Used by internal Mautic methods. Use the contstant difectly instead.
     *
     * @return string
     */
    public function getTableAlias(): string
    {
        return CustomItem::TABLE_ALIAS;
    }
}
