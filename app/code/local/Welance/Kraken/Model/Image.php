<?php
/**
 * Class Welance_Kraken_Model_Image
 *
 * @method datetime setCreatedAt()
 * @method datetime getCreatedAt()
 * @method string setPath()
 * @method string getPath()
 * @method string setImageName()
 * @method string getImageName()
 * @method string setOriginalChecksum()
 * @method string getOriginalChecksum()
 * @method int setOriginalSize()
 * @method int getOriginalSize()
 * @method datetime setUploadedAt()
 * @method datetime getUploadedAt()
 * @method string setUploadedChecksum()
 * @method string getUploadedChecksum()
 * @method int setUploadedSize()
 * @method int getUploadedSize()
 * @method int setUploadedSaved()
 * @method int getUploadedSaved()
 * @method setUploadedSavedPercent()
 * @method getUploadedSavedPercent()
 * @method boolean getSuccess()
 * @method boolean setSuccess()
 * @method array setResponseError()
 * @method array getResponseError()
 */
class Welance_Kraken_Model_Image extends Mage_Core_Model_Abstract
{
    const KRAKEN_UPLOAD_API_URL = 'https://api.kraken.io/v1/upload';
    const KRAKEN_USER_STATUS_API_URL = 'https://api.kraken.io/user_status';

    protected function _construct()
    {
        $this->_init('welance_kraken/image');
    }

    public function saveImageInQueue($dir,$imageName,$checksum)
    {
        $this->setCreatedAt(time())
            ->setPath($dir)
            ->setImageName($imageName)
            ->setOriginalChecksum($checksum);
        try {

            $this->save();

        } catch(Exception $e){

            Mage::log($e->getMessage(),null,'kraken.log');

        }

        return $this;
    }

    public function imageExits($path,$imageName,$checksum)
    {
        $imageCollection = Mage::getResourceModel('welance_kraken/image_collection')
            ->addFieldToFilter('path',$path)
            ->addFieldToFilter('image_name',$imageName)
            ->addFieldToFilter('original_checksum',$checksum);

        if($imageCollection->getSize() > 0){
            return true;
        }

        return false;
    }

    public function saveResponse($response)
    {
        if($response->success == true){
            if($response->original_size <= $response->kraked_size == false){
                $path = Mage::getBaseDir() . DS . $this->getPath() . DS . $this->getImageName();
                try{
                    copy($response->kraked_url,$path);
                } catch(Exception $e){
                    Mage::log($e->getMessage(),null,'kraken_response.log');
                }


                $this->setUploadedAt(time())
                    ->setOriginalSize($response->original_size)
                    ->setSuccess($response->success)
                    ->setSizeAfterUpload($response->kraked_size)
                    ->setSavedFileSize($response->saved_bytes)
                    ->setPercentSaved(round(($response->saved_bytes/$this->getOriginalSize())*100,2))
                    ->setChecksumAfterUpload(sha1_file($path))
                    ->setResponseError(null)
                    ->setInQueue(0);
            } else {
                $this->setUploadedAt(time())
                    ->setOriginalSize($response->original_size)
                    ->setSizeAfterUpload($response->original_size)
                    ->setPercentSaved(0)
                    ->setSuccess(0)
                    ->setResponseError(Mage::helper('welance_kraken')->__('No Savings found.'))
                    ->setInQueue(0);
            }

        } else{
            $this->setResponseError($response->message)
                ->setSuccess(0);
        }

        try{
            $this->setResponseTime(microtime(true) - $response->startTime);
            $this->save();
        } catch(Exception $e){
            Mage::log($e->getMessage(),null,'kraken_response.log');
        }

        return $this;
    }

    public function getImages($limit = null, $imageIds = null)
    {
        $imageCollection = $this->getCollection()
            ->addFieldToSelect('*');

        if($imageIds){
            $imageCollection->addFieldToFilter('id',array('in' => $imageIds));
        } else {
            $imageCollection->addFieldToFilter('success',array('null' => true));
        }

        if($limit){
            $imageCollection->getSelect()->limit($limit);
        }

        return $imageCollection;

    }

    public function getImagesFromQueue($limit)
    {
        $imageCollection = $this->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('in_queue',1);

        $imageCollection->getSelect()->limit($limit);

        return $imageCollection;

    }


    public function clearUploadQueue()
    {
        $this->setUploadedAt(null)
            ->setOriginalSize(null)
            ->setSuccess(null)
            ->setSizeAfterUpload(null)
            ->setSavedFileSize(null)
            ->setPercentSaved(null)
            ->setChecksumAfterUpload(null)
            ->setResponseError(null)
            ->setInQueue(0)
            ->save();

        return $this;
    }

    public function addImagesToQueue($imageIds = null)
    {
        $imageCollection = $this->getImages(null,$imageIds);

        foreach ($imageCollection as $image){
            $image->setInQueue(1)
                ->save();
        }

        return $this;
    }

    public function addAllImagesToQueue()
    {
        $resource = Mage::getSingleton('core/resource');

        $writeConnection = $resource->getConnection('core_write');

        $table = $resource->getTableName('welance_kraken/image');

        $query = "UPDATE {$table} SET in_queue = 1 WHERE success IS NULL";

        $writeConnection->query($query);

        return $this;
    }
}