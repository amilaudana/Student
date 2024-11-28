<?php

declare(strict_types=1);

namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Backend\App\Action;
use CodeAesthetix\Student\Api\StudentRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultInterface;

class Delete extends Action
{
    /**
     * @var StudentRepositoryInterface
     */
    protected StudentRepositoryInterface $studentRepository;

    /**
     * @param Action\Context $context
     * @param StudentRepositoryInterface $studentRepository
     */
    public function __construct(
        Action\Context $context,
        StudentRepositoryInterface $studentRepository
    ) {
        parent::__construct($context);
        $this->studentRepository = $studentRepository;
    }

    /**
     * Execute action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $studentId = (int) $this->getRequest()->getParam('student_id');
        if (!$studentId) {
            $this->messageManager->addErrorMessage(__('We can\'t find a student to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $student = $this->studentRepository->getById($studentId);
            $this->studentRepository->delete($student);
            $this->messageManager->addSuccessMessage(__('The student has been deleted.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the student.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
