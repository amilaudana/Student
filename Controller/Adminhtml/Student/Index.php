<?php
namespace CodeAesthetix\Student\Controller\Adminhtml\Student;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('CodeAesthetix_Student::student')
            ->getConfig()->getTitle()->prepend(__('Manage Students'));

        return $resultPage;
    }
}
