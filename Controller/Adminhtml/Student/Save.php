<?php
namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Backend\App\Action;
use CodeAesthetix\Student\Api\StudentRepositoryInterface;
use CodeAesthetix\Student\Model\StudentFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResourceConnection;

class Save extends Action
{
    protected $studentRepository;
    protected $studentFactory;
    protected $dataPersistor;
    protected $resource;

    public function __construct(
        Action\Context $context,
        StudentRepositoryInterface $studentRepository,
        StudentFactory $studentFactory,
        DataPersistorInterface $dataPersistor,
        ResourceConnection $resource
    ) {
        parent::__construct($context);
        $this->studentRepository = $studentRepository;
        $this->studentFactory = $studentFactory;
        $this->dataPersistor = $dataPersistor;
        $this->resource = $resource;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                $data['is_active'] = isset($data['is_active'])
                    ? (int)filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN)
                    : 0;

                if (isset($data['store_id']) && !is_array($data['store_id'])) {
                    $data['store_id'] = explode(',', $data['store_id']);
                }

                $studentId = $data['student_id'] ?? null;
                $student = $studentId
                    ? $this->studentRepository->getById($studentId)
                    : $this->studentFactory->create();

                $student->setData($data);
                $this->studentRepository->save($student);

                // Save the store associations manually
                if (isset($data['store_id'])) {
                    $this->_saveStoreAssociations($student->getId(), $data['store_id']);
                }

                $this->messageManager->addSuccessMessage(__('The student has been saved.'));
                $this->dataPersistor->clear('student');

                if (isset($data['back']) && $data['back'] === 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['student_id' => $student->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the student.'));
            }

            $this->dataPersistor->set('student', $data);

            if (isset($studentId)) {
                return $resultRedirect->setPath('*/*/edit', ['student_id' => $studentId]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    protected function _saveStoreAssociations($studentId, array $storeIds)
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('student_entity_store');

        // Insert new associations without deleting previous records
        foreach ($storeIds as $storeId) {
            $connection->insertOnDuplicate(
                $tableName,
                ['student_id' => (int)$studentId, 'store_id' => (int)$storeId]
            );
        }
    }
}
