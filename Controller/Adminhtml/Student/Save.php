<?php

declare(strict_types=1);

namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use CodeAesthetix\Student\Api\StudentRepositoryInterface;
use CodeAesthetix\Student\Model\StudentFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\App\Action;

/**
 * Save Student action.
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var StudentFactory
     */
    private StudentFactory $studentFactory;

    /**
     * @var StudentRepositoryInterface
     */
    private StudentRepositoryInterface $studentRepository;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param StudentFactory $studentFactory
     * @param StudentRepositoryInterface $studentRepository
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        StudentFactory $studentFactory,
        StudentRepositoryInterface $studentRepository
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->studentFactory = $studentFactory;
        $this->studentRepository = $studentRepository;
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            if (empty($data['student_id'])) {
                $data['student_id'] = null;
            }

            /** @var \CodeAesthetix\Student\Model\Student $student */
            $student = $this->studentFactory->create();

            $id = (int) $this->getRequest()->getParam('student_id');
            if ($id) {
                try {
                    $student = $this->studentRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This student no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $student->setData($data);

            try {
                $this->studentRepository->save($student);
                $this->messageManager->addSuccessMessage(__('You saved the student.'));
                $this->dataPersistor->clear('student');
                return $this->processStudentReturn($student, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the student.'));
            }

            $this->dataPersistor->set('student', $data);
            return $resultRedirect->setPath('*/*/edit', ['student_id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process and set the student return
     *
     * @param \CodeAesthetix\Student\Model\Student $student
     * @param array $data
     * @param ResultInterface $resultRedirect
     * @return ResultInterface
     */
    private function processStudentReturn(\CodeAesthetix\Student\Model\Student $student, array $data, ResultInterface $resultRedirect): ResultInterface
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect === 'continue') {
            $resultRedirect->setPath('*/*/edit', ['student_id' => $student->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } elseif ($redirect === 'duplicate') {
            $duplicateStudent = $this->studentFactory->create(['data' => $data]);
            $duplicateStudent->setId(null);
            $duplicateStudent->setIdentifier($data['identifier'] . '-' . uniqid());
            $duplicateStudent->setIsActive(0);
            $this->studentRepository->save($duplicateStudent);
            $id = $duplicateStudent->getId();
            $this->messageManager->addSuccessMessage(__('You duplicated the student.'));
            $this->dataPersistor->set('student', $data);
            $resultRedirect->setPath('*/*/edit', ['student_id' => $id]);
        }

        return $resultRedirect;
    }
}
