<?php
namespace Bss\Mostview\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Bss\Mostview\Block\Mostviewproduct;

class Productwidget extends Mostviewproduct implements BlockInterface {

    protected $_template = "widget/mostview.phtml";

}