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
    /**
     * @var StudentResource
     */
    protected $resource;

    /**
     * @var StudentFactory
     */
    protected $studentFactory;

    /**
     * @var StudentCollectionFactory
     */
    protected $studentCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var LoggerInterface
     */
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

    public function save(StudentInterface $student): StudentInterface
    {
        try {
            // Set store ID if not set
            if (empty($student->getStoreId())) {
                $student->setStoreId($this->storeManager->getStore()->getId());
            }

            // Ensure the entity has the correct ID field before saving
            if (!$student->getId() && $student->getStudentId()) {
                $student->setId($student->getStudentId());
            }

            // Check if the student already exists and rehydrate if necessary
            if ($student->getId() && !$student->getOrigData()) {
                // Get the existing student record
                $existingStudent = $this->getById($student->getId());

                // Rehydrate student model with existing data if found
                if ($existingStudent->getId()) {
                    $student = $this->hydrator->hydrate($existingStudent, $this->hydrator->extract($student));
                }
            }

            // Save student (insert or update)
            $this->resource->save($student);

        } catch (\Exception $e) {
            // Log error and throw exception for the save operation failure
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
        $student = $this->getById($studentId);
        return $this->delete($student);
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
