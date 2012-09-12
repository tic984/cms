<?php

class WeDo_Helpers_Xml
{

    private static $_enableLogging = false;

    public static function queryForNodeList(&$domDocument, $xpath_query)
    {
        try {
            $xpath = new DOMXPath($domDocument);
            $arr = array();
            self::debug($xpath_query);
            foreach ($xpath->query($xpath_query) as $res)
                $arr[] = $res;
            return $arr;
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . ", " . $xpath_query);
        }
    }

    public static function queryForNodeValue(&$domDocument, $xpath_query)
    {
        try {
            $xpath = new DOMXPath($domDocument);
            $res = $xpath->evaluate($xpath_query);
            $node = $res->item(0);
            self::debug($xpath_query);
            if (!empty($node))
                return $node->nodeValue;
            return null;
        } catch (Exception $e) {
            throw new Exception($xpath_query);
        }
    }

    //@TODO: mettere a posto, non voglio che ritorni una stringa vuota
    public static function queryForAttribute(&$domDocument, $xpath_query)
    {
        $xpath = new DOMXPath($domDocument);
        $query_result = $xpath->query($xpath_query);

        if (empty($query_result))
            return '';
        $arr = array();
        self::debug($xpath_query);
        foreach ($query_result as $res)
            $arr[] = $res->nodeValue;

        $res = current($arr);
        if ($res === false)
            return '';
        return $res;
    }

    public static function queryForCount(&$domDocument, $xpath_query)
    {
        $xpath = new DOMXPath($domDocument);
        self::debug($xpath_query);
        return $xpath->evaluate($xpath_query);
    }

    public static function query(&$domDocument, $xpath_query)
    {
        $xpath = new DOMXPath($domDocument);
        self::debug($xpath_query);
        return $xpath->query($xpath_query);
    }

    private static function debug($query)
    {
        if (self::$_enableLogging)
            Logger::getLogger(__CLASS__)->debug($query);
    }

}