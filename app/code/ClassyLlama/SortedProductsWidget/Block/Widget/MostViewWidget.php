<?php
/**
 * Created by PhpStorm.
 * User: HDN
 * Date: 2/11/2018
 * Time: 6:04 PM
 */


namespace ClassyLlama\SortedProductsWidget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use ClassyLlama\SortedProductsWidget\Block\MostViewProduct;

class MostViewWidget extends Template implements BlockInterface {

    protected $_template = "widget/mostviewproducts.phtml";

//    function __construct(Template\Context $context, array $data = [])
//    {
//        parent::__construct($context, $data);
////        die('asdasd');
//    }

//    protected function __beforeToHtml()
//    {
//        $productlist = "Hello meme";
//
//        $this->setData("abc", $productlist);
//        return parent::_beforeToHtml();
//    }

}