<?php

class WeDo_Descriptors_Module extends WeDo_Descriptors_Descriptor
{

    public function __construct($path)
    {
        try{ 
            parent::fromFile($path);
        } catch (Exception $e) { throw $e; }
    }

    public function classHasTranslatedFields($class)
    {
        $query = "//class[@name='$class']/@hasTranslatedFields";
        if (WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query) == "Y")
            return true;
        return false;
    }

    public function classHasRelations($class)
    {
        $query = "//class[@name='$class']/@hasRelations";
        if (WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query) == "Y")
            return true;
        return false;
    }
/*
    public function classIsFoundation($class)
    {
        $query = "//class[@name='$class']/@model";
        if (WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query) == "foundation")
            return true;
        return false;
    }

    public function classIsEav($class)
    {
        $query = "//class[@name='$class']/@model";
        if (WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query) == "eav")
            return true;
        return false;
    }
 * 
 */

    
    public function getClassModel($class)
    {
        $query = "//class[@name='$class']/@model";
        return WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query);
    }

    public function getClassService($class)
    {
        $query = "//class[@name='$class']/@service";
        return WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query);
    }

    public function getClassHelper($class)
    {
        $query = "//class[@name='$class']/@helper";
        return WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query);
    }

    public function getClassController($class)
    {
        $query = "//class[@name='$class']/@controller";
        return WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query);
    }

    public function getClassConnectionByName($class, $connectionName)
    {
        $query = "//class[@name='$class']/db/connections/connection[@name='$connectionName']";
        return WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query);
    }

    public function getClassTableName($class, $alias)
    {
        try {
        $query = "//class[@name='$class']/db/tables/table[@name='$alias']";
        return WeDo_Helpers_Xml::queryForNodeValue($this->getDescriptor(), $query);
        } catch(Exception $e)
        {
            throw new Exception("No table found for alias $alias");
        }
    }

    public function classIsAvailableForExport($class)
    {
        $query = "//class[@name='$class']/integration/export/@isExportable";
        if (WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query) == "Y")
            return true;
        return false;
    }

    public function classIsAvailableForImport($class)
    {
        $query = "//class[@name='$class']/integration/import/@isImportable";
        if (WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query) == "Y")
            return true;
        return false;
    }

    public function getClassExportFormatsAvailable($class)
    {
        $query = "//class[@name='$class']/integration/export/format";
        return WeDo_Helpers_Xml::queryForNodeList($this->getDescriptor(), $query);
    }

    public function getClassImportFormatsAvailable($class)
    {
        $res = array();
        $query = "//class[@name='$class']/integration/import/format";
        foreach (WeDo_Helpers_Xml::query($this->getDescriptor(), $query) as $node)
            $res[] = $node->nodeValue;
        return $res;
    }

    public function getClassPrimaryKeys($class)
    {
        $res = array();
        $query = "//class[@name='$class']/primary_keys/key";
        foreach (WeDo_Helpers_Xml::query($this->getDescriptor(), $query) as $node)
            $res[] = $node->nodeValue;
        return $res;
    }

    public function classHasDefaultSortingOptions($class)
    {
        $res = array();
        $query = "//class[@name='$class']/sortings/sorting[@default='Y']";
        $res = WeDo_Helpers_Xml::query($this->getDescriptor(), $query);

        if ($res->length == 0)
            return false;
        return true;
    }

    public function getClassSortingOptions($class, $sortname)
    {
        $res = array();
        $query = "//class[@name='$class']/sortings/sorting[@name='$sortname']/sort";
        foreach (WeDo_Helpers_Xml::query($this->getDescriptor(), $query) as $node)
            $res[] = $node->nodeValue . " " . $node->getAttribute('order');
        return $res;
    }

    public function getClassBackendPathForPage($class, $page)
    {
        $query = "//class[@name='$class']/backend/pages/page[@name='$page']/@path";
        return WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query);
    }

    public function getClassBackendPageProperty($class, $page, $property)
    {
        $query = "//class[@name='$class']/backend/pages/page[@name='$page']/params/param[@name='$property']";
        return WeDo_Helpers_Xml::queryForNodeValue($this->getDescriptor(), $query);
    }

    public function getClassBackendPageLabel($class, $page, $labelfor)
    {
        $query = "//class[@name='$class']/backend/pages/page[@name='$page']/labels/label[@for='$labelfor']";
        return WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query);
    }

    public function getClassBackendPageLabels($class, $page)
    {
        $query = "//class[@name='$class']/backend/pages/page[@name='$page']/labels/label";
        $res = array();
        foreach (WeDo_Helpers_Xml::queryForNodeList($this->getDescriptor(), $query) as $node)
            $res[$node->getAttribute("for")] = $node->nodeValue;
        return $res;
    }

    public function getActions($class)
    {
        $res = array();
        $query = "//class[@name='$class']/backend/actions/action";
        foreach (WeDo_Helpers_Xml::query($this->getDescriptor(), $query) as $node)
        {
            $actionName = $node->getAttribute("action");
            $actionLabel = $node->getAttribute("label");
            $actionBehaviour = $node->getAttribute("behaviour");
            $actionStyleClass = $node->getAttribute("styleClass");
            $actionrequires = $node->getAttribute("requires");
            $actionTarget = $node->getAttribute("target");
            $actionLink = $node->getAttribute("link");

            $res[] = array(
                "label" => $actionLabel,
                "action" => $actionName,
                "behaviour" => $actionBehaviour,
                "styleClass" => $actionStyleClass,
                "requires" => $actionrequires,
                "target" => $actionTarget,
                "link" => $actionLink
            );
        }
        return $res;
    }

    public function getModuleRelease()
    {
        $query = "//module/@rel";
        return WeDo_Helpers_Xml::queryForAttribute($this->getDescriptor(), $query);
    }

    public function getClassCallbacksFor($class, $action)
    {
        $res = array();
        $query = "//class[@name='$class']/callbacks/callback[@for='$action'][1]";
        $node = WeDo_Helpers_Xml::query($this->getDescriptor(), $query);
        $node = $node->item(0);
        if (empty($node))
            return $res;

        $handler = $node->getAttribute("handler");
        $action = $node->getAttribute("action");
        $params = $node->nodeValue;
        $res = array(
            "handler" => $handler,
            "action" => $action,
            "params" => $params
        );

        return $res;
    }

    public function getClassAvailableStatuses($class)
    {
        $res = array();
        $query = "//class[@name='$class']/class_status/status";
        $nodes = WeDo_Helpers_Xml::query($this->getDescriptor(), $query);

        foreach ($nodes as $node)
            $res[] = strval($node);
        return $res;
    }

    public function getClassDefaultStatus($class)
    {
        $res = array();
        $query = "//class[@name='$class']/class_status/status[@default='Y'][1]";
        return WeDo_Helpers_Xml::queryForNodeValue($this->getDescriptor(), $query);

        return strval($node);
    }

}

?>