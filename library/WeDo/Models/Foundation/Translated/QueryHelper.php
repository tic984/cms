<?php
class TranslatedFoundationQueryHelperDecorator
{

	public static function loadById($query, $params, $className, &$moduleDescriptor, &$classDescriptor)
	{
		try {
				$transltable = $moduleDescriptor->getClassTableName($className, "translatedTable");
				$translated_fields = DbHelper::fieldsArrayToSql($classDescriptor->getTranslatedFields(), "translatedTable");
				
				$query -> select($translated_fields)
						-> select('translatedTable.lang')
					->leftJoinOn(array($transltable => "translatedTable"), 'translatedTable.id = mainTable.id')
					->where(array("translatedTable.lang = '?'" => $params['lang']));
				return $query;

		} catch (Exception $e) { throw $e; }
	}
	
	public static function all($query, $lang, $className, &$moduleDescriptor, &$classDescriptor, &$displayContext)
	{
		try{
			$transltable = $moduleDescriptor->getClassTableName($className, "translatedTable");
			$translated_fields = DbHelper::fieldsArrayToSql($classDescriptor->getTranslatedFields($displayContext->getView()), "translatedTable");
			$query	->select($translated_fields)
					->select('translatedTable.lang')
					->leftJoinOn(array($transltable => "translatedTable"), 'cat.id = translatedTable.id')
					->where(array("translatedTable.lang = '?'" => $lang));
			return $query;
		} catch(Exception $e) { throw $e; }
	}
}