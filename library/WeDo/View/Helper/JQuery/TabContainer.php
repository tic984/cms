<?php
class WeDo_View_Helper_JQuery_TabContainer extends ZendX_JQuery_View_Helper_TabContainer
{
    
    public $list = '';
    public $html = '';
    
    /*
    public function tabContainer($id=null, $params=array(), $attribs=array())
    {
        $this->html = '';
        $this->list = '';
        
        if(func_num_args() === 0) {
            return $this;
        }

        if(!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }

        if(isset($this->_tabs[$id])) {
            $fragment_counter = 1;
            foreach($this->_tabs[$id] AS $k => $v) {
                $frag_name = sprintf('%s-frag-%d', $attribs['id'], $fragment_counter++);
                $opts = $v['options'];
                if(isset($opts['contentUrl'])) {
                    $this->list .= '<a href="'.$opts['contentUrl'].'">'.$v['name'].'</a>'.PHP_EOL;
                } else {
                    $this->list .= '<a href="#'.$frag_name.'">'.$v['name'].'</a>'.PHP_EOL;
                    $this->html .= '<div id="'.$frag_name.'" class="ui-tabs-panel">'.$v['content'].'</div>'.PHP_EOL;
                }
            }
            unset($this->_tabs[$id]);
        }
        $this->list = sprintf('<div class="tabs" id="%s">%s</div>', $id, $this->list);
        if(count($params)) {
            $params = ZendX_JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }
        
        $js = sprintf('%s("#%s").tabs(%s);',
            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
            $attribs['id'],
            $params
        );
        $this->jquery->addOnLoad($js);
        return $this->html;
    }
     * 
     */
    
    public function tabContainer($id=null, $params=array(), $attribs=array())
    {
        if($this->list!= '') return $this->list;
        if($this->html!= '') return $this->html;
        
        $this->html = '';
        
        if(func_num_args() === 0) {
            return $this;
        }

        if(!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }

        $content = "";
        if(isset($this->_tabs[$id])) {
            $this->list = '<div id="'.$attribs['id'].'">'.PHP_EOL;
            
            $fragment_counter = 1;
            foreach($this->_tabs[$id] AS $k => $v) {
                $frag_name = sprintf('%s-frag-%d', $attribs['id'], $fragment_counter++);
                $opts = $v['options'];
                if(isset($opts['contentUrl'])) {
                    $this->list .= '<a href="'.$opts['contentUrl'].'">'.$v['name'].'</a>'.PHP_EOL;
                } else {
                    $this->list .= '<a href="#'.$frag_name.'">'.$v['name'].'</a>'.PHP_EOL;
                    $this->html .= '<div id="'.$frag_name.'">'.$v['content'].'</div><!-- end of '.$attribs['id'].' -->'.PHP_EOL;
                }
            }
            $this->list .= '</div>'.PHP_EOL;

            $content = $this->list.$this->html;
            unset($this->_tabs[$id]);
        }

        if(count($params)) {
            $params = ZendX_JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = sprintf('%s("#%s").tabs(%s);',
            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
            $attribs['id'],
            $params
        );
        $this->jquery->addOnLoad($js);
        return $this;
//        $html = '<div'
//              . $this->_htmlAttribs($attribs)
//              . '>'.PHP_EOL
//              . $content
//              . '</div>'.PHP_EOL;
//        return $html;
    }
}
?>
