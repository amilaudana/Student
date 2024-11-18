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

    /**
     * Execute the save action.
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                // Normalize `is_active` field
                $data['is_active'] = isset($data['is_active'])
                    ? (int)filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN)
                    : 0;

                // Normalize store IDs if present
                if (isset($data['store_id']) && !is_array($data['store_id'])) {
                    $data['store_id'] = explode(',', $data['store_id']);
                }

                // Load existing student or create a new one
                $studentId = $data['student_id'] ?? null;
                $student = $studentId
                    ? $this->studentRepository->getById($studentId)
                    : $this->studentFactory->create();

                $student->setData($data);

                // Save the student
                $this->studentRepository->save($student);

                // Success message and clear persisted data
                $this->messageManager->addSuccessMessage(__('The student has been saved.'));
                $this->dataPersistor->clear('student');

                // Redirect based on the 'back' parameter
                if (isset($data['back']) && $data['back'] === 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['student_id' => $student->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the student.'));
            }

            // Persist data in case of error
            $this->dataPersistor->set('student', $data);

            // Redirect to edit if an error occurred
            if (isset($studentId)) {
                return $resultRedirect->setPath('*/*/edit', ['student_id' => $studentId]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
