<?php
/***************************************************************************
 Extension Name	: Mostviewed Products 
 Extension URL	: http://www.magebees.com/most-viewed-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace  Magebees\Mostviewed\Controller\Adminhtml\Manage;

class Grid extends \Magento\Backend\App\Action
{
    public function execute()
    {
        
            $this->getResponse()->setBody(
                $this->_view->getLayout()->
                createBlock('Magebees\Mostviewed\Block\Adminhtml\Product\Grid')->toHtml()
            );
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Mostviewed::mostviewedproduct');
    }
}
