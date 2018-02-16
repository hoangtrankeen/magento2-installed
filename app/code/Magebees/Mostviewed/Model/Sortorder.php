<?php
/***************************************************************************
 Extension Name	: Mostviewed Products 
 Extension URL	: http://www.magebees.com/most-viewed-products-extension-for-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
namespace Magebees\Mostviewed\Model;

class Sortorder implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' =>'asc', 'label' => __('Ascending')],['value' =>'desc', 'label' => __('Descending')]];
    }

    public function toArray()
    {
        return [ 1 => __('Descending'),2=>__('Ascending')];
    }
}
