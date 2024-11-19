<?php

namespace CodeAesthetix\Student\Model\Student;

use CodeAesthetix\Student\Model\ResourceModel\Student\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\RequestInterface;

/**
 * Class DataProvider
 * Data provider for student entity, used in the form UI component
 */
class DataProvider extends AbstractDataProvider
{
    protected $loadedData;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
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
                if ($student->getStoreId()) {
                    $this->loadedData[$studentId]['store_id'] = $student->getStoreId();
                }
            }
        }

        return $this->loadedData ?? [];
    }
}
