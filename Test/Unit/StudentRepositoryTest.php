<?php

namespace CodeAesthetix\Student\Test\Unit;

use CodeAesthetix\Student\Api\Data\StudentInterface;
use CodeAesthetix\Student\Model\ResourceModel\Student as StudentResource;
use CodeAesthetix\Student\Model\Student;
use CodeAesthetix\Student\Model\StudentFactory;
use CodeAesthetix\Student\Model\StudentRepository;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class StudentRepositoryTest extends TestCase
{
    private $studentRepository;
    private $resourceMock;
    private $studentFactoryMock;
    private $storeManagerMock;
    private $hydratorMock;
    private $collectionProcessorMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resourceMock = $this->createMock(StudentResource::class);
        $this->studentFactoryMock = $this->createMock(StudentFactory::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->hydratorMock = $this->createMock(HydratorInterface::class);
        $this->collectionProcessorMock = $this->createMock(CollectionProcessorInterface::class);

        $this->studentRepository = $objectManager->getObject(
            StudentRepository::class,
            [
                'resource' => $this->resourceMock,
                'studentFactory' => $this->studentFactoryMock,
                'storeManager' => $this->storeManagerMock,
                'hydrator' => $this->hydratorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
            ]
        );
    }

    public function testSaveStudentThrowsException()
    {
        // Expecting a CouldNotSaveException to be thrown
        $this->expectException(CouldNotSaveException::class);

        $studentMock = $this->getMockBuilder(Student::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mocking resource save to throw an exception
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception('Some save error'));

        // Attempt to save the student, which should throw the exception
        $this->studentRepository->save($studentMock);
    }

    public function testSaveStudentSuccess()
    {
        $studentMock = $this->getMockBuilder(Student::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId', 'setStoreId', 'getStoreId', 'setId', 'getOrigData'])
            ->getMock();

        // Mocking the store ID behavior to accommodate multiple calls
        $studentMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn([]);

        $studentMock->expects($this->once())
            ->method('setStoreId')
            ->with([0]);

        // Mock behavior to simulate saving a student
        $studentMock->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $studentMock->expects($this->once())
            ->method('setId')
            ->with(1);

        $studentMock->expects($this->any())
            ->method('getOrigData')
            ->willReturn(null);

        // Update the property name from $studentFactory to $studentFactoryMock
        $this->studentFactoryMock->method('create')->willReturn($studentMock);

        // Mocking the resource save method to simulate setting an ID after saving
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($studentMock)
            ->willReturnCallback(function ($student) {
                $student->setId(1); // Simulate that the student ID is set after save
            });

        // Mocking the getConnection() and delete() methods
        $connectionMock = $this->getMockBuilder(\Magento\Framework\DB\Adapter\AdapterInterface::class)
            ->getMock();

        $this->resourceMock->expects($this->once())
            ->method('getConnection')
            ->willReturn($connectionMock);

        $connectionMock->expects($this->once())
            ->method('delete')
            ->with($this->anything());

        // Use $hydratorMock instead of $hydrator
        $this->hydratorMock->expects($this->once())
            ->method('extract')
            ->with($studentMock)
            ->willReturn(['some_data' => 'value']);

        $this->hydratorMock->expects($this->once())
            ->method('hydrate')
            ->with($this->isInstanceOf(Student::class), $this->isType('array'))
            ->willReturn($studentMock);

        // Attempt to save the student
        $result = $this->studentRepository->save($studentMock);

        // Assert that the returned student is the same as the mocked one
        $this->assertSame($studentMock, $result);
    }

    public function testGetByIdThrowsException()
    {
        $this->expectException(NoSuchEntityException::class);

        $studentMock = $this->getMockBuilder(Student::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();

        $studentMock->expects($this->once())->method('getId')->willReturn(null);

        $this->studentFactoryMock->method('create')->willReturn($studentMock);
        $this->resourceMock->expects($this->once())->method('load')->with($studentMock, 1);

        // Attempting to get a student by ID, expecting an exception
        $this->studentRepository->getById(1);
    }

    public function testDeleteStudentThrowsException()
    {
        $this->expectException(CouldNotDeleteException::class);

        $studentMock = $this->getMockBuilder(Student::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->willThrowException(new \Exception('Some delete error'));

        // Attempt to delete, expecting a CouldNotDeleteException
        $this->studentRepository->delete($studentMock);
    }

    public function testDeleteByIdThrowsException()
    {
        $this->expectException(CouldNotDeleteException::class);
        $this->expectExceptionMessage('Could not delete the student: The student with the "999" ID does not exist.');

        $studentMock = $this->getMockBuilder(Student::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();

        $studentMock->expects($this->once())->method('getId')->willReturn(null);

        $this->studentFactoryMock->method('create')->willReturn($studentMock);
        $this->resourceMock->expects($this->once())->method('load')->with($studentMock, 999);

        // Attempting to delete by ID should throw CouldNotDeleteException
        $this->studentRepository->deleteById(999);
    }
}
