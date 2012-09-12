<?php
class WeDo_Models_ShantyMongo extends Shanty_Mongo_Document 
{   
    public function toMap() {
        $map = $this->_cleanData;
        $map['_id'] = $this->getId();
        return $map;
    }

    public function fromRequest($request_type = INPUT_POST) {
        try {

            $map = array();
            switch ($request_type) {
                default:
                case INPUT_POST:
                    $map = $_POST;
                    break;
                case INPUT_GET:
                    $map = $_GET;
                    break;
                case INPUT_REQUEST:
                    $map = $_REQUEST;
                    break;
            }


            if (get_magic_quotes_gpc()) {
                foreach ($map as $k => $v) {
                    if ($k == '_id')
                        $this->_id = new MongoId($v);
                    else
                        $this->$k = $v;
                }
            }
            else {
                foreach ($map as $k => $v) {
                    if ($k == '_id')
                        $this->_id = new MongoId($v);
                    else
                        $this->$k = addslashes($v);
                }
            }
            return $this;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>
