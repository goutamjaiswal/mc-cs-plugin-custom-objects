<?php

declare(strict_types=1);

namespace MauticPlugin\CustomObjectsBundle\CustomFieldType;

use DateTimeInterface;
use MauticPlugin\CustomObjectsBundle\CustomFieldType\DataTransformer\DateTransformer;
use MauticPlugin\CustomObjectsBundle\CustomFieldType\DataTransformer\ViewDateTransformer;
use MauticPlugin\CustomObjectsBundle\Entity\CustomField;
use MauticPlugin\CustomObjectsBundle\Entity\CustomFieldValueDate;
use MauticPlugin\CustomObjectsBundle\Entity\CustomFieldValueInterface;
use MauticPlugin\CustomObjectsBundle\Entity\CustomItem;
use MauticPlugin\CustomObjectsBundle\Exception\InvalidValueException;
use Symfony\Component\Form\DataTransformerInterface;

class DateType extends AbstractCustomFieldType
{
    use DateOperatorTrait;

    /**
     * @var string
     */
    public const NAME = 'custom.field.type.date';

    public const TABLE_NAME = 'custom_field_value_date';

    /**
     * @var string
     */
    protected $key = 'date';

    /**
     * {@inheritdoc}
     */
    protected $formTypeOptions = [
        'widget' => 'single_text',
        'format' => 'yyyy-MM-dd',
        'html5'  => false,
        'attr'   => [
            'data-toggle' => 'date',
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

        return new CustomFieldValueDate($customField, $customItem, $value);
    }

    public function getSymfonyFormFieldType(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\DateType::class;
    }

    public function getEntityClass(): string
    {
        return CustomFieldValueDate::class;
    }

    /**
     * {@inheritdoc}
     */
    public function createDefaultValueTransformer(): DataTransformerInterface
    {
        return new DateTransformer();
    }

    /**
     * {@inheritdoc}
     */
    public function createApiValueTransformer(): DataTransformerInterface
    {
        return new DateTransformer();
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
            return $value->format('Y-m-d');
        }

        return (string) $value;
    }
}
