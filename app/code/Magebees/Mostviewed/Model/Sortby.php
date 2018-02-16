<?php
/***************************************************************************
 Extension Name	: Mostviewed Products 
 Extension URL	: http://www.magebees.com/most-viewed-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Mostviewed\Model;

class Sortby implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' =>'position', 'label' => __('Position')],['value' =>'price', 'label' => __('Price')], ['value' =>'name', 'label' => __('Name')]];
    }

    public function toArray()
    {
        return [0 => __('Name'), 1 => __('Price'),2=>__('Position')];
    }
}
