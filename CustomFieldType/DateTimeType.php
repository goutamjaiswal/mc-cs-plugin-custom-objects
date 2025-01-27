<?php

declare(strict_types=1);

namespace MauticPlugin\CustomObjectsBundle\CustomFieldType;

use DateTimeInterface;
use MauticPlugin\CustomObjectsBundle\CustomFieldType\DataTransformer\DateTimeAtomTransformer;
use MauticPlugin\CustomObjectsBundle\CustomFieldType\DataTransformer\DateTimeTransformer;
use MauticPlugin\CustomObjectsBundle\CustomFieldType\DataTransformer\ViewDateTransformer;
use MauticPlugin\CustomObjectsBundle\Entity\CustomField;
use MauticPlugin\CustomObjectsBundle\Entity\CustomFieldValueDateTime;
use MauticPlugin\CustomObjectsBundle\Entity\CustomFieldValueInterface;
use MauticPlugin\CustomObjectsBundle\Entity\CustomItem;
use MauticPlugin\CustomObjectsBundle\Exception\InvalidValueException;
use Symfony\Component\Form\DataTransformerInterface;

class DateTimeType extends AbstractCustomFieldType
{
    use DateOperatorTrait;

    /**
     * @var string
     */
    public const NAME = 'custom.field.type.datetime';

    public const TABLE_NAME = 'custom_field_value_datetime';

    /**
     * @var string
     */
    protected $key = 'datetime';

    /**
     * {@inheritdoc}
     */
    protected $formTypeOptions = [
        'widget' => 'single_text',
        'format' => 'yyyy-MM-dd HH:mm',
        'attr'   => [
            'data-toggle' => 'datetime',
        ],
    ];

    /**
     * @param mixed|null $value
     *
     * @throws InvalidValueException
     */
    public function createValueEntity(CustomField $customField, CustomItem $customItem, $value = null): CustomFieldValueInterface
    {
        if (empty($value)) {
            $value = null;
        } elseif (is_string($value)) {
            try {
                $value = new \DateTimeImmutable($value);
            } catch (\Throwable $e) {
                throw new InvalidValueException($e->getMessage());
            }
        }

        return new CustomFieldValueDateTime($customField, $customItem, $value);
    }

    public function getSymfonyFormFieldType(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class;
    }

    public function getEntityClass(): string
    {
        return CustomFieldValueDateTime::class;
    }

    /**
     * {@inheritdoc}
     */
    public function createDefaultValueTransformer(): DataTransformerInterface
    {
        return new DateTimeTransformer();
    }

    /**
     * {@inheritdoc}
     */
    public function createApiValueTransformer(): DataTransformerInterface
    {
        return new DateTimeAtomTransformer();
    }

    /**
     * {@inheritdoc}
     */
    public function createViewTransformer(): DataTransformerInterface
    {
        return new ViewDateTransformer();
    }

    /**
     * {@inheritdoc}
     */
    public function valueToString(CustomFieldValueInterface $fieldValue): string
    {
        $value = $fieldValue->getValue();

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return (string) $value;
    }
}
