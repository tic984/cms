<?php
class WeDo_Models_ShantyMongoMapper
{
    public function all($params=array(), $fields=array(), $skip=false, $limit=false)
    {
       $cursor = call_user_func(array(static::$_modelRef, 'all'), $params, $fields); //Project_Pages_Model_Page::all($params, $fields);
       if($skip!== false)
           $cursor = $cursor->skip($skip);
       if($limit !== false)
           $cursor = $cursor->limit($limit);
        return $cursor;
    }
    
    public function count($params=array())
    {
        $cursor = call_user_func(array(static::$_modelRef, 'all'), $params);
        return $cursor->count();
    }
    
    public function find($id, $fields = array())
    {
        return call_user_func(array(static::$_modelRef, 'find'), $id, $fields); //Project_Pages_Model_Page::find($id, $fields);
    }
    
    public function save()
    {
        
    }
    
    public function remove($paramId)
    {
        return call_user_func(array(static::$_modelRef, 'remove'), array('_id' => $paramId)); //Project_Pages_Model_Page::remove(array('_id' => $paramId));
    }
}
?>
