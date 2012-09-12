<?php
class EavObjectDal
{

	const SQL_GET_ATTRIBUTES = "SELECT * FROM %s WHERE class_uri='%s'";
	const SQL_GET_LIST_ATTRIBUTES = "SELECT %s FROM `%s` WHERE list = 'Y' AND class_uri='%s'";
	const SQL_GET_DISTINCT_BACKEND_MODELS = "SELECT DISTINCT `backend_model` FROM `%s` WHERE class_uri='%s'";

	const SELECT_DISTINCT_LIST_ATTRIBUTES = 1;
	const SELECT_ALL_LIST_ATTRIBUTES = 2;

	const TBL_EAV_ATTRIBUTES = 'tbl_eav_attributes';

	public static function loadById($id, $lang, $classUri, &$moduleDescriptor, &$classDescriptor, &$connection )
	{
		try {

			list($moduleName, $className) = WeDo_Helpers_Application::explodeClassUri($classUri);
			$mainTable = $moduleDescriptor->getClassTableName($className,"mainTable");

			//Base fields for EAV Objects are those reported in .xml.
			$base_fields = $classDescriptor->getAllFields();
			$attributes = self::getAttributes($classUri, $connection);

			//At first, I export ALL base fields;
			$baseFields = self::loadAllEntityFields($mainTable, $id, $connection);

			//now I query the DB for all other fields, but THIS WAY:
			//first I ask for DISTINCT backend types, so i can get the tables where contents are
			//then i query these tables. So in one query, I'll get all varchars (for example), then I'll get all ints, etc..
			$attributesRow = array();
			foreach(self::getDistinctBackendModelsForClass($classUri, $connection) as $backendModel)
			{
				$attributeTable = $moduleDescriptor->getClassTableName($className,$backendModel['backend_model']);
				$s = new WeDo_Db_Query_Select();
				$s	->select(array('attributes.frontend_label', 'attTable.value' ))
				->from(array($attributeTable => "attTable"))
				->innerJoinOn(array(self::TBL_EAV_ATTRIBUTES => 'attributes'), 'attributes.id = attTable.attribute_id')
				->where(array("attTable.entity_id = '?'" => $id))
				->where(array("attTable.lang = '?'" => $lang));
					
				$attributesRow[] = $connection->fetchRowsAssociative($s->getQuery());
			}
			return self::render($attributes, $baseFields, $attributesRow);

		} catch (Exception $e) { throw $e; }
	}
	/**
	 *
	 * To get a List, i need first to detect where list attributes are.
	 * If there're list attributes outside the entity table, I need to extract them.
	 * I extract these attributes the way I extract them for loadById: get distinct backendmodel first,
	 * then perform distinct queries.
	 *
	 * @param string $lang
	 * @param string $classUri
	 * @param ModuleDescriptor $moduleDescriptor
	 * @param ClassDescriptor $classDescriptor
	 * @param DisplayContext $displayContext
	 * @param Connection $connection
	 */
	public static function getList($lang, $classUri, &$moduleDescriptor, &$classDescriptor, &$displayContext, &$connection)
	{
		try {

			list($moduleName, $className) = WeDo_Helpers_Application::explodeClassUri($classUri);
			$mainTable = $moduleDescriptor->getClassTableName($className,"mainTable");
			$list_attributes = self::getListAttributes($classUri, $connection, self::SELECT_ALL_LIST_ATTRIBUTES);

			$select_entity_fields = new WeDo_Db_Query_Select();
			$select_entity_fields->select()
			->from($mainTable)
			->where(array("active = 'Y'"));
			if(!empty($displayContext) && $displayContext->useLimit())
			$select_entity_fields->limit($displayContext->getStart(), $displayContext->getLen());

			$entities = $connection->fetchRowsAssociative($select_entity_fields->getQuery());

			if(!empty($list_attributes))
			{
				//I extract all entity id from result before

				$ids = array();
				foreach($entities as $e)
				$ids[] = $e['id'];

				$sql_ids =  implode(',', $ids);
				$filled_attributes = array();

				foreach(self::getListAttributes($classUri, $connection, self::SELECT_DISTINCT_LIST_ATTRIBUTES) as $backendModel)
				{
					$attributeTable = $moduleDescriptor->getClassTableName($className, $backendModel['backend_model']);
					$s = new WeDo_Db_Query_Select();
					$s	->select(array('attTable.entity_id', 'attributes.frontend_label', 'attTable.value' ))
					->from(array($attributeTable => "attTable"))
					->innerJoinOn(array(self::TBL_EAV_ATTRIBUTES => 'attributes'), 'attributes.id = attTable.attribute_id')
					->where(array("attTable.entity_id IN (?)" => $sql_ids))
					->where(array("attTable.lang = '?'" => $lang));
					$filled_attributes[] = $connection->fetchRowsAssociative($s->getQuery());
				}
			}
			return self::renderList($entities, $list_attributes, $filled_attributes);
		} catch (Exception $e) { throw $e; }
	}

