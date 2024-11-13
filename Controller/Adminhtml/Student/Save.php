<?php
namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Backend\App\Action;
use CodeAesthetix\Student\Api\StudentRepositoryInterface;
use CodeAesthetix\Student\Model\StudentFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    protected $studentRepository;
    protected $studentFactory;

    public function __construct(
        Action\Context $context,
        StudentRepositoryInterface $studentRepository,
        StudentFactory $studentFactory
    ) {
        parent::__construct($context);
        $this->studentRepository = $studentRepository;
        $this->studentFactory = $studentFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $this->_redirect('*/*/');
        }

        try {
            $studentId = isset($data['student_id']) ? $data['student_id'] : null;
            $student = $studentId ? $this->studentRepository->getById($studentId) : $this->studentFactory->create();
            $student->setData($data);
            $this->studentRepository->save($student);
            $this->messageManager->addSuccessMessage(__('The student has been saved.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the student.'));
        }

        return $this->_redirect('*/*/');
    }
}
