<?php
namespace CodeAesthetix\Student\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Registry;

abstract class Student extends Action
{
    const ADMIN_RESOURCE = 'CodeAesthetix_Student::student';

    protected $_coreRegistry;

    public function __construct(Action\Context $context, Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    protected function initPage(Page $resultPage)
    {
        $resultPage->setActiveMenu('CodeAesthetix_Student::student')
            ->addBreadcrumb(__('Student'), __('Student'))
            ->addBreadcrumb(__('Manage Students'), __('Manage Students'));
        return $resultPage;
    }
}
