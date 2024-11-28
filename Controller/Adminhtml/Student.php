<?php

declare(strict_types=1);

namespace CodeAesthetix\Student\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Registry;

abstract class Student extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'CodeAesthetix_Student::student';

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Action\Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(Action\Context $context, Registry $coreRegistry)
    {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Initialize student management page
     *
     * @param Page $resultPage
     * @return Page
     */
    protected function initPage(Page $resultPage): Page
    {
        $resultPage->setActiveMenu('CodeAesthetix_Student::student')
            ->addBreadcrumb(__('Student'), __('Student'))
            ->addBreadcrumb(__('Manage Students'), __('Manage Students'));
        return $resultPage;
    }
}
