<?php

namespace CodeAesthetix\Student\Api;

use CodeAesthetix\Student\Api\Data\StudentInterface;

interface StudentRepositoryInterface
{
    /**
     * Save student data
     *
     * @param StudentInterface $student
     * @return StudentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(StudentInterface $student): StudentInterface;

    /**
     * Retrieve student by ID
     *
     * @param int $studentId
     * @return StudentInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($studentId): ?StudentInterface;

    /**
     * Delete student
     *
     * @param StudentInterface $student
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(StudentInterface $student): bool;

    /**
     * Delete student by ID
     *
     * @param int $studentId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($studentId): bool;
}
