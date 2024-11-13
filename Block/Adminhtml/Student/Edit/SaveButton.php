<?php

namespace CodeAesthetix\Student\Block\Adminhtml\Student\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

class SaveButton implements ButtonProviderInterface
{
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getButtonData()
    {
        return [
            'label' => __('Save Student'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
