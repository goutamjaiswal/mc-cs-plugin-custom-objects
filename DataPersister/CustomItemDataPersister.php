<?php

namespace MauticPlugin\CustomObjectsBundle\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use MauticPlugin\CustomObjectsBundle\Entity\CustomItem;
use MauticPlugin\CustomObjectsBundle\Model\CustomItemModel;

final class CustomItemDataPersister implements DataPersisterInterface
{
    private CustomItemModel $customItemModel;

    public function __construct(CustomItemModel $customItemModel)
    {
        $this->customItemModel = $customItemModel;
    }

    public function supports($data): bool
    {
        return $data instanceof CustomItem;
    }

    public function persist($data)
    {
        \assert($data instanceof CustomItem);

        $this->customItemModel->save($data);

        return $data;
    }

    public function remove($data)
    {
        \assert($data instanceof CustomItem);

        $this->customItemModel->delete($data);
    }
}
