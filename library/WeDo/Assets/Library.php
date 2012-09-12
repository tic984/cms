<?php
class WeDo_Assets_Library
{
    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_VIDEO = 'video';
    const MEDIA_TYPE_YOUTUBE = 'youtube';
    const MEDIA_TYPE_DOWNLOAD = 'download';
    const MEDIA_TYPE_ARCHIVE = 'archive';
    
    const SQL_TABLE = 'tbl_library';
    
    const SQL_LOAD_BY_MEDIA_TYPE = "SELECT `id`, `type`, `asset`, `attributes`, `pid`, `creation_dt`
                                    FROM %s 
                                    WHERE type='%s' AND pid=0
                                    ";
    
    public static function listByMediatype($media_type, $params=array())
    {
        try {
            $sql = sprintf(self::SQL_LOAD_BY_MEDIA_TYPE, self::SQL_TABLE, $media_type);
            $result = WeDo_Application::getSingleton('database/default')->fetchRowsAssociative($sql);
            $res = array();
            foreach($result as $map)
                $res[] = WeDo_Assets_Asset::__fromMap($map); 
            return $res;
        } catch(Exception $e)
        {
            throw $e;
        }
    }
}
?>
