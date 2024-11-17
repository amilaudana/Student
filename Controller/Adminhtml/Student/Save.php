<?php
namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Backend\App\Action;
use CodeAesthetix\Student\Api\StudentRepositoryInterface;
use CodeAesthetix\Student\Model\StudentFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Request\DataPersistorInterface;

class Save extends Action
{
    protected $studentRepository;
    protected $studentFactory;
    protected $dataPersistor;

    public function __construct(
        Action\Context $context,
        StudentRepositoryInterface $studentRepository,
        StudentFactory $studentFactory,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->studentRepository = $studentRepository;
        $this->studentFactory = $studentFactory;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                // Handle the case of enabling/disabling a record
                if (isset($data['is_active']) && $data['is_active'] === 'true') {
                    $data['is_active'] = 1;
                } elseif (isset($data['is_active']) && $data['is_active'] === 'false') {
                    $data['is_active'] = 0;
                }

                // Determine if we are working with a new or existing student
                $studentId = isset($data['student_id']) ? $data['student_id'] : null;
                $student = $studentId ? $this->studentRepository->getById($studentId) : $this->studentFactory->create();

                $student->setData($data);

                // Save the student data
                $this->studentRepository->save($student);

                // Add success message and clear the persisted data
                $this->messageManager->addSuccessMessage(__('The student has been saved.'));
                $this->dataPersistor->clear('student');

                // Redirect to the edit page if 'back' is set in the request data
                if (isset($data['back']) && $data['back'] === 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['student_id' => $student->getId()]);
                }

                // Otherwise, redirect to the index page
                return $resultRedirect->setPath('*/*/');

            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the student.'));
            }

            // Persist data in case of error
            $this->dataPersistor->set('student', $data);

            // Redirect back to the edit form with the student ID if we had an error
            if (isset($studentId)) {
                return $resultRedirect->setPath('*/*/edit', ['student_id' => $studentId]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
