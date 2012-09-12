<?php

class WeDo_Assets_Asset
{

    private $id;
    private $type;
    private $assetPath;
    private $assetUrl;
    private $assetName;
    private $attributes;
    private $pid;
    private $creation_dt;

    
    const SQL_LOAD = 'SELECT `id`, `type`, `asset_path`, `asset_url`, `asset_name`, `attributes`, `pid`, `creation_dt` 
                        FROM `%s`
                        WHERE id=%d AND pid=%d';

    const SQL_INSERT = 'INSERT INTO `%s` (`type`, `asset_path`, `asset_url`, `asset_name`, `attributes`, `pid`, `creation_dt`)
                                VALUES("%s", "%s","%s","%s", "%s", "%d","%s")';

    const SQL_UPDATE = 'UPDATE `%s` SET `type`="%s", `asset_path`="%s", `asset_url`="%s", `asset_name`="%s", `attributes`="%s" WHERE `pid`="%d" AND id="%d"';
    const PARENT = 0;
    const ATTRIBUTE_NOT_SET = -1;

    public function __construct()
    {
        $this->id = -1;
        $this->type = '';
        $this->assetPath = '';
        $this->assetUrl = '';
        $this->assetName = '';
        $this->attributes = new stdClass();
        $this->pid = self::PARENT;
        $this->creation_dt = date("Y-m-d h:i:s", WeDo_Application::getTime());
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getAssetPath()
    {
        return $this->assetPath;
    }

    public function setAssetPath($asset)
    {
        $this->assetPath = $asset;
        return $this;
    }
    public function getAssetUrl()
    {
        return $this->assetUrl;
    }

    public function setAssetUrl($asset)
    {
        $this->assetUrl = $asset;
        return $this;
    }
    
    public function getAssetName()
    {
        return $this->assetName;
    }

    public function setAssetName($asset)
    {
        $this->assetName = $asset;
        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
    
    public function setAttribute($attribute, $value)
    {
        $this->attributes->$attribute = $value;
        return $this;
    }
    public function getAttribute($attribute)
    {
        if(isset($this->attributes->$attribute))
            return $this->attributes->$attribute;
        return self::ATTRIBUTE_NOT_SET;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function setPid($pid)
    {
        $this->pid = $pid;
        return $this;
    }

    public function getCreation_dt()
    {
        return $this->creation_dt;
    }

    public function setCreation_dt($creation_dt)
    {
        $this->creation_dt = $creation_dt;
        return $this;
    }

    public static function __fromMap($map)
    {
        $obj = new WeDo_Assets_Asset();
        $obj->setAssetPath($map['asset_path'])
                ->setAssetName($map['asset_name'])
                ->setAssetUrl($map['asset_url'])
                ->setAttributes(json_decode(stripslashes($map['attributes'])))
                ->setCreation_dt($map['creation_dt'])
                ->setId($map['id'])
                ->setPid($map['pid'])
                ->setType($map['type']);
        return $obj;
    }

    public function __toMap()
    {
        $map = array();
        $map['asset_path'] = $this->getAssetPath();
        $map['asset_url'] = $this->getAssetUrl();
        $map['asset_name'] = $this->getAssetName();
        $map['attributes'] = addslashes(json_encode($this->getAttributes()));
        $map['creation_dt'] = $this->getCreation_dt();
        $map['id'] = $this->getId();
        $map['pid'] = $this->getPid();
        $map['type'] = $this->getType();
        return $map;
    }

    public static function load($id, $pid=self::PARENT)
    {
        try {
            $sql = sprintf(self::SQL_LOAD, WeDo_Assets_Library::SQL_TABLE, $id, $pid);
            $res = WeDo_Application::getSingleton('database/default')->fetchAssociative($sql);
            return self::__fromMap($res);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function save()
    {
        try {
            if ($this->id == -1)
                return $this->insert();
            return $this->update();
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function insert()
    {
        try {
            $map = $this->__toMap();
            $sql = sprintf(self::SQL_INSERT, WeDo_Assets_Library::SQL_TABLE, $map['type'], $map['asset_path'],$map['asset_url'],$map['asset_name'], $map['attributes'], $map['pid'], $map['creation_dt']);
            $this->id = WeDo_Application::getSingleton('database/default')->insert($sql);
            return $this;
        } catch (Exception $e) {
            throw $e;
        }
    }
    private function update()
    {
        try {
            $map = $this->__toMap();
            $sql = sprintf(self::SQL_UPDATE, WeDo_Assets_Library::SQL_TABLE, $map['type'], $map['asset_path'],$map['asset_url'],$map['asset_name'], $map['attributes'], $map['pid'], $map['id']);
            WeDo_Application::getSingleton('database/default')->execute($sql);
            return $this;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function duplicate()
    {
        $map = $this->__toMap();
        $new = WeDo_Assets_Asset::__fromMap($map);
        $new->setPid($this->id)
                ->setId(-1);
        return $new;
    }

}

?>
