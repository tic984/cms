<?php

class WeDo_Descriptors_Object
{

    private $simpleXmlDescriptor;
//    private $_map;
    private $_relations;

    public function __construct($simpleXmlDescriptor)
    {
        $this->simpleXmlDescriptor = $simpleXmlDescriptor;
        //$this->init_map();
        $this->init_relations();
    }

//    private function init_map()
//    {
//        $this->_map = array();
//        foreach ($this->simpleXmlDescriptor->obj->vars->var as $var)
//        {
//            $varname = trim(strval($var));
//            $defaultvalue = trim($this->getDefaultValue($varname));
//            $this->_map[$varname] = $defaultvalue;
//        }
//    }

    private function init_relations()
    {
        $this->_relations = array();
        foreach ($this->simpleXmlDescriptor->obj->vars->rel as $rel)
        {
            $relname = trim(strval($rel));
            $this->_relations[$relname] = array();
        }
    }
//devo far ritornare i campi se tradotti o no
    public function getMap($arr_fieldProperties)
    {
        $map = array();
        if(empty($arr_fieldProperties)) 
        {
        
            foreach ($this->simpleXmlDescriptor->obj->vars->var as $var)
            {
                $varname = trim(strval($var));
                $defaultvalue = trim($this->getDefaultValue($varname));
                $map[$varname] = $defaultvalue;
            }
        } else {
            foreach ($this->simpleXmlDescriptor->obj->vardefs as $var)
            {
                $varname = trim(strval($var));
                if($var[$arr_fieldProperties[0]])
                $defaultvalue = trim($this->getDefaultValue($varname));
                $map[$varname] = $defaultvalue;
            }
        }
        return $map;
    }

    public function getRelations($view='')
    {
        if (empty($view))
            return array_keys($this->_relations);

        $arr = array();
        $nodes = $this->simpleXmlDescriptor->xpath("//views/view[@name='$view']/rel");
        foreach ($nodes as $n)
            $arr[] = strval($n);
        return $arr;
    }

    public function getRelationsName($view='')
    {
        return $this->_relations;
    }

    public function getRelDef($relname)
    {
        $node = $this->simpleXmlDescriptor->obj->reldefs->$relname;
        return strval($node['reldef']);
    }

    /**
     * 
     * returns the full reldefs of a given node.
     * @param unknown_type $relname
     */
    public function getFieldRelDef($relname)
    {
        $node = $this->simpleXmlDescriptor->obj->reldefs->$relname;
        return $node;
    }

    public function getVarDef($varname)
    {
        $node = $this->simpleXmlDescriptor->obj->vardefs->$varname;
        return strval($node['vardef']);
    }

    private function getDefaultValue($item)
    {
        $node = $this->simpleXmlDescriptor->obj->vardefs->$item->defaultvalue;
        if ($node["eval"] == "N")
            return strval($node);
        else
        {
            $code = strval($node);
            if ($node["type"] == "php")
            {
                eval($this->prepareEval($code));
                return $val;
            }
        }
    }

    private function prepareEval($code)
    {
        str_replace('$', '\$', $code);
        $txt = '$val = ' . $code . ';';
        return $txt;
    }

    public function getTranslatedFields($viewname='')
    {
        $arr = array();
        $nodes = $this->simpleXmlDescriptor->xpath('//*[@translatable="Y"]');
        if (empty($viewname))
        {
            foreach ($nodes as $node)
                $arr[] = $node->getName();
        } else
        {
            $viewfields = $this->getFieldsInView($viewname);
            foreach ($nodes as $node)
            {
                $nodename = $node->getName();
                if (in_array($nodename, $viewfields))
                    $arr[] = $nodename;
            }
        }
        return $arr;
    }

    public function getUntranslatedFields($viewname='')
    {
        $arr = array();
        $nodes = $this->simpleXmlDescriptor->xpath('//*[@translatable="N"]');
        if (empty($viewname))
        {
            foreach ($nodes as $node)
                $arr[] = $node->getName();
        } else
        {
            $viewfields = $this->getFieldsInView($viewname);
            foreach ($nodes as $node)
            {
                $nodename = $node->getName();
                if (in_array($nodename, $viewfields))
                    $arr[] = $nodename;
            }
        }
        return $arr;
    }

    public function isFieldTranslatable($field)
    {
        $node = current($this->simpleXmlDescriptor->obj->vardefs->$field);
        if ($node['translatable'] == 'Y')
            return true;
        return false;
    }

    public function getAllFields($view='')
    {
        $arr = array();
        if ($view == '')
        {
            $nodes = $this->simpleXmlDescriptor->obj->vardefs;
            foreach ($nodes->children() as $node)
                $arr[] = $node->getName();
        } else
            return $this->getFieldsInView($view);
        return $arr;
    }

    public function getFieldsInView($viewname)
    {
        $arr = array();
        $nodes = $this->simpleXmlDescriptor->xpath("//views/view[@name='$viewname']/var");
        foreach ($nodes as $n)
            $arr[] = strval($n);

        return $arr;
    }

    /**
     *
     * returns the name of the field where the relation is stored.
     * @param string $viewname
     */
    public function getRelationsInView($viewname)
    {
        $arr = array();
        $nodes = $this->simpleXmlDescriptor->xpath("//views/view[@name='$viewname']/rel");
        foreach ($nodes as $n)
            $arr[] = strval($n);
        /**
         * Now I have to fetch the name of the relations.
         */
        //		$relnames = array();
        //		foreach($arr as $nodename)
        //		{
        //			$xpath_query = "//".$nodename."/@reldef[1]";
        //			$relnames[] = strval(current($this->simpleXmlDescriptor->xpath($xpath_query)));
        //
		//		}
        return $arr;
    }

    /*
     * I use reldef attribute to extract relation name;
     * It's not clean programming, i admit :)
     */

    public function fieldIsRelation($field)
    {
        return isset($this->simpleXmlDescriptor->obj->reldefs->$field);
    }
    
    public function getRelationFieldByRelationName($inTableRelationname)
    {
        $query = "//*[@reldef='$inTableRelationname']";
        $fields = $this->simpleXmlDescriptor->xpath($query);
        $res = '';
        foreach ($fields as $f)
            $res = $f->getName();
        return $res;
    }

    /**
     * 
     * returns full vardefs of the given Node, not just the name
     * @param $label
     */
    public function getFieldVardef($field)
    {
        return $this->simpleXmlDescriptor->obj->vardefs->$field;
    }
    
    public function getFieldVardefLabel($field)
    {
        $vardef = $this->getFieldVardef($field);
        return $vardef['vardef'];
    }
    
    public function getLibraryItems()
    {
        
    }

}