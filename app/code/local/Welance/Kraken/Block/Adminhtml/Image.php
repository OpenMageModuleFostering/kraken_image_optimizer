<?php

class Welance_Kraken_Block_Adminhtml_Image extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('kraken/image.phtml');
    }

    protected function _prepareLayout()
    {
        $helper = Mage::helper('welance_kraken');
        $this->_addButton('get_images', array(
            'label'   => $helper->__('Get all images'),
            'onclick' => "setLocation('{$this->getUrl('*/*/getImages')}')",
            'class'   => 'add'
        ));

        if(Mage::getResourceModel('welance_kraken/image_collection')->getSize() >0){
            $this->_addButton('add_images_to_queue', array(
                'label'   => $helper->__('Add all images to queue'),
                'onclick' => "setLocation('{$this->getUrl('*/*/addToQueue')}')",
                'class'   => 'add'
            ));
        }

        if(Mage::helper('welance_kraken')->canShowBackupButton()){
            $this->_addButton('restore_images', array(
                'label'   => $helper->__('Restore all images from backup files'),
                'onclick' => "setLocation('{$this->getUrl('*/*/restore')}')",
                'class'   => 'add'
            ));
        }

        $this->setChild('grid', $this->getLayout()->createBlock('welance_kraken/adminhtml_image_grid', 'welance_kraken_image.grid'));
        return parent::_prepareLayout();
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

}