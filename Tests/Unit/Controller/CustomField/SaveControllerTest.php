<?php

/*
 * @copyright   2019 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CustomObjectsBundle\Tests\Unit\Controller\CustomField;

use Doctrine\Common\Collections\ArrayCollection;
use MauticPlugin\CustomObjectsBundle\Controller\CustomField\FormController;
use MauticPlugin\CustomObjectsBundle\Controller\CustomField\SaveController;
use MauticPlugin\CustomObjectsBundle\CustomFieldType\SelectType;
use MauticPlugin\CustomObjectsBundle\Entity\CustomField;
use MauticPlugin\CustomObjectsBundle\Entity\CustomFieldFactory;
use MauticPlugin\CustomObjectsBundle\Entity\CustomFieldOption;
use MauticPlugin\CustomObjectsBundle\Entity\CustomObject;
use MauticPlugin\CustomObjectsBundle\Form\Type\CustomFieldType;
use MauticPlugin\CustomObjectsBundle\Form\Type\CustomObjectType;
use MauticPlugin\CustomObjectsBundle\Model\CustomFieldModel;
use MauticPlugin\CustomObjectsBundle\Model\CustomObjectModel;
use MauticPlugin\CustomObjectsBundle\Provider\CustomFieldPermissionProvider;
use MauticPlugin\CustomObjectsBundle\Provider\CustomFieldRouteProvider;
use MauticPlugin\CustomObjectsBundle\Tests\Unit\Controller\ControllerTestCase;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class SaveControllerTest extends ControllerTestCase
{
    private $formFactory;
    private $translator;
    private $customFieldModel;
    private $customFieldFactory;
    private $permissionProvider;
    private $fieldRouteProvider;
    private $customObjectModel;
    private $form;
    private $saveController;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formFactory = $this->createMock(FormFactory::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->customFieldModel = $this->createMock(CustomFieldModel::class);
        $this->customFieldFactory = $this->createMock(CustomFieldFactory::class);
        $this->permissionProvider = $this->createMock(CustomFieldPermissionProvider::class);
        $this->fieldRouteProvider = $this->createMock(CustomFieldRouteProvider::class);
        $this->customObjectModel = $this->createMock(CustomObjectModel::class);
        $this->form = $this->createMock(FormInterface::class);

        $this->saveController = new SaveController(
            $this->formFactory,
            $this->translator,
            $this->customFieldModel,
            $this->customFieldFactory,
            $this->permissionProvider,
            $this->fieldRouteProvider,
            $this->customObjectModel        );

        $this->addSymfonyDependencies($this->saveController);
    }

    public function testSaveActionEdit()
    {
        $objectId   = 1;
        $fieldId    = 2;
        $fieldType  = 'text';
        $panelId    = null;
        $panelCount = null;

        $customObject = $this->createMock(CustomObject::class);
        $customObject->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($objectId);

        $customField = $this->createMock(CustomField::class);
        $customField->setId($fieldId);

        $request = $this->createMock(Request::class);
        $request->expects($this->at(0))
            ->method('get')
            ->with('objectId')
            ->willReturn($objectId);
        $request->expects($this->at(1))
            ->method('get')
            ->with('fieldId')
            ->willReturn($fieldId);
        $request->expects($this->at(2))
            ->method('get')
            ->with('fieldType')
            ->willReturn($fieldType);
        $request->expects($this->at(3))
            ->method('get')
            ->with('panelId')
            ->willReturn($panelId);
        $request->expects($this->at(4))
            ->method('get')
            ->with('panelCount')
            ->willReturn($panelCount);
        $request->expects($this->at(5))
            ->method('get')
            ->with('custom_field')
            ->willReturn([]);
        $request->expects($this->at(5))
            ->method('get')
            ->with('custom_field')
            ->willReturn([]);

        $this->customObjectModel->expects($this->once())
            ->method('fetchEntity')
            ->with($objectId)
            ->willReturn($customObject);

        $this->customFieldModel->expects($this->once())
            ->method('fetchEntity')
            ->with($fieldId)
            ->willReturn($customField);

        $this->permissionProvider->expects($this->once())
            ->method('canEdit')
            ->with($customField);

        $action = 'action';
        $this->fieldRouteProvider->expects($this->once())
            ->method('buildSaveRoute')
            ->with($fieldType, $fieldId, $customObject->getId(), $panelCount, $panelId)
            ->willReturn($action);

        $this->formFactory->expects($this->at(0))
            ->method('create')
            ->with(CustomFieldType::class, $customField, ['action' => $action])
            ->willReturn($this->form);

        $this->form->expects($this->once())
            ->method('handleRequest')
            ->with($request);

        $this->form->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $this->customFieldModel->expects($this->once())
            ->method('setAlias')
            ->with($customField);

        $this->form->expects($this->once())
            ->method('getData')
            ->willReturn($customField);

        $customField->expects($this->once())
            ->method('getOptions')
            ->willReturn(new ArrayCollection());

        $this->customFieldModel->expects($this->once())
            ->method('setAlias');

        $customObject->expects($this->once())
            ->method('setCustomFields');

        $customObject = $this->createMock(CustomObject::class);
        $this->formFactory->expects($this->at(1))
            ->method('create')
            ->with(CustomObjectType::class, $customObject)
            ->willReturn($this->form);

        $this->saveController->saveAction($request);
    }

    public function testSaveActionCreate()
    {

    }
}
