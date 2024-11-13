<?php
namespace CodeAesthetix\Student\Model;

use CodeAesthetix\Student\Api\Data\StudentInterface;
use CodeAesthetix\Student\Api\StudentRepositoryInterface;
use CodeAesthetix\Student\Model\ResourceModel\Student as StudentResource;
use CodeAesthetix\Student\Model\StudentFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class StudentRepository implements StudentRepositoryInterface
{
    protected $resource;
    protected $studentFactory;

    public function __construct(
        StudentResource $resource,
        StudentFactory $studentFactory
    ) {
        $this->resource = $resource;
        $this->studentFactory = $studentFactory;
    }

    public function save(StudentInterface $student): StudentInterface
    {
        try {
            $this->resource->save($student);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Could not save the student: %1', $e->getMessage())
            );
        }

        return $student;
    }

    public function getById($studentId): ?StudentInterface
    {
        $student = $this->studentFactory->create();
        $this->resource->load($student, $studentId);
        if (!$student->getId()) {
            throw new NoSuchEntityException(__('The student with the "%1" ID doesn\'t exist.', $studentId));
        }
        return $student;
    }

    public function delete(StudentInterface $student): bool
    {
        try {
            $this->resource->delete($student);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Could not delete the student: %1', $e->getMessage())
            );
        }
        return true;
    }

    public function deleteById($studentId): bool
    {
        $student = $this->getById($studentId);
        return $this->delete($student);
    }
}

