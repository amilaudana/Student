<?php
namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Backend\App\Action;
use CodeAesthetix\Student\Api\StudentRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    protected $studentRepository;

    public function __construct(
        Action\Context $context,
        StudentRepositoryInterface $studentRepository
    ) {
        parent::__construct($context);
        $this->studentRepository = $studentRepository;
    }

    public function execute()
    {
        $studentId = (int) $this->getRequest()->getParam('student_id');
        if (!$studentId) {
            $this->messageManager->addErrorMessage(__('We can\'t find a student to delete.'));
            return $this->_redirect('*/*/');
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

        return $this->_redirect('*/*/');
    }
}
