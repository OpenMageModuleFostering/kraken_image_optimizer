<?php

class Welance_Kraken_Block_Adminhtml_Image_View extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('kraken/image/view.phtml');
        $this->setId('image_view');
    }

    protected function _prepareLayout()
    {
        $this->setChild('optimize_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('welance_kraken')->__('Optimize Image'),
                    'onclick'   => 'setLocation(\'' . $this->getOptimizeUrl() . '\')',
                    'class'  => 'add'
                ))
        );

        $this->setChild('queue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('welance_kraken')->__('Add Image To Queue'),
                    'onclick'   => 'setLocation(\'' . $this->getQueueUrl() . '\')',
                    'class'  => 'add'
                ))
        );

        $this->setChild('restore_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('welance_kraken')->__('Restore Backup'),
                    'onclick'   => 'setLocation(\'' . $this->getRestoreUrl() . '\')',
                    'class'  => 'add'
                ))
        );

        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('welance_kraken')->__('Back'),
                    'onclick'   => 'setLocation(\''.$this->getBackUrl() .'\')',
                    'class' => 'back'
                ))
        );


    }

    public function getImage()
    {
        return Mage::registry('current_image');
    }

    public function getOptimizeUrl()
    {
        return $this->getUrl('*/*/uploadSingleImage', array('_current'=>true));
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }

    public function getRestoreUrl()
    {
        return $this->getUrl('*/*/restoreSingleImage', array('_current'=>true));
    }

    public function getQueueUrl()
    {
        return $this->getUrl('*/*/addSingleImageToQueue', array('_current'=>true));
    }


    public function getOptimizeButtonHtml()
    {
        return $this->getChildHtml('optimize_button');
    }

    public function getRestoreButtonHtml()
    {
        return $this->getChildHtml('restore_button');
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getQueueButtonHtml()
    {
        return $this->getChildHtml('queue_button');
    }

    public function backupFileExists()
    {
        $_image = $this->getImage();

        return file_exists(Mage::getBaseDir().DS.Mage::getStoreConfig('welance_kraken/kraken_config/backup_dir').DS.$_image->getPath().DS.$_image->getImageName());
    }
}