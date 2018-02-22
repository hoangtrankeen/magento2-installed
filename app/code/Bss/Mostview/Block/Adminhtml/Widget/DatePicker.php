<?php
namespace Bss\Mostview\Block\Adminhtml\Widget;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;

class DatePicker extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var  Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, Registry $coreRegistry, array $data = [],
        \Magento\Framework\Data\Form\Element\Factory $elementFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
//        $baseURL = $this->getBaseUrl();
//        $html = $element->getElementHtml();
//        $calpath = $baseURL . 'pub/media/systemcalendar/';
//        if (!$this->_coreRegistry->registry('datepicker_loaded')) {
//            $html .= '<style type="text/css">input.datepicker { background-image: url(' . $calpath . 'calendar.png) !important; background-position: calc(100% - 8px) center; background-repeat: no-repeat; } input.datepicker.disabled,input.datepicker[disabled] { pointer-events: none; }</style>';
//            $this->_coreRegistry->registry('datepicker_loaded', 1);
//        }
//        $html .= '<script type="text/javascript">
//            require(["jquery", "jquery/ui"], function () {
//                jQuery(document).ready(function () {
//                    jQuery("#' . $element->getHtmlId() . '").datepicker( { dateFormat: "dd/mm/yy" } );
//
//                    var el = document.getElementById("' . $element->getHtmlId() . '");
//                    el.className = el.className + " datepicker";
//                });
//            });
//            </script>';
//
//        $element->setData('after_element_html', $html->getElementHtml());
//        return $element;

        $input = $this->_elementFactory->create("date",  ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setFormat("dd/mm/yy");
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        $element->setData('after_element_html', $input->getElementHtml());
        return $element;
    }


}