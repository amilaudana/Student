<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Backend\App\Action\Context;
use CodeAesthetix\Student\Api\StudentRepositoryInterface as StudentRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use CodeAesthetix\Student\Api\Data\StudentInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

class InlineEdit extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'CodeAesthetix_Student::student';

    /**
     * @var \CodeAesthetix\Student\Api\StudentRepositoryInterface
     */
    protected $studentRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param StudentRepository $studentRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        StudentRepository $studentRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->studentRepository = $studentRepository;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Execute action for inline editing
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        // Verify if the request is an AJAX request
        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $studentId) {
                    try {
                        // Load student entity by ID
                        $studentEntity = $this->studentRepository->getById($studentId);

                        $studentEntity->setData(array_merge($studentEntity->getData(), $postItems[$studentId]));

                        // Save the updated student entity
                        $this->studentRepository->save($studentEntity);
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithStudentId($studentId, $e->getMessage());
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add student ID to error message
     *
     * @param int $studentId
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithStudentId($studentId, $errorText)
    {
        return '[Student ID: ' . $studentId . '] ' . $errorText;
    }
}
