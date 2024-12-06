# <div align="center">Students Module</div>
<div align="center" dir="auto">
<img alt="GitHub License" src="https://img.shields.io/github/license/amilaudana/student"> 
<img src="https://img.shields.io/badge/magento-2-blue.svg?logo=magento&longCache=true" alt="Magento 2 Supported Version">
</div>

## <div align="center">CodeAesthetix Students Module</div>  
<div align="center" dir="auto">
<img src="https://img.shields.io/badge/student-1.0.0-blue.svg?logo=magento&longCache=false&style=for-the-badge" alt="Magento 2 Supported Version">
</div>

### Overview
The CodeAesthetix Student module is a custom Magento 2 module designed to manage student records within the Magento Admin Panel. It provides administrators with tools to add, update, delete, and associate students with specific stores, leveraging Magento 2's best practices such as Service Contracts, UI Components, and Dependency Injection.

### Features
Admin UI for managing student data (Add, Edit, Delete).
Store association for students to handle multi-store environments.
Logging and validation for secure data handling.
Grid-based student management with filtering and sorting options.
Modular architecture following Magento 2 coding standards.

### Requirements
Magento Version: Magento 2.4.x

PHP Version: PHP 8.x

### Installation
Clone the Repository:
Clone the repository into the app/code/CodeAesthetix/Student directory:

```
git clone https://github.com/amilaudana/Student.git app/code/CodeAesthetix/Student
```
Enable the Module:
Run the following Magento commands to enable the module:

```
php bin/magento module:enable CodeAesthetix_Student
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```
Flush Cache:
Clear the Magento cache to apply changes:

```
php bin/magento cache:flush
```

### Usage
#### Admin Form
The Student module provides an admin form for adding or editing student records:

Go to Student > Manage Students in the Magento Admin Panel.

From here, you can:

Add new students with fields such as First Name, Last Name, Age, and Description.
Associate students with specific stores.
Manage Students Grid
The module includes a grid for managing student records:

View all students, filter by specific criteria, and sort columns.
Perform inline editing for quick updates.

### Key Components
#### Service Contracts
The module uses Magento 2 service contracts to handle student data operations.

CodeAesthetix\Student\Api\Data\StudentInterface: Interface defining the student entity.

CodeAesthetix\Student\Api\StudentRepositoryInterface: Interface for CRUD operations on student data.

#### UI Components
The admin panel leverages Magento 2’s UI Components:

ui_component/student_form.xml: Defines the admin form layout and fields.
ui_component/student_listing.xml: Defines the student management grid.

#### Resource Models
The module uses resource models to handle database operations:

CodeAesthetix\Student\Model\ResourceModel\Student: Resource model for the student_entity table.
CodeAesthetix\Student\Model\ResourceModel\Student\Collection: Handles collections of student entities.

#### Logging
The module includes logging for operations like creating, updating, and deleting students, ensuring secure data handling and easy debugging.

### Testing
Unit Tests
The module includes unit tests for key components such as repositories and models.

Run the tests using PHPUnit:

```
vendor/bin/phpunit app/code/CodeAesthetix/Student/Test/Unit
```
### Troubleshooting
If you encounter any issues:

Ensure the module is enabled:

```
php bin/magento module:status
```

Check logs in the var/log directory for detailed error messages:

var/log/system.log
var/log/exception.log
Verify database integrity:
Ensure all foreign key constraints are satisfied, and the schema matches the module requirements.

### License
This module is licensed under the MIT License.
