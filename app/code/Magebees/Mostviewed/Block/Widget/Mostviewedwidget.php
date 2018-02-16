<?php
/***************************************************************************
 Extension Name	: Mostviewed Products 
 Extension URL	: http://www.magebees.com/most-viewed-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Mostviewed\Block\Widget;

class Mostviewedwidget extends \Magebees\Mostviewed\Block\Mostviewed implements \Magento\Widget\Block\BlockInterface
{
    
    
    public function addData(array $arr)
    {
        
        $this->_data = array_merge($this->_data, $arr);
    }

    public function setData($key, $value = null)
    {
        
        $this->_data[$key] = $value;
    }
 
    public function _toHtml()
    {
        if ($this->getData('template')) {
            $this->setTemplate($this->getData('template'));
        }
        return parent::_toHtml();
    }
}
