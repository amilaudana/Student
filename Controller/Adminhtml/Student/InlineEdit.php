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

class InlineEdit extends \Magento\Backend\App\Action
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
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $studentId) {
                    /** @var \CodeAesthetix\Student\Model\Student $student */
                    $student = $this->studentRepository->getById($studentId);
                    try {
                        $student->setData(array_merge($student->getData(), $postItems[$studentId]));
                        $this->studentRepository->save($student);
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithStudentId(
                            $student,
                            __($e->getMessage())
                        );
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
     * @param StudentInterface $student
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithStudentId(StudentInterface $student, $errorText)
    {
        return '[Student ID: ' . $student->getId() . '] ' . $errorText;
    }
}
