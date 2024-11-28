<?php

namespace CodeAesthetix\Student\Model;

use CodeAesthetix\Student\Api\Data\StudentInterface;
use CodeAesthetix\Student\Api\StudentRepositoryInterface;
use CodeAesthetix\Student\Model\ResourceModel\Student as StudentResource;
use CodeAesthetix\Student\Model\ResourceModel\Student\CollectionFactory as StudentCollectionFactory;
use CodeAesthetix\Student\Model\StudentFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Psr\Log\LoggerInterface;

class StudentRepository implements StudentRepositoryInterface
{
    protected $resource;
    protected $studentFactory;
    protected $studentCollectionFactory;
    protected $dataObjectHelper;
    protected $dataObjectProcessor;
    private $storeManager;
    private $collectionProcessor;
    private $hydrator;
    private $searchResultsFactory;
    private $logger;

    public function __construct(
        StudentResource $resource,
        StudentFactory $studentFactory,
        StudentCollectionFactory $studentCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        SearchResultsInterfaceFactory $searchResultsFactory,
        LoggerInterface $logger,
        CollectionProcessorInterface $collectionProcessor = null,
        HydratorInterface $hydrator = null
    ) {
        $this->resource = $resource;
        $this->studentFactory = $studentFactory;
        $this->studentCollectionFactory = $studentCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->logger = $logger;
        $this->collectionProcessor = $collectionProcessor ?: ObjectManager::getInstance()->get(CollectionProcessorInterface::class);
        $this->hydrator = $hydrator ?? ObjectManager::getInstance()->get(HydratorInterface::class);
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function save(StudentInterface $student): StudentInterface
    {
        if (empty($student->getStoreId())) {
            $student->setStoreId([0]);
        }

        if ($student->getId() && $student instanceof Student && !$student->getOrigData()) {
            $student = $this->hydrator->hydrate($this->getById($student->getId()), $this->hydrator->extract($student));
        }

        try {
            $this->resource->save($student);
            $this->saveStoreAssociations($student);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $student;
    }


    protected function saveStoreAssociations(StudentInterface $student)
    {
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTable('student_entity_store');

        $studentId = (int)$student->getId();
        $storeIds = (array)$student->getStoreId();

        // Delete existing associations
        $connection->delete($table, ['student_id = ?' => $studentId]);

        // Insert new store associations
        foreach ($storeIds as $storeId) {
            $connection->insert($table, [
                'student_id' => $studentId,
                'store_id' => (int)$storeId,
            ]);
        }
    }

    public function getById($studentId): ?StudentInterface
    {
        $student = $this->studentFactory->create();
        $this->resource->load($student, $studentId);
        if (!$student->getId()) {
            throw new NoSuchEntityException(__('The student with the "%1" ID does not exist.', $studentId));
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
        try {
            $student = $this->getById($studentId);
            return $this->delete($student);
        } catch (NoSuchEntityException $e) {
            // Log the message or handle the error as needed, and rethrow
            throw new CouldNotDeleteException(__('Could not delete the student: %1', $e->getMessage()));
        }
    }


    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->studentCollectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
