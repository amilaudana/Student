<?php
namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use CodeAesthetix\Student\Model\StudentFactory;

class Edit extends \CodeAesthetix\Student\Controller\Adminhtml\Student implements HttpGetActionInterface
{
    protected $resultPageFactory;
    protected $studentFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        StudentFactory $studentFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->studentFactory = $studentFactory;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('student_id');
        $model = $this->studentFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This student no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('student', $model);

        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Student') : __('New Student'),
            $id ? __('Edit Student') : __('New Student')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Students'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getFirstName() . ' ' . $model->getLastName() : __('New Student')
        );

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CodeAesthetix_Student::student');
    }
}
