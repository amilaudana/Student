<?php
namespace CodeAesthetix\Student\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Student extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('student_entity', 'student_id');
    }
}
