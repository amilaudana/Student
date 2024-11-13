<?php
namespace CodeAesthetix\Student\Api\Data;

interface StudentInterface
{
    const STUDENT_ID = 'student_id';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const AGE = 'age';
    const DESCRIPTION = 'description';
    const CREATED_AT = 'created_at';

    /**
     * Get Student ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Student ID
     *
     * @param int $studentId
     * @return $this
     */
    public function setId($studentId);

    /**
     * Get First Name
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set First Name
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName);

    /**
     * Get Last Name
     *
     * @return string
     */
    public function getLastName();

    /**
     * Set Last Name
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName);

    /**
     * Get Age
     *
     * @return int
     */
    public function getAge();

    /**
     * Set Age
     *
     * @param int $age
     * @return $this
     */
    public function setAge($age);

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set Created At
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
