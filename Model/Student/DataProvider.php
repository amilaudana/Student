<?php

namespace CodeAesthetix\Student\Model\Student;

use CodeAesthetix\Student\Model\ResourceModel\Student\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class DataProvider
 * Data provider for the student entity, used in the form UI component.
 */
class DataProvider extends AbstractDataProvider
{
    protected $loadedData;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data from collection to be provided to the form
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $studentId = $this->request->getParam('student_id');
        if ($studentId) {
            $student = $this->collection->getItemById($studentId);
            if ($student) {
                $this->loadedData[$studentId] = $student->getData();

                // Load store association if available
                $storeIds = $student->getResource()->lookupStoreIds($student->getId());
                $this->loadedData[$studentId]['store_id'] = $storeIds;

                // Ensure `is_active` field is properly set in the loaded data
                $this->loadedData[$studentId]['is_active'] = $student->getData('is_active');
            }
        }

        // Handle persisted data if available
        $data = $this->dataPersistor->get('student');
        if (!empty($data)) {
            $student = $this->collection->getNewEmptyItem();
            $student->setData($data);
            $this->loadedData[$student->getId()] = $student->getData();
            $this->dataPersistor->clear('student');
        }

        return $this->loadedData ?? [];
    }
}
