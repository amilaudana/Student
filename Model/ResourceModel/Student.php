<?php
namespace CodeAesthetix\Student\Model\ResourceModel;

use CodeAesthetix\Student\Api\Data\StudentInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Student resource model
 * Handles interactions with the database for the student entity.
 */
class Student extends AbstractDb
{
    protected $storeManager;
    protected $entityManager;
    protected $metadataPool;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        $this->storeManager = $storeManager;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('student_entity', 'student_id');
    }

    public function getConnection()
    {
        return $this->metadataPool->getMetadata(StudentInterface::class)->getEntityConnection();
    }

    protected function _beforeSave(AbstractModel $object)
    {
        // Add any pre-save validation logic if needed
        return $this;
    }

    public function load(AbstractModel $object, $value, $field = null)
    {
        $studentId = $this->getStudentId($object, $value, $field);
        if ($studentId) {
            $this->entityManager->load($object, $studentId);

            // Load store associations
            $storeIds = $this->lookupStoreIds($studentId);
            $object->setData('store_id', $storeIds);
        }
        return $this;
    }

    private function getStudentId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(StudentInterface::class);
        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(StudentInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), Store::DEFAULT_STORE_ID];

            $select->join(
                ['ss' => $this->getTable('student_entity_store')],
                $this->getMainTable() . '.' . $linkField . ' = ss.' . $linkField,
                ['store_id']
            )
                ->where('is_active = ?', 1)
                ->where('ss.store_id in (?)', $stores)
                ->order('store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(StudentInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['ss' => $this->getTable('student_entity_store')], 'store_id')
            ->join(
                ['se' => $this->getMainTable()],
                'ss.' . $linkField . ' = se.' . $linkField,
                []
            )
            ->where('se.' . $entityMetadata->getIdentifierField() . ' = :student_id');

        return $connection->fetchCol($select, ['student_id' => (int)$id]);
    }
}
