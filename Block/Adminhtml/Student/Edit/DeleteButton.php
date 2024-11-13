<?php

namespace CodeAesthetix\Student\Block\Adminhtml\Student\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

class DeleteButton implements ButtonProviderInterface
{
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getButtonData()
    {
        $data = [];
        if ($this->getStudentId()) {
            $data = [
                'label' => __('Delete Student'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to delete this student?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    public function getDeleteUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('*/*/delete', ['student_id' => $this->getStudentId()]);
    }

    public function getStudentId()
    {
        return $this->context->getRequest()->getParam('student_id');
    }
}
