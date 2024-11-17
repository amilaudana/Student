<?php
namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use CodeAesthetix\Student\Model\StudentFactory;

/**
 * Edit Student action.
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var StudentFactory
     */
    protected $studentFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        StudentFactory $studentFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->studentFactory = $studentFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // Load layout, set active menu, and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('CodeAesthetix_Student::student')
            ->addBreadcrumb(__('Student'), __('Student'))
            ->addBreadcrumb(__('Manage Students'), __('Manage Students'));
        return $resultPage;
    }

    /**
     * Edit Student
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('student_id');
        $model = $this->_objectManager->create(\CodeAesthetix\Student\Model\Student::class);

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This student no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('student', $model);

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Students'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getFirstName() . ' ' . $model->getLastName() : __('New Student'));

        return $resultPage;
    }


    /**
     * Check if the user has permission to access this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CodeAesthetix_Student::student');
    }
}
