<?php
namespace CodeAesthetix\Student\Block\Adminhtml\Student;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Store\Model\StoreManagerInterface;
use CodeAesthetix\Student\Model\StudentFactory;
use Magento\Cms\Model\Template\FilterProvider;
 

class Student extends AbstractBlock implements IdentityInterface
{
    /**
     * Prefix for cache key of Student block
     */
    const CACHE_KEY_PREFIX = 'STUDENT_BLOCK_';

    /**
     * @var FilterProvider
     */
    protected $_filterProvider;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Student factory
     *
     * @var StudentFactory
     */
    protected $_studentFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param FilterProvider $filterProvider
     * @param StoreManagerInterface $storeManager
     * @param StudentFactory $studentFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        FilterProvider $filterProvider,
        StoreManagerInterface $storeManager,
        StudentFactory $studentFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_studentFactory = $studentFactory;
    }

    /**
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $studentId = $this->getStudentId();
        $html = '';
        if ($studentId) {
            $storeId = $this->_storeManager->getStore()->getId();
            /** @var \CodeAesthetix\Student\Model\Student $student */
            $student = $this->_studentFactory->create();
            $student->setStoreId($storeId)->load($studentId);
            if ($student->isActive()) {
                $html = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($student->getContent());
            }
        }
        return $html;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [StudentFactory::CACHE_TAG . '_' . $this->getStudentId()];
    }

    /**
     * @inheritdoc
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        $cacheKeyInfo[] = $this->_storeManager->getStore()->getId();
        return $cacheKeyInfo;
    }
}
