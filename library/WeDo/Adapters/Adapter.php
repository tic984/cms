<?php

abstract class WeDo_Adapters_Adapter
{

    private $_conn_name;

    public function __construct($namespace, $conn_name)
    {
        $this->_conn_name = $namespace . $conn_name;
    }

    public function enrollSingleton()
    {
        WeDo_Application::enrollSingleton($this, WeDo_ClassURI::fromString($this->_conn_name));
    }

}

?>