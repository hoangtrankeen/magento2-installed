<?php

class Bss_Mostview_Block_Adminhtml_Widget_Date extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
    }

    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        //var_dump($this->getSkinUrl('images/grid-cal.gif')); exit;
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => '',
            'method'    => 'post'
        ));
        $element2 = new Varien_Data_Form_Element_Date(
            array(
                'name' => 'date',
                'label' => Mage::helper('module')->__('Date'),
                'after_element_html' => '<small>Click icon to select</small>',
                'tabindex' => 1,
                'type'  => 'datetime',
                'time'=>true,
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'value' => date(
                    'yyyy-MM-dd HH:mm:ss',
                    strtotime('next weekday')
                )
            )
        );

        $element2->setForm($form);
        $id = 'date'.$element->getHtmlId();
        $element2->setId($id);
        $html = $element2->getElementHtml();
        $html .= "
        <style>
        .calendar{z-index:999999;}
        #$id{display:none}
        </style>
        <script>     
        var element = jQuery('#".$element->getHtmlId()."');
        element.attr('style', 'width: 150px !important');
        jQuery('body').on('change', '#$id', function(e) {
            element.val(jQuery(this).val())
        });          
        jQuery('#{$id}_trig').on('click', function() {                                       
          setTimeout(function(){                            
          jQuery('.calendar').css('top',(parseInt(element.offset().top)-50)+'px')
          jQuery('.calendar').css('left',(parseInt(element.offset().left)-150)+'px')      
          }, 100);   
        });    
        </script>";

        $element->setData('after_element_html', $html);
        return $element;
    }
}