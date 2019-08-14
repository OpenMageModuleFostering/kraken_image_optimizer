<?php

class Welance_Kraken_Block_Adminhtml_Image_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('kraken_image_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

    }

    protected function _getCollectionClass()
    {
        return 'welance_kraken/image_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $_helper = Mage::helper('welance_kraken');

        $this->addColumn('created_at',
            array(
                'header' => $_helper->__('Created at'),
                'type' => 'datetime',
                'filter_index' => 'created_at',
                'index' => 'created_at'
            )
        );

        $this->addColumn('path',
            array(
                'header'    => $_helper->__('Path'),
                'filter_index'  => 'path',
                'index' => 'path'
            )
        );

        $this->addColumn('image_name',
            array(
                'header' => $_helper->__('Image Name'),
                'filter_index' => 'image_name',
                'index' => 'image_name'
            )
        );

        $this->addColumn('original_size',
            array(
                'header' => $_helper->__('Original Size'),
                'type' => 'number',
                'filter_index' => 'original_size',
                'index' => 'original_size',
                'renderer'   => 'Welance_Kraken_Block_Adminhtml_Image_Renderer_Size'
            )
        );

        $this->addColumn('uploaded_at',
            array(
                'header' => $_helper->__('Uploaded At'),
                'type' => 'datetime',
                'filter_index' => 'uploaded_at',
                'index' => 'uploaded_at'
            )
        );


        $this->addColumn('size_after_upload',
            array(
                'header' => $_helper->__('Size after Upload'),
                'type' => 'number',
                'filter_index' => 'size_after_upload',
                'index' => 'size_after_upload',
                'renderer'   => 'Welance_Kraken_Block_Adminhtml_Image_Renderer_Size'
            )
        );

        $this->addColumn('saved_file_size',
            array(
                'header' => $_helper->__('Saved File Size'),
                'type' => 'number',
                'filter_index' => 'saved_file_size',
                'index' => 'saved_file_size',
                'renderer'   => 'Welance_Kraken_Block_Adminhtml_Image_Renderer_Size'
            )
        );

        $this->addColumn('percent_saved',
            array(
                'header' => $_helper->__('Saved %'),
                'type' => 'number',
                'filter_index' => 'percent_saved',
                'index' => 'percent_saved'
            )
        );

        $this->addColumn('response_error',
            array(
                'header' => $_helper->__('Message'),
                'filter_index' => 'response_error',
                'index' => 'response_error'
            )
        );

        $this->addColumn('in_queue',
            array(
                'header' => $_helper->__('In Queue'),
                'filter_index' => 'in_queue',
                'index' => 'in_queue'
            )
        );


        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $_helper = Mage::helper('welance_kraken');

        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->setFormFieldName('image');

        $this->getMassactionBlock()->addItem('optimize', array(
            'label'=> $_helper->__('Add selected images to queue'),
            'url'  => $this->getUrl('*/*/addToQueueMass'),
        ));

        $this->getMassactionBlock()->addItem('restore', array(
            'label'=> $_helper->__('Restore selected images from backup'),
            'url'  => $this->getUrl('*/*/restoreMass'),
        ));

        return $this;
    }


    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array(
                'id'=>$row->getId())
        );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }


}