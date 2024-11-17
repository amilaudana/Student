<?php
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
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StudentRepositoryInterface
     */
    protected $studentRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        StudentRepositoryInterface $studentRepository
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->studentRepository = $studentRepository;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $student) {
            try {
                // Load student by ID before deleting to ensure it's fully loaded
                $studentEntity = $this->studentRepository->getById($student->getId());
                if ($studentEntity->getId()) {
                    $this->studentRepository->delete($studentEntity);
                } else {
                    throw new \Exception(__('Student entity with ID %1 not found.', $student->getId()));
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

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }



}
