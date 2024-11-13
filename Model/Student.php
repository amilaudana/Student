<?php
namespace CodeAesthetix\Student\Model;

use Magento\Framework\Model\AbstractModel;
use CodeAesthetix\Student\Api\Data\StudentInterface;

class Student extends AbstractModel implements StudentInterface
{
    protected function _construct()
    {
        $this->_init('CodeAesthetix\Student\Model\ResourceModel\Student');
    }

    public function getId()
    {
        return $this->getData(self::STUDENT_ID);
    }

    public function getFirstName()
    {
        return $this->getData(self::FIRST_NAME);
    }

    public function getLastName()
    {
        return $this->getData(self::LAST_NAME);
    }

    public function getAge()
    {
        return $this->getData(self::AGE);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setFirstName($firstName)
    {
        return $this->setData(self::FIRST_NAME, $firstName);
    }

    public function setLastName($lastName)
    {
        return $this->setData(self::LAST_NAME, $lastName);
    }

    public function setAge($age)
    {
        return $this->setData(self::AGE, $age);
    }

    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
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
}
