<?php
/***************************************************************************
 Extension Name	: Mostviewed Products 
 Extension URL	: http://www.magebees.com/most-viewed-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Bss\Mostview\Model;

class Showproduct implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Display Category Wise')],['value' => 2, 'label' => __('Display All Products')]];
    }

    public function toArray()
    {
        return [2 => __('Display All Products'),1=>__('Display Category Wise')];
    }
}
