<?php
class EmailResource extends Descriptor
{
	public function __construct(&$simpleXmlDescriptor)
	{
		parent::fromSimpleXml($simpleXmlDescriptor);
	}
}
?>