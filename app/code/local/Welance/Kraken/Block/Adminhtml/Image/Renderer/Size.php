<?php

class Welance_Kraken_Block_Adminhtml_Image_Renderer_Size extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $size = $row->getData($this->getColumn()->getIndex());

        return Mage::helper('welance_kraken')->getImageSizeConverted($size);
    }
}