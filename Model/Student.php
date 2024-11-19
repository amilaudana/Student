<?php

namespace CodeAesthetix\Student\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use CodeAesthetix\Student\Api\Data\StudentInterface;

class Student extends AbstractExtensibleModel implements StudentInterface
{
    protected function _construct()
    {
        $this->_init(\CodeAesthetix\Student\Model\ResourceModel\Student::class);
    }

    public function getStudentId()
    {
        return $this->getData(self::STUDENT_ID);
    }

    public function setStudentId($studentId)
    {
        return $this->setData(self::STUDENT_ID, $studentId);
    }

    public function getFirstName()
    {
        return $this->getData(self::FIRST_NAME);
    }

    public function setFirstName($firstName)
    {
        return $this->setData(self::FIRST_NAME, $firstName);
    }

    public function getLastName()
    {
        return $this->getData(self::LAST_NAME);
    }

    public function setLastName($lastName)
    {
        return $this->setData(self::LAST_NAME, $lastName);
    }

    public function getAge()
    {
        return $this->getData(self::AGE);
    }

    public function setAge($age)
    {
        return $this->setData(self::AGE, $age);
    }

    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }
}
