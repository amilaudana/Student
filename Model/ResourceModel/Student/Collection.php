<?php
namespace CodeAesthetix\Student\Model\ResourceModel\Student;

use CodeAesthetix\Student\Api\Data\StudentInterface;
use CodeAesthetix\Student\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'student_id';
    protected $_eventPrefix = 'student_collection';
    protected $_eventObject = 'student_collection';

    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(StudentInterface::class);

        $this->performAfterLoad('student_entity', $entityMetadata->getLinkField());

        return parent::_afterLoad();
    }

    protected function _construct()
    {
        $this->_init(\CodeAesthetix\Student\Model\Student::class, \CodeAesthetix\Student\Model\ResourceModel\Student::class);
        $this->_map['fields']['store'] = 'student_entity.store_id';
        $this->_map['fields']['student_id'] = 'main_table.student_id';
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('student_id', 'first_name');
    }

    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);
        return $this;
    }

    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(StudentInterface::class);
        $this->joinStoreRelationTable('student_entity', $entityMetadata->getLinkField());
    }
}