	public static function countList($lang, $classUri, &$moduleDescriptor, &$classDescriptor, &$connection)
	{
		try {

		} catch (Exception $e) { throw $e; }
	}

	public static function insertToCatalog(&$object, &$connection)
	{
		try
		{

		} catch (Exception $e) { throw $e; }
	}

	public static function insertToTables($id, &$object, &$moduleDescriptor, &$classDescriptor, &$connection)
	{
		try
		{

		} catch (Exception $e) { throw $e; }
	}

	private static function getAttributes($classUri, $connection)
	{
		$query = sprintf(self::SQL_GET_ATTRIBUTES, self::TBL_EAV_ATTRIBUTES, DbHelper::escape($classUri));
		return $connection->fetchRowsAssociative($query);
	}

	private static function getDistinctBackendModelsForClass($classUri, $connection)
	{
		$query = sprintf(self::SQL_GET_DISTINCT_BACKEND_MODELS, self::TBL_EAV_ATTRIBUTES, DbHelper::escape($classUri));
		return $connection->fetchRowsAssociative($query);
	}

	private static function getListAttributes($classUri, $connection, $select_distinct)
	{
		if($select_distinct=='SELF::SELECT_DISTINCT_LIST_ATTRIBUTES')
		$query = sprintf(self::SQL_GET_LIST_ATTRIBUTES, ' DISTINCT `backend_model` ', self::TBL_EAV_ATTRIBUTES, DbHelper::escape($classUri));
		else
		$query = sprintf(self::SQL_GET_LIST_ATTRIBUTES, '`backend_model`, `frontend_label` ', self::TBL_EAV_ATTRIBUTES, DbHelper::escape($classUri));
		return $connection->fetchRowsAssociative($query);
	}

	private static function loadAllEntityFields($mainTable, $id, $connection)
	{
		try {
			$queryBaseFields = new WeDo_Db_Query_Select();
			$queryBaseFields->select()
			->from(array($mainTable => "mainTable"))
			->where(array("id = '?'" => $id));

			return $connection->fetchAssociative($queryBaseFields->getQuery());
		} catch (Exception $e) { throw $e; }
	}

	private static function render($attributes, $baseFields, $attributesRow)
	{
		//print_r($attributesRow);
		//I might not have some properties in the db. So first I set all property I should have (attributes)
		//and later I'll override them with those I have.
		foreach($attributes as $attributedescriptor)
		$baseFields[$attributedescriptor['frontend_label']] = '';

		foreach($attributesRow as $backendmodeltable)
		foreach($backendmodeltable as $row)
		$baseFields[$row['frontend_label']] = $row['value'];

		return $baseFields;
	}

	private static function renderList($entities, $list_attributes, $filled_attributes)
	{
		$res = array();
		foreach($entities as $e)
		{
			$id = $e['id'];
			$res[$id] = $e;
			foreach($list_attributes as $attributedescriptor)
			$res[$id][$attributedescriptor['frontend_label']] = '';


			foreach($filled_attributes as $backendmodeltable)
			foreach($backendmodeltable as $row)
			{
				$attr_ref_id = $row['entity_id'];
				$res[$attr_ref_id][$row['frontend_label']] = $row['value'];
			}
		}
		return $res;
	}
}
?>