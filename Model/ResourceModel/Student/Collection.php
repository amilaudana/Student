<?php
namespace CodeAesthetix\Student\Model\ResourceModel\Student;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use CodeAesthetix\Student\Model\Student;
use CodeAesthetix\Student\Model\ResourceModel\Student as StudentResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Student::class, StudentResource::class);
    }
}
