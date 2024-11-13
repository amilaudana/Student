<?php
namespace CodeAesthetix\Student\Ui\Component;

use Magento\Ui\DataProvider\AbstractDataProvider;
use CodeAesthetix\Student\Model\ResourceModel\Student\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}
