<?php

declare(strict_types=1);

/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use CodeAesthetix\Student\Model\ResourceModel\Student\CollectionFactory;
use CodeAesthetix\Student\Api\StudentRepositoryInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'CodeAesthetix_Student::student';

    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var StudentRepositoryInterface
     */
    protected StudentRepositoryInterface $studentRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param StudentRepositoryInterface $studentRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        StudentRepositoryInterface $studentRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->studentRepository = $studentRepository;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|\Exception
     */
    public function execute(): Redirect
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $student) {
            try {
                // Load student by ID before deleting to ensure it's fully loaded
                $studentEntity = $this->studentRepository->getById((int)$student->getId());

                if ($studentEntity->getId()) {
                    $this->studentRepository->delete($studentEntity);
                } else {
                    throw new \Exception((string)__('Student entity with ID %1 not found.', $student->getId()));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Error deleting student with ID %1: %2', $student->getId(), $e->getMessage())
                );
            }
        }

        if ($collectionSize > 0) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
