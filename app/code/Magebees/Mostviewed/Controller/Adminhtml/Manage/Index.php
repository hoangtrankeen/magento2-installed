<?php
/***************************************************************************
 Extension Name	: Mostviewed Products 
 Extension URL	: http://www.magebees.com/most-viewed-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Mostviewed\Controller\Adminhtml\Manage;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magebees_Mostviewed::grid');
        $resultPage->getConfig()->getTitle()->prepend(__('MageBees Mostviewed Products'));

       
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
         $store=(int)$this->getRequest()->getParam('store', 0);
         $session = $this->_objectManager->get('Magento\Customer\Model\Session');
         $session->setTestKey($store);
         return $resultPage;
    }
    
    protected function _isAllowed()
    {
        return true;
    }
}
