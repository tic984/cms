<?php
class WeDo_Assets_Uploader
{
    private $_classUri;
    private $_apply_rename;
    private $_fieldName;
    private $_itemId;
    private $_itemLang;
    
    const ASSETS_PATH = '/assets';
    const APPLY_RENAME = true;
    const DO_NOT_RENAME = false;
    
    public function __construct($classUri, $fieldName, $itemId, $itemLang, $apply_rename=self::APPLY_RENAME)
    {
        $this->_apply_rename = $apply_rename;
        $this->_classUri = $classUri;
        $this->_fieldName = $fieldName;
        $this->_itemId = $itemId;
        $this->_itemLang = $itemLang;
    }
    
    private function _getAttributes($absPath)
    {
        $attributes = array();
        if(file_exists($absPath))
        {
            $attributes["size"] = filesize($absPath);
            $is_image = getimagesize($absPath);
            if($is_image)
            {
                $attributes["width"] = $is_image[0];
                $attributes["height"] = $is_image[1];
                $attributes["type"] = $is_image[2];
                $attributes["bits"] = $is_image['bits'];
                $attributes["channels"] = $is_image['channels'];
                $attributes["mime"] = $is_image['mime'];                
            }
            return json_encode($attributes);
        }
        return false;
    }
    
    public function upload(&$fileRef) {
        try {
            $tempFile = $fileRef['tmp_name'];
            $fileName = $fileRef['name'];
            
            $targetPath = $this->_getAbsPath($fileName);
            if($this->_apply_rename)
            {
                $fileName = $this->_rename($targetPath);
                $targetPath = $this->_getAbsPath($fileName);
            }
            
            $targetFile = str_replace('//', '/', $targetPath);
            $urlPath = str_replace('//', '/', $this->_getUrlPath($fileName));
            if(!move_uploaded_file($tempFile, $targetFile)) throw new Exception("Cannot move uploaded file");

            switch ($_FILES['uploaded']['error']) {
                case 0:
                    $asset = $this->save($fileName, $targetPath, $urlPath);
                    $this->updateObject($asset);
                    return $asset;
                case 1:
                    throw new Exception("The file is bigger than this PHP installation allows");
                case 2:
                    throw new Exception("The file is bigger than this form allows");
                case 3:
                    throw new Exception("Only part of the file was uploaded");
                case 4:
                    throw new Exception("No file was uploaded");
                case 6:
                    throw new Exception("Missing a temporary folder");
                case 7:
                    throw new Exception("Failed to write file to disk");
                case 8:
                    throw new Exception("File upload stopped by extension");
                default:
                    throw new Exception("unknown error " . $fileRef['error']);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    private function _getAbsPath($fname)
    {
        return sprintf("%s%s/%s", PUBLIC_PATH, self::ASSETS_PATH , $fname);
    }
    
    private function _getUrlPath($fname)
    {
        return sprintf("%s/%s", self::ASSETS_PATH , $fname);
    }
    
    private function _rename($targetFile) {
        try {
            
            $pathinfo = pathinfo($targetFile);
            if (isset($pathinfo['extension']))
                return sprintf("%s.%s", md5(time() . $pathinfo['filename']), $pathinfo['extension']);
            return sprintf("%s", md5(time() . $pathinfo['filename']));
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    private function save($fname, $fabsPath, $fUrlPath)
    {
        try {
            $asset = new WeDo_Assets_Asset();
            $asset->setAssetName($fname)
                    ->setAssetPath($fabsPath)
                    ->setAssetUrl($fUrlPath)
                    ->setType('image')
                    ->setAttributes($this->_getAttributes($fabsPath))
                    ->save();
            return $asset;
        } catch(Exception $e)
        {
            throw $e;
        }
        
    }
    
    private function updateObject(&$asset)
    {
        try {
            
            $obj = call_user_func($this->_classUri .'::find', $this->_itemId);       
            $assetField = $this->_fieldName;
            $obj->$assetField = $asset->getAssetName();
            $obj->save();
        } catch(Exception $e)
        {
            throw $e;
        }
        
    }
    
}
?>
