<?
/**
 * Acrit Core: Wildberries tasks
 * @documentation https://suppliers.wildberries.ru/remote-wh-site/api-content.html
 */

namespace Acrit\Core\Export\Plugins\WildberriesV3Helpers;

use
	\Acrit\Core\Helper,
	\Bitrix\Main\Entity;

Helper::loadMessages(__FILE__);

class TaskTable extends Entity\DataManager {
	
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName(){
		return 'acrit_wb_task';
	}
	
	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap() {
		\Acrit\Core\Export\Exporter::getLangPrefix(realpath(__DIR__.'/../../../class.php'), $strLang, $strHead, 
			$strName, $strHint);
		return array(
			'ID' => new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Helper::getMessage($strLang.'ID'),
			)),
			'MODULE_ID' => new Entity\IntegerField('MODULE_ID', array(
				'title' => Helper::getMessage($strLang.'MODULE_ID'),
			)),
			'PROFILE_ID' => new Entity\IntegerField('PROFILE_ID', array(
				'title' => Helper::getMessage($strLang.'PROFILE_ID'),
			)),
			'TASK_UUID' => new Entity\StringField('TASK_UUID', array(
				'title' => Helper::getMessage($strLang.'TASK_UUID'),
			)),
			'SUPPLIER_ID' => new Entity\StringField('SUPPLIER_ID', array(
				'title' => Helper::getMessage($strLang.'SUPPLIER_ID'),
			)),
			'PRODUCTS_COUNT' => new Entity\IntegerField('PRODUCTS_COUNT', array(
				'title' => Helper::getMessage($strLang.'PRODUCTS_COUNT'),
			)),
			'STOCKS_REQUEST' => new Entity\TextField('STOCKS_REQUEST', array(
				'title' => Helper::getMessage($strLang.'STOCKS_REQUEST'),
			)),
			'STOCKS_RESPONSE' => new Entity\TextField('STOCKS_RESPONSE', array(
				'title' => Helper::getMessage($strLang.'STOCKS_RESPONSE'),
			)),
			'STOCKS_RESPONSE_CODE' => new Entity\IntegerField('STOCKS_RESPONSE_CODE', array(
				'title' => Helper::getMessage($strLang.'STOCKS_RESPONSE_CODE'),
			)),
			'SESSION_ID' => new Entity\StringField('SESSION_ID', array(
				'title' => Helper::getMessage($strLang.'SESSION_ID'),
			)),
			'TIMESTAMP_X' => new Entity\DatetimeField('TIMESTAMP_X', array(
				'title' => Helper::getMessage($strLang.'TIMESTAMP_X'),
			)),
		);
	}
	
	/**
	 * Delete by filter
	 *
	 * @return array
	 */
	public static function deleteByFilter($arFilter=null) {
		$strTable = static::getTableName();
		$strSql = "DELETE FROM `{$strTable}` WHERE 1=1";
		if(is_array($arFilter)){
			foreach($arFilter as $strField => $strValue){
				$strEqual = '=';
				if(preg_match('#^(.*?)([A-z0-9_]+)(.*?)$#', $strField, $arMatch)){
					$strField = $arMatch[2];
					if($arMatch[1] == '!'){
						$strEqual = '!=';
					}
				}
				$strField = \Bitrix\Main\Application::getConnection()->getSqlHelper()->forSql($strField);
				$strValue = \Bitrix\Main\Application::getConnection()->getSqlHelper()->forSql($strValue);
				if(is_numeric($strField)){
					$strSql .= " AND ({$strValue})";
				}
				else{
					$strSql .= " AND (`{$strField}`{$strEqual}'{$strValue}')";
				}
			}
			$strSql .= ';';
		}
		return \Bitrix\Main\Application::getConnection()->query($strSql);
	}

}
