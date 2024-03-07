<?
/**
 * Acrit Core: onliner.by plugin
 * @documentation https://github.com/onlinerby/onliner-b2b-api/
 */

namespace Acrit\Core\Export\Plugins;


use \Acrit\Core\Export\Filter,
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\Plugins\OnlinerbyHelpers\Api,
	\Acrit\Core\Export\Plugins\OnlinerbyHelpers\CategoryTable as Category,
	\Acrit\Core\Export\Plugins\OnlinerbyHelpers\AttributeTable as Attribute,
	\Acrit\Core\Export\Plugins\OnlinerbyHelpers\AttributeValueTable as AttributeValue,
	\Acrit\Core\Export\Plugins\OnlinerbyHelpers\AccesstokenTable as Accesstoken,
	\Acrit\Core\Export\ProfileTable as Profile,
	\Acrit\Core\Export\Field\ValueBase,
	\Bitrix\Main\Context;


class OnlinerbyGeneralApi extends Onlinerby {
	
	const DATE_UPDATED = '2023-03-20';
	const ATTRIBUTE_ID = 'attribute_%s_%s';
		
	protected $api_url = 'https://b2bapi.onliner.by';
	
	const ATTR_ID_IMAGE = 4194;
	const ATTR_ID_IMAGES = 4195;
	const ATTR_ID_YOUTUBE_CODE = 4074;
	const ATTR_ID_JSON_RICH_CONTENT = 11254;
	
	protected static $bSubclass = true;
	//protected $cache_dir = '/upload/acrit.core/.tmp/onliner.by/api/';
	
	# General	
	protected $strDefaultFilename = 'onliner.by.xml';	
	protected $arSupportedFormats = ['JSON']; // Формат выгрузки - JSON
	protected $arSupportedEncoding = [self::UTF8];
	//protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'USD', 'EUR'];
	
	# Basic settings
	protected $bApi = true; // Выгружаем не в файл, а по АПИ
	protected $bCategoriesExport = true; // Нужно чтобы в целом была возможность работать с категориями, хотя категории отдельно не выгружаются	
	//protected $bCategoriesList = true; // В плагине доступен список категорий, необходимо для работы со списком категорий	
	//protected $bCategoriesUpdate = true; // Разрешаем обновлять категории	
	protected $bCategoriesStrict = true; // Важно указывать только категории сервиса
	protected $bCategoryCustomName = true; // Добавляем возможность использовать значение «Использовать поля товаров» в опции «Источник названий категорий»

	protected $bCurrenciesExport = true;
	
	# XML settings
	//protected $strXmlItemElement = 'offer';
	//protected $intXmlDepthItems = 1;
	
	//protected $arXmlMultiply = ['availability'];
	
	protected $test_count = 0;
	protected $api_categories = [];
	protected $arUsedCategories = [];
	protected $api_attribute_values = [];
	protected $api_attribute_upload_result = [];
	protected $arRedefinitions = [];
	
	# Misc
	protected $strCategoryLevelDelimiter = ' / '; // Символ(ы) для разделения категорий разных уровней (пример: Авто / Оборудование / Магнитолы)
	
	# Other export settings	
	//protected $httpclient = null;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockId){
		
		$arResult = [];
		
		# General
		$arResult['HEADER_GENERAL'] = [];
		$arResult['sku'] = ['FIELD' => 'ID'];
		//$arResult['parent_sku'] = ['FIELD' => 'ID'];
		$arResult['title'] = ['FIELD' => 'NAME'];
		$arResult['brand'] = ['FIELD' => 'IBLOCK__NAME'];
		$arResult['category'] = ['FIELD' => 'IBLOCK__NAME'];
		$arResult['section'] = ['FIELD' => 'IBLOCK_SECTION_ID'];
		//$arResult['description'] = ['FIELD' => 'DETAIL_TEXT'];
		$arResult['brand'] = [];
		//$arResult['attributes'] = ['FIELD' => ['CATALOG_WEIGHT', 'CATALOG_BARCODE', 'PROPERTY_COLOR'], 'MULTIPLE' => true];
		//$arResult['images'] = ['FIELD' => 'PROPERTY_MORE_PHOTO'];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1'];
		//$arResult['sale_price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'];
		$arResult['quantity'] = ['FIELD' => 'CATALOG_QUANTITY'];
		$arResult['status'] = ['FIELD' => 'ACTIVE'];
		//$arResult['sale_start_date'] = ['FIELD' => 'ACTIVE_FROM'];
		//$arResult['sale_end_date'] = ['FIELD' => 'ACTIVE_TO'];
		//$arResult['variation'] = ['FIELD' => 'PROPERTY_COLOR_REF'];
		//$arResult['product_id'] = [];
		$arResult['currency'] = ['FIELD' => 'CATALOG_PURCHASING_CURRENCY'];
		$arResult['comment'] = '';
		$arResult['producer'] = '';
		$arResult['importer'] = '';
		$arResult['serviceCenters'] = '';
		$arResult['warranty'] = '';
		$arResult['deliveryTownTime'] = '';
		$arResult['deliveryCountryTime'] = '';
		$arResult['productLifeTime'] = '';
		$arResult['isCashless'] = '';
		$arResult['isCredit'] = '';
		$arResult['termHalva'] = '';
		$arResult['priceHalva'] = '';
		$arData = $this->getDataForFields($intIBlockId);
		
		foreach($arData as $arCategory){
			$arResult['HEADER_CATEGORY_'.$arCategory['CATEGORY_ID']] = [
				'NAME' => $this->formatCategoryName($arCategory['CATEGORY_ID'], $arCategory['NAME']),
				'NORMAL_CASE' => true,
			];
			foreach($arCategory['ATTRIBUTES'] as $arAttribute){				
				$strAttributeId = sprintf(static::ATTRIBUTE_ID, $arCategory['CATEGORY_ID'], $arAttribute['ATTRIBUTE_ID']);
				
				$arField = [
					'NAME' => $arAttribute['NAME'],
					'DISPLAY_CODE' => 'attribute_'.$arAttribute['ATTRIBUTE_ID'],
					'DESCRIPTION' => $this->descriptionToHint($arAttribute['DESCRIPTION']),
					'REQUIRED' => count($arData) == 1 && $arAttribute['IS_REQUIRED'] == 'Y' 
						|| $arAttribute['FORCE_REQUIRED'] == 'Y',
					'MULTIPLE' => false,
					'CUSTOM_REQUIRED' => $arAttribute['IS_REQUIRED'] == 'Y',
					'PARAMS' => [],
				];
				if($arAttribute['DICTIONARY_ID']){
					$arField['ALLOWED_VALUES_CUSTOM'] = true;
				}
				$this->guessDefaultValue($arField, $arAttribute);
				$arResult[$strAttributeId] = $arField;
			}
		}		
		
		return $arResult;
	}
	
	/**
	 *	Format category name with ID
	 */
	protected function formatCategoryName($intCategoryId, $strCategoryName=null){
		if(!strlen($strCategoryName) && $intCategoryId > 0){
			$strCategoryName = $this->getCategoryName($intCategoryId);
		}
		if(!is_numeric($intCategoryId) || !$intCategoryId){
			return $strCategoryName;
		}
		return sprintf('[%d] %s', $intCategoryId, $strCategoryName);
	}
	
	/**
	 *	Try to guess default value
	 */
	protected function guessDefaultValue(&$arField, $arAttribute){
		if($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_IMAGE){ // Izobrazhenie
			$arField['FIELD'] = ['DETAIL_PICTURE'];
			$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'multiple'];
			$arField['PARAMS'] = ['MULTIPLE' => 'multiple'];
		}
		elseif($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_IMAGES){ // Izobrazheniya
			$arField['FIELD'] = ['PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS', 'PROPERTY_PICS_NEWS'];
			$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'join', 'MULTIPLE_separator' => 'space'];
			$arField['PARAMS'] = ['MULTIPLE' => 'join', 'MULTIPLE_separator' => 'space'];
		}
		elseif($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_YOUTUBE_CODE){ // YouTube
			$arField['FIELD'] = ['PROPERTY_YOUTUBE', 'PROPERTY_VIDEO'];
			$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'multiple'];
			$arField['PARAMS'] = ['MULTIPLE' => 'multiple'];
		}
		elseif($arAttribute['ATTRIBUTE_ID'] == static::ATTR_ID_JSON_RICH_CONTENT){ // RichContent
			$arField['FIELD'] = [''];
			$arField['FIELD_PARAMS'] = ['HTMLSPECIALCHARS' => 'skip'];
			$arField['PARAMS'] = ['HTMLSPECIALCHARS' => 'skip'];
		}
		elseif($arAttribute['NAME'] == static::getMessage('GUESS_BRAND')){
			$arField['FIELD'] = ['PROPERTY_BRAND', 'PROPERTY_BRAND_REF'];
		}
		elseif($arAttribute['NAME'] == static::getMessage('GUESS_GROUP')){
			$arField['FIELD'] = ['ID'];
		}
	}
	
	/**
	 *	Convert simple description text to hint
	 */
	protected function descriptionToHint($strDescription){
		$strDescription = htmlspecialcharsbx($strDescription);
		$strPattern = '#((http|https|ftp|ftps)://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,6}(\/\S*)?)#';
		$strReplace = '<a href="$1" target="_blank">$1</a>';
		$strDescription = preg_replace($strPattern, $strReplace, $strDescription);
		$strDescription = nl2br($strDescription);
		return $strDescription;
	}
	
	/**
	 *	Get used categories from redefinitions
	 */
	protected function getUsedCategories($intIBlockId=null, $bJustIds=false){
		$arResult = [];
		$arIBlockParams = $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS'];		
		if($arIBlockParams['CATEGORIES_ALTERNATIVE'] == 'Y'){
			if(is_array($arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'])){
				foreach($arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'] as $intCategoryId){
					if(is_numeric($intCategoryId)){
						$arResult[$intCategoryId] = $this->getCategoryName($intCategoryId);
					}
				}
			}
		}
		else{
			$arRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', 
				[$this->intProfileId, $intIBlockId]);
			$arSelectedCategories = Exporter::getInvolvedSectionsID($intIBlockId, 
				$this->arProfile['IBLOCKS'][$intIBlockId]['SECTIONS_ID'], 
				$this->arProfile['IBLOCKS'][$intIBlockId]['SECTIONS_MODE']);
			$arUsedCategories = array_intersect_key($arRedefinitions, array_flip($arSelectedCategories));
			foreach($arUsedCategories as $strCategoryName){
				if(strlen($strCategoryName)){
					$strCategoryId = $this->parseCategoryId($strCategoryName);
					$arResult[$strCategoryId] = $strCategoryName;
				}
			}
		}
		if($bJustIds){
			$arResult = array_keys($arResult);
		}		
		return $arResult;
	}
	
	/**
	 *	Prepare fields data for getUniversalFields()
	 */
	protected function getDataForFields($intIBlockId){
		$arResult = [];
		$arCatalog = Helper::getCatalogArray($intIBlockId);
		$intMainIBlockId = is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] 
			? $arCatalog['PRODUCT_IBLOCK_ID'] : $intIBlockId;
		$arUsedCategories = $this->getUsedCategories($intMainIBlockId);
		$arUsedCategoriesId = array_keys($arUsedCategories);
		$arNotRequiredAttributes = $this->arProfile['IBLOCKS'][$intMainIBlockId]['PARAMS']['ATTRIBUTES_CANCEL_REQUIRED'];
		$arNotRequiredAttributes = Helper::explodeValues($arNotRequiredAttributes);
		$arNotRequiredAttributes = array_filter($arNotRequiredAttributes);
		if(!empty($arUsedCategoriesId)){
			$resCategories = Category::getList([
				'order' => ['NAME' => 'ASC'],
				'filter' => ['CATEGORY_ID' => $arUsedCategoriesId],
				'select' => ['ID', 'CATEGORY_ID', 'NAME'],
			]);
			while($arCategory = $resCategories->fetch()){
				$arCategory['ATTRIBUTES'] = [];
				$arResult[$arCategory['CATEGORY_ID']] = $arCategory;
			}			
			$resAttributes = Attribute::getList([
				'order' => ['NAME' => 'ASC'],
				'filter' => ['CATEGORY_ID' => $arUsedCategoriesId],
				'select' => ['ID', 'CATEGORY_ID', 'ATTRIBUTE_ID', 'DICTIONARY_ID', 'NAME', 'DESCRIPTION', 'TYPE', 
					'IS_COLLECTION', 'IS_REQUIRED', 'GROUP_ID', 'GROUP_NAME'],
			]);				
			while($arAttribute = $resAttributes->fetch()){
				if(in_array($arAttribute['ATTRIBUTE_ID'], $arNotRequiredAttributes)){
					$arAttribute['IS_REQUIRED'] = 'N';
				}
				$arResult[$arAttribute['CATEGORY_ID']]['ATTRIBUTES'][$arAttribute['ATTRIBUTE_ID']] = $arAttribute;
			}			
		}
		# Group
		if(!empty($arResult) && $this->isAttributesGrouped($intMainIBlockId)){
			
			# Find common attributes
			$arCommonAttributes = [];
			foreach($arResult as $arGroup){
				$arCommonAttributes[] = $arGroup['ATTRIBUTES'];
			}
			if(count($arCommonAttributes) > 1){
				$arCommonAttributes = call_user_func_array('array_intersect_key', $arCommonAttributes);
			}
			foreach($arCommonAttributes as $intAttrId => $arAttr){
				if(!$this->isAttributeDictionaryCommon($intAttrId)){
					unset($arCommonAttributes[$intAttrId]);
				}
			}
			# Check required
			foreach($arCommonAttributes as $intAttrId => $arAttr){
				foreach($arResult as $arGroup){
					if($arGroup['ATTRIBUTES'][$intAttrId]['IS_REQUIRED'] == 'Y'){
						$arCommonAttributes[$intAttrId]['IS_REQUIRED'] = 'Y';
						$arCommonAttributes[$intAttrId]['FORCE_REQUIRED'] = 'Y';
					}
				}
			}
			# Remove common attributes from categories
			if(!empty($arCommonAttributes)){
				foreach($arResult as $intGroupId => $arGroup){
					foreach($arCommonAttributes as $intAttrId => $arAttr){
						if(isset($arGroup['ATTRIBUTES'][$intAttrId])){
							unset($arResult[$intGroupId]['ATTRIBUTES'][$intAttrId]);
						}
					}
				}
			}
			# Create new virtual category
			if(!empty($arCommonAttributes)){
				$arResult = array_merge([
					static::GROUPED_CODE => [
						'ID' => false,
						'CATEGORY_ID' => static::GROUPED_CODE,
						'NAME' => static::getMessage('GROUPED_ATTRIBUTES_HEADER'),
						'ATTRIBUTES' => $arCommonAttributes,
					],
				], $arResult);
			}			
		}
		return $arResult;
	}
	
	/**
	 *	Check if attributes are grouped for iblock
	 */
	protected function isAttributesGrouped($intIBlockId){
		return $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS']['GROUP_ATTRIBUTES'] == 'Y';
	}

	/**
	 *	Handler for setProfileArray
	 */
	protected function onSetProfileArray(){
		
	}
	
	protected function getAddedApiCategories() {		
		$access_token = $this->getAccessToken();
		
		// HTTP-запрос			
		$obHttp = new \Bitrix\Main\Web\HttpClient();
		$obHttp->disableSslVerification();
		
		$obHttp->setHeader('Authorization', 'Bearer '.$access_token);
		$obHttp->setHeader('Accept', 'application/json');		
		
		//список категорий уже подключенных к магазину
		$strReq = '/sections?filter=enabled';			
		
		$req = $this->api_url.$strReq;			
		$strResponse = $obHttp->get($req);
		
		//$error = $obHttp->getError();
		$result2 = json_decode($strResponse, true);
		
		//file_put_contents(__DIR__.'/o_7.txt', var_export($result2, true));
			
		unset($obHttp);
		
		return $result2;
	}
	
	protected function addApiCategoryToShop($cat_ids_for_add){
		//file_put_contents(__DIR__.'/o_11.txt', var_export($cat_ids_for_add, true));

		$access_token = $this->getAccessToken();
		
		// HTTP-запрос			
		$obHttp = new \Bitrix\Main\Web\HttpClient();
		$obHttp->disableSslVerification();
		
		$obHttp->setHeader('Authorization', 'Bearer '.$access_token);
		$obHttp->setHeader('Accept', 'application/json');		
		
		//https://github.com/onlinerby/onliner-b2b-api/docs/shop/section/add.md
		foreach ( $cat_ids_for_add as $i => $cat_id )
		{
			$strReq = '/sections/'.$cat_id;			
			
			$req = $this->api_url.$strReq;			
			//$strResponse = $obHttp->get($req);
			$strResponse = $obHttp->post($req, $strReq);
			
			//$error = $obHttp->getError();
			$result = json_decode($strResponse, true);
			//file_put_contents(__DIR__.'/o_12.txt', var_export($result, true));
			//break; //test
		}		
			
		unset($obHttp);
	}
	
	/**
	 *	Handler for onRedefinitionCategoryAfterSave
	 */
	public function onRedefinitionCategoryAfterSave(&$arProfile, &$arCategoriesData){
		//file_put_contents(__DIR__.'/o_6.txt', var_export($arCategoriesData, true));
		
		$added_api_categories = $this->getAddedApiCategories();
		//return; //test
		
		$cat_ids = array();
		$not_added_cat_ids = array();
		foreach ( $arCategoriesData as $cat_i => $cat_name_data )
		{
			$cat_name_data_pre = explode(']', $cat_name_data);
			$cat_id = str_ireplace('[', '', $cat_name_data_pre[0]);
			$cat_ids[] = $cat_id;
			
			$cat_added = false;
			foreach ( $added_api_categories as $a_cat_id => $a_cat_name )
			{
				if ( $cat_id == $a_cat_id )
				{
					$cat_added = true;
					break;
				}
			}
			if ( $cat_id != '' && !in_array($cat_id, $not_added_cat_ids) && !$cat_added )
			{
				$not_added_cat_ids[] = $cat_id;
			}
		}
		//file_put_contents(__DIR__.'/o_10.txt', var_export($not_added_cat_ids, true));
		
		$this->addApiCategoryToShop($not_added_cat_ids);
	}
	
	/**
	 *	Export data by API (step-by-step if cron, or one step if manual)
	 */
	protected function stepExport_ExportApi(&$arSession, $arStep){
		$mResult = Exporter::RESULT_ERROR;
		if($this->bCron){
			do{
				$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
			}
			while($mResult === Exporter::RESULT_CONTINUE);
		}
		else{
			$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
		}
		return $mResult;
	}
	
	/**
	 *	1) получение по api списка доступных для выгрузки категорий
	 *	2) получение по api списка доступных для каждой выгружаемой и сопоставленной
	 *	категории атрибутов
	 *	3) получение по api списка доступных значений для каждого атрибута
	 */
	protected function onlinerApi_getApiDataBeforeUpload() {
		$this->onlinerApi_getAvailableUploadCategories();
		//$this->onlinerApi_getAvailableUploadAttributes();
		//$this->onlinerApi_getAvailableUploadAttributeValues();		
	}
	
	/**
	 *	"[17034083] Category 1 / Category 2 / Product name" => 17034083, &name => "Category 1 / Category 2 / Product name"
	 */
	protected function parseCategoryId(&$strCategoryName){
		if(strlen($strCategoryName) && preg_match('#^\[(\d+)\][\s]*(.*?)$#', $strCategoryName, $arMatch)){
			$strCategoryName = $arMatch[2];
			return $arMatch[1];
		}
		return false;
	}
	
	/**
	 *	3) получение по api списка доступных значений для каждого атрибута
	 */
	protected function onlinerApi_getAvailableUploadAttributeValues() {
		//$api_attribute_values_cache_file = $_SERVER["DOCUMENT_ROOT"].$this->cache_dir.'api_attribute_values.txt';
		$this->api_attribute_values = array();
		$request = Context::getCurrent()->getRequest();
		$profile_id = $this->intProfileId;
		//if ( !$this->arParams['USE_CACHE'] )
		if ( true )
		{			
			foreach ( $this->api_attributes as $cat => $cat_fields )
			{
				$cat_id = $this->getApiCategoryIdByCode($cat);
				foreach ( $cat_fields['Attribute'] as $atr_index => $atr_fields )
				{
					$atr_id = $atr_fields['Id'];
					if ( $atr_fields['AttributeType'] == 'multi_option' )
					{
						//file_put_contents(__DIR__.'/f25.txt', var_export($atr_fields['Options'], true), FILE_APPEND);
						
						foreach ( $atr_fields['Options']['Option'] as $atr_val_i => $atr_val_fields )
						{
							//file_put_contents(__DIR__.'/f26.txt', var_export($atr_val_fields, true), FILE_APPEND);
							
							$arFields = [
								'PROFILE_ID' => $profile_id,
								'CATEGORY_ID' => $cat_id,
								'ATTRIBUTE_ID' => $atr_id,
								'ATTRIBUTE_CODE' => $atr_id,
								'DICTIONARY_ID' => 1,
								'VALUE_ID' => $atr_val_fields['id'],
								'VALUE' => $atr_val_fields['Name'],
								'CODE' => $atr_val_fields['id'],
								'SESSION_ID' => session_id(),
								'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
							];
							
							$arFilter = [
								'PROFILE_ID' => $arFields['PROFILE_ID'],
								'CATEGORY_ID' => $arFields['CATEGORY_ID'],
								'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
								'VALUE_ID' => $arFields['VALUE_ID'],
								//'ATTRIBUTE_CODE' => $arFields['ATTRIBUTE_CODE'],
							];
							$resDBItem = AttributeValue::getList(['filter' => $arFilter, 'select' => ['ID']]);
							if($arDbItem = $resDBItem->fetch()){
								AttributeValue::update($arDbItem['ID'], $arFields);
							}
							else{
								AttributeValue::add($arFields);
							}
						}						
					}
					
				}
			}
			//$this->addToLog($this->api_attribute_values);
		}
		else {
			$this->api_attribute_values = array();
			
			$resDBItem = AttributeValue::getList(['filter' => [], 'select' => ['ID', 'CODE', 'VALUE', 'ATTRIBUTE_CODE']]);
			
			foreach ( $this->api_attributes as $cat => $cat_fields )
			{				
				$cat_code = $this->getApiCategoryCodeById($cat);
				foreach ( $cat_fields as $atr_i => $atr_fields )
				{
					while ( $arDbItem = $resDBItem->fetch() )
					{
						$rec = array('code' => $arDbItem['CODE'], 'name' => $arDbItem['VALUE']);
						$this->api_attribute_values[$cat_code][$arDbItem['ATTRIBUTE_CODE']][] = $rec;
					}
				}
			}
		}	
	}

	protected function getChildCategories($data)
	{
		if ( !isset($data) )
			return '';
	
		if ( is_string($data) )
			return '';
	
		if ( is_string($data['Children']['Category'][0]['Children']) )
			return '';		

		$this->addToLog($data['Children']['Category'][0]['Name']);
		$this->addToLog(is_array($data['Children']['Category'][0]['Children']));
		
		foreach ( $data['Children']['Category']['Children'] as $c_i => $c_values )
		{
			$this->addToLog($c_values['Name']);
			if ( stripos($c_values['Name'], $data['search_category_name']) !== false )
			{
				return $c_values['CategoryId'];
			}

			if ( is_array($c_values['Children']) )				
			{
				$this->addToLog(is_array($c_values['Children']));
				$this->addToLog($c_values['CategoryId']);				
				$data2 = array('Children' => $c_values['Children'], 'search_category_name' => $data['search_category_name'], 'Name' => $c_values['Name']);
				$result = $this->getChildCategories($data2);				
				if ( $result != '' )
					return $result;
			}

		}
		return '';
	}
	
	/**
	 *	получение code api категории по её name
	 *	
	 */
	protected function getApiCategoryCodeByName($category_name)
	{		
		foreach ( $this->arRedefinitions as $r_i => $r_val )
		{
			$category_hierarchy_data = explode(']', $r_val);
			$category_code = str_ireplace('[', '', $category_hierarchy_data[0]);
			$search_category_name = $category_hierarchy_data[1];
			
			if ( is_numeric($category_code) )
				return $category_code;
			
			foreach ( $this->api_categories['Category'] as $c_i => $c_values )
			{				
				if ( stripos($c_values['Name'], $search_category_name) !== false )
				{
					return $c_values['CategoryId'];
				}

				if ( is_array($c_values['Children']) )					
				{					
					$data = array('Children' => $c_values['Children'], 'search_category_name' => $search_category_name);
					$result = $this->getChildCategories($data);
					if ( $result != '' )
						return $result;
				}
			}			
			
		}
		return '';
	}
	
	/**
	 *	получение id api категории по её code
	 *	
	 */
	protected function getApiCategoryIdByCode($category_code)
	{		
		foreach ( $this->api_categories as $c_i => $c_values )
		{
			if ( $c_values['code'] == $category_code )
			{
				$cat_id = $c_i + 1;
				return $cat_id;
			}
		}
		
		return '';
	}
	
	/**
	 *	получение code api категории по её id
	 *	
	 */
	protected function getApiCategoryCodeById($category_id)
	{		
		foreach ( $this->api_categories as $c_i => $c_values )
		{
			$cat_id = $c_i + 1;
			if ( $cat_id == $category_id )
			{				
				return $c_values['code'];
			}
		}
		
		return '';
	}
	
	/**
	 *	получение id api атрибута по его code
	 *	
	 */
	protected function getApiAttributeIdByCode($category_code, $attribute_code)
	{		
		foreach ( $this->api_attributes[$category_code] as $c_i => $c_values )
		{
			if ( $c_values[0]['Id'] == $attribute_code )
			{
				$attr_id = $c_values[0]['Id'];
				return $attr_id;
			}
		}
		
		return '';
	}
	
	/**
	 *	2) получение по api списка доступных для каждой выгружаемой и сопоставленной
	 *	категории атрибутов
	 */
	protected function onlinerApi_getAvailableUploadAttributes() {		
		$arUsedCategories = array();
		foreach ( $this->arProfile['IBLOCKS'] as $intIBlockId => $block_data )
		{
			$arUsedCategories[$intIBlockId] = $this->getUsedCategories($intIBlockId, true);
		}
		$request = Context::getCurrent()->getRequest();
		$profile_id = $this->intProfileId;
		
		//if ( !$this->arParams['USE_CACHE'] )
		if ( true )
		{
			// HTTP-запрос
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			$obHttp->setHeaders([
			  'Accept: application/json',
			]);
			
			//$arCategoryAttributesRequested = array();
			$this->api_attributes = array();
			
			foreach ( $this->arProfile['IBLOCKS'] as $intIBlockId => $block_data )
			{
				if ( $arUsedCategories[$intIBlockId] !== '' )
				{
					$cat_id = $arUsedCategories[$intIBlockId][0];
					$cat_code = $cat_id;
					
					$strReq = 'Action=GetCategoryAttributes&Format=JSON&PrimaryCategory='.$cat_id.'&Timestamp='.rawurlencode(date(DATE_ATOM)).'&UserID='.rawurlencode($this->arProfile['PARAMS']['COMPANY_ID']).'&Version=1.0';
					$hash = hash_hmac('sha256', $strReq, $this->arProfile['PARAMS']['AUTH_TOKEN']);
					$strReq .= '&Signature='.$hash;
					
					//$req = $this->api_url.$this->arProfile['PARAMS']['API_REGION_URL'].'?'.$strReq;
					$req = $this->api_url.'?'.$strReq;
					$strResponse = $obHttp->get($req);
					$result = json_decode($strResponse, true);

					if ( isset($result['SuccessResponse']['Body']) )
					{				
						$this->api_attributes[$cat_code] = $result['SuccessResponse']['Body'];
						
						foreach ( $this->api_attributes[$cat_code] as $atr_index => $atr_fields )
						{
							foreach ( $atr_fields as $atr_fields_index => $atr_fields_data )
							{
								$multivalued = 'N';
								if ( $atr_fields_data['InputType'] == 'multiselect' )
									$multivalued = 'Y';
								
								$mandatory = 'N';
								
								$delete_no_mandatory_field = false;
								
								if ( $atr_fields_data['isMandatory'] == '1' )
									$mandatory = 'Y';
								else {
									$delete_no_mandatory_field = true;
								}
								
								$arFields = [
									'PROFILE_ID' => $profile_id,
									'CATEGORY_ID' => $cat_id,
									'ATTRIBUTE_ID' => $atr_fields_data['Id'],
									'DICTIONARY_ID' => 1,
									'NAME' => $atr_fields_data['Label'],
									'CODE' => $atr_fields_data['Name'],
									'DESCRIPTION' => $atr_fields_data['Description'],
									'TYPE' => $atr_fields_data['AttributeType'],
									'MULTIVALUED' => $multivalued,
									'IS_COLLECTION' => 'N',
									'IS_REQUIRED' => $mandatory,
									'SESSION_ID' => session_id(),
									'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
								];							
								
								$arFilter = [
									'PROFILE_ID' => $arFields['PROFILE_ID'],
									'CATEGORY_ID' => $arFields['CATEGORY_ID'],
									'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
								];
								$resDBItem = Attribute::getList(['filter' => $arFilter, 'select' => ['ID']]);
								
								if($arDbItem = $resDBItem->fetch()){
									if ( $delete_no_mandatory_field )
										Attribute::delete($arDbItem['ID']);
									else	
										Attribute::update($arDbItem['ID'], $arFields);
								}
								else{
									if ( !$delete_no_mandatory_field )
										Attribute::add($arFields);
								}								
							}
						}
					}
				}
			}
		}
		else {			
			$this->api_attributes = array();
			
			$resDBItem = Attribute::getList(['filter' => [], 'select' => ['ID', 'CODE', 'TYPE', 'MULTIVALUED', 'IS_REQUIRED']]);
			
			foreach ( $this->arProfile['IBLOCKS'] as $intIBlockId => $block_data )
			{
				if ( $arUsedCategories[$intIBlockId] !== '' )
				{
					$cat_id = $arUsedCategories[$intIBlockId][0] - 1;
					
					while ( $arDbItem = $resDBItem->fetch() )
					{
						$rec = array('code' => $arDbItem['CODE'], 'type' => $arDbItem['TYPE'], 'multiValued' => $arDbItem['MULTIVALUED'], 'mandatory' => $arDbItem['IS_REQUIRED']);
						$this->api_attributes[$cat_id][] = $rec;
					}
				}
			}
		}
	}
	
	/**
	 *	1) получение по api списка доступных для выгрузки категорий
	 *  1.1) добавление выбранных категорий к магазину посредством вызова метода app для api
	 *	см. https://github.com/onlinerby/onliner-b2b-api/docs/shop/section/add.md
	 */
	protected function onlinerApi_getAvailableUploadCategories() {		
		$access_token = $this->getAccessToken();
		//var_dump($access_token);	
		
		//return; //test
		$strResponse = '';
		
		//if ( !$this->arParams['USE_CACHE'] )
		if ( true )
		{
			// HTTP-запрос			
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			$obHttp->disableSslVerification();
			
			$obHttp->setHeader('Authorization', 'Bearer '.$access_token);
			$obHttp->setHeader('Accept', 'application/json');			
			
			//получение списка всех категорий
			$strReq = '/sections';			
			
			$req = $this->api_url.$strReq;			
			$strResponse = $obHttp->get($req);
			
			//$error = $obHttp->getError();
			$result = json_decode($strResponse, true);
			
			//список категорий уже подключенных к магазину
			$strReq = '/sections?filter=enabled';			
			
			$req = $this->api_url.$strReq;			
			$strResponse = $obHttp->get($req);
			
			//$error = $obHttp->getError();
			$result2 = json_decode($strResponse, true);
			
			//file_put_contents(__DIR__.'/o_3.txt', var_export($result2, true));
			
			unset($obHttp);
			
			//test
			//file_put_contents(__DIR__.'/o_1.txt', var_export($result, true));
			//return;
			//test
			
			$this->api_categories = null;
				
			if ( isset($result) && count($result) > 0 )	
				$this->api_categories = $result;
			
			$strSessionId = session_id();
			$arName = array();

			//$root_categories_count = 0;
			//foreach( $this->api_categories as $cat_index => $cat_data){
			foreach( $this->api_categories as $cat_id => $cat_name ) {
				//$root_categories_count++;
				//foreach( $cat_data as $cat_d_index => $cat_d_fields){
					$category_id = $cat_id;//$cat_d_fields['CategoryId'];
					
					//$arNameChain = array_merge($arName, [$category_id => $cat_d_fields['Name']]);
					$arNameChain = array_merge($arName, [$category_id => $cat_name]);
					/*
					if ( !empty($cat_d_fields['Children'] )) 
					{
						$this->processUpdatedCategory($cat_d_fields['children'], $strSessionId, $arNameChain, true);
					}
					*/
					$arFields = [
						'CATEGORY_ID' => $category_id,
						'NAME' => $cat_name, //$cat_d_fields['Name'],
						'CODE' => $category_id,
						'SESSION_ID' => session_id(),
						'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
					];
					$arFilter = [
						'CATEGORY_ID' => $arFields['CATEGORY_ID'],
					];
					$resDBItem = Category::getList(['filter' => $arFilter, 'select' => ['ID']]);
					if($arDbItem = $resDBItem->fetch()){
						Category::update($arDbItem['ID'], $arFields);
					}
					else{
						Category::add($arFields);
					}
				//}
			}
		}
		/* else 
		{
			$resDBItem = Category::getList(['filter' => [], 'select' => ['ID', 'CATEGORY_ID', 'NAME', 'CODE']]);
			$this->api_categories = array();
			
			while ( $arDbItem = $resDBItem->fetch() )
			{
				$rec = array('code' => $arDbItem['CODE'], 'title' => $arDbItem['NAME']);
				$id = $arDbItem['CATEGORY_ID'] - 2;
				$this->api_categories[$id] = $rec;
			}
		} */
	}
	
	protected function getAttrNameById($cat_id, $attr_id) {		
		foreach ( $this->api_attributes[$cat_id]['Attribute'] as $attr_i => $attr_fields )
		{
			if ( $attr_fields['Id'] == $attr_id )
			{
				return $attr_fields['Name'];
			}
		}
		
		return '';
	}
	
	/**
	 *	4) выполнение выгрузки
	 */
	protected function onlinerApi_DoAttributeUpload($arJsonItems){		
		$strResponse = '';		
		$arJson = array();
		$elements = array();
		$element_attributes = array();
		$element_index = 0;
		
		foreach($arJsonItems as $arItemIndex => $arItem)
		{
			$element = array();
			
			foreach( $arItem as $arItem_field => $arItem_field_value )
			{				
				//if ( stripos($arItem_field, 'attribute__') !== false )
				if ( stripos($arItem_field, 'attribute_') !== false )
				{
					//$attr_index_pre = explode('__', $arItem_field);
					$attr_index_pre = explode('_', $arItem_field);
					$cat_id = $attr_index_pre[1];
					$attr_id = $attr_index_pre[2];					
					$attribute_name = $this->getAttrNameById($cat_id, $attr_id);
					//$this->addToLog($attribute_name);
					//$element_attributes[$arItemIndex][] = array('code' => $attribute_code, 'value' => $arItem_field_value);
					$element_attributes[$arItemIndex][] = array('code' => $attribute_name, 'value' => $arItem_field_value);						
				}
				else {
					if ( stripos($arItem_field, 'attributes') !== false )
					{
						
					}
					else {
						if ( stripos($arItem_field, 'category_code') !== false )
						{
							
						}
						else {
							$element[$arItem_field] = $arItem_field_value;
						}
					}
				}
			}
			
			$elements[] = $element;
			$element_index++;
		}
		
		$element_index = 0;
		foreach($arJsonItems as $arItemIndex => $arItem)
		{
			foreach( $arItem as $arItem_field => $arItem_field_value )
			{
				if ( stripos($arItem_field, 'attribute__') !== false )
				{
					
				}
				else {
					if ( stripos($arItem_field, 'attributes') !== false )
					{
						$elements[$element_index][$arItem_field] = $element_attributes[$arItemIndex];
					}
					else {
						if ( stripos($arItem_field, 'category_code') !== false )
						{
							
						}
						else {
							if ( stripos($arItem_field, 'category') !== false )
							{								
								$arJsonItems[$arItemIndex]['category'] = $arJsonItems[$arItemIndex]['category_code'];
								$elements[$element_index][$arItem_field] = $arJsonItems[$arItemIndex]['category_code'];
							}
						}
					}
				}
			}
			$arJson[] = $elements[$element_index];
			$element_index++;
		}
		//if ( !$this->arParams['USE_CACHE'] )
		if ( true )
		{
			// HTTP-запрос
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			
			$obHttp->setHeaders([
			  'Accept: application/json',
			]);
			/*
			$strJson = \Bitrix\Main\Web\Json::encode($arJson);
			
			$obHttp->setHeader('Content-Type', 'text/plain');
$obHttp->setHeader('Accept', 'application/json');
$obHttp->setHeader('Content-Length', mb_strlen($strJson));
$obHttp->setHeader('X-Auth-Token', $this->arParams['AUTH_TOKEN']);
			*/			
			
			//выгрузка товаров
			//$post_data = $strJson;
			
			$product_data = '<?xml version="1.0" encoding="UTF-8" ?>
<Request>';
  /*
    <SellerSku>4105382173aaee4</SellerSku>
    <ParentSku/>
    <Status>active</Status>
    <Name>Magic Product</Name>
    <Variation>XXL</Variation>
    <PrimaryCategory>4</PrimaryCategory>
    <Categories>2,3,5</Categories>
    <Description><![CDATA[This is a <b>bold</b> product.]]></Description>
    <Brand>ASM</Brand>
    <Price>100.00</Price>
    <SalePrice>32.5</SalePrice>
    <SaleStartDate>2013-09-03T11:31:23+00:00</SaleStartDate>
    <SaleEndDate>2013-10-03T11:31:23+00:00</SaleEndDate>
    <ShipmentType>dropshipping</ShipmentType>
    <ProductId>xyzabc</ProductId>
    <ProductData>
      <Megapixels>490</Megapixels>
      <OpticalZoom>7</OpticalZoom>
      <SystemMemory>4</SystemMemory>
      <NumberCpus>32</NumberCpus>
      <Network>This is network</Network>
    </ProductData>
    <Quantity>10</Quantity>
  </Product>
  */
  
			foreach ( $arJson as $item_i => $item_fields )
			{
				//file_put_contents(__DIR__.'/f39.txt', var_export($item_fields, true));
				
				$product_status = 'active';
				if ( $item_fields['status'] != 'Y' )
				{
					$product_status = 'inactive';
				}
				
				$product_data .= 
'<Product><SellerSku>'.$item_fields['sku'].'</SellerSku>
<ParentSku/>
<Status>'.$product_status.'</Status>
<Name>'.$item_fields['title'].'</Name>
<Variation>'.$item_fields['variation'].'</Variation>
<PrimaryCategory>'.$item_fields['category'].'</PrimaryCategory>
<Categories></Categories>
<Description><![CDATA['.$item_fields['description'].']]></Description>
<Brand>'.$item_fields['brand'].'</Brand>
<Price>'.$item_fields['price'].'</Price>
<SalePrice>'.$item_fields['sell_price'].'</SalePrice>
<SaleStartDate>'.$item_fields['sale_start_date'].'</SaleStartDate>
<SaleEndDate>'.$item_fields['sale_end_date'].'</SaleEndDate>
<ShipmentType>dropshipping</ShipmentType>
<ProductId>'.$item_fields['product_id'].'</ProductId>';
	
				$product_data .= PHP_EOL.'<ProductData>'.PHP_EOL;
	
				foreach ( $item_fields['attributes'] as $attr_i => $attr_fields )
				{
					$product_data .= '<'.$attr_fields['code'].'>'.$attr_fields['value'].'</'.$attr_fields['code'].'>'.PHP_EOL;
				}
	
	$product_data .= 
'</ProductData>
<Quantity>'.$item_fields['quantity'].'</Quantity>
</Product>';
			}			
  
			$product_data .= '</Request>';
			
			//file_put_contents(__DIR__.'/f32.txt', var_export($product_data, true));
			
			$strReq = 'Action=ProductCreate&Format=JSON&Timestamp='.rawurlencode(date(DATE_ATOM)).'&UserID='.rawurlencode($this->arProfile['PARAMS']['COMPANY_ID']).'&Version=1.0';
			
			$hash = hash_hmac('sha256', $strReq, $this->arProfile['PARAMS']['AUTH_TOKEN']);
			$strReq .= '&Signature='.$hash;
			
			//$req = $this->api_url.$this->arProfile['PARAMS']['API_REGION_URL'].'?'.$strReq;
			$req = $this->api_url.'?'.$strReq;

			//return; //test
			
			$strResponse = $obHttp->post(				
				$req, $product_data);
			
			//file_put_contents(__DIR__.'/f31.txt', var_export($strResponse, true));
			
		}
		else {
			//$strResponse = file_get_contents($api_attribute_upload_result_cache_file);
		}
		
		$this->api_attribute_upload_result = json_decode($strResponse, true);
		$this->addToLog($strResponse);
	}
	
	
	
	protected function uploadProducts() {
		$this->onlinerApi_getApiDataBeforeUpload();
		
		$this->arRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', 
			[$this->intProfileId, $intIBlockId]);			

		$product_data = '[';
			
		$bExported = false;
		$arItems = $this->getExportDataItems(null, null);
		$obDatabase = \Bitrix\Main\Application::getConnection();
		$redefined_cat_names = [];
		$redefined_cat_codes = [];
		//$tmp_1 = [];
		//$products = [];
		if(!empty($arItems)){
			$arJsonItems = [];
			$arDataMore = [];
			
			foreach($arItems as $arItem){
				$arEncodedItem = Json::decode($arItem['DATA']);
				$arJsonItems[$arItem['ELEMENT_ID']] = $arEncodedItem;
				$arDataMore[$arItem['ELEMENT_ID']] = unserialize($arItem['DATA_MORE']);
				$redefined_category_name = $this->arRedefinitions[$arEncodedItem['section']];
				
				//$tmp_1[] = $redefined_category_name;
				//$products[] = $arItem;
				if ( $redefined_category_name != '' )
				{								
					$redefined_category_name_pre = explode('] ', $redefined_category_name);
					$redefined_cat_names[$arEncodedItem['section']] = $redefined_category_name_pre[1];
					
					$category_code = str_ireplace('[', '', $redefined_category_name_pre[0]);//$this->getApiCategoryCodeByName($redefined_category_name);
					$arJsonItems[$arItem['ELEMENT_ID']]['category_code'] = $category_code;
					$redefined_cat_codes[$arEncodedItem['section']] = $category_code;
				}
				
				$stock_status = 'in_stock';
				
				if ( $arEncodedItem['section'] != 'Y' )
					$stock_status = '';
				
				$product_data .= 
				'{
					 "category":"'.$redefined_category_name_pre[1].'",
					 "vendor":"'.$arEncodedItem['brand'].'",
					 "model":"'.$arEncodedItem['title'].'",
					 "article":"'.$arEncodedItem['sku'].'",
					 //"price":"'.$arEncodedItem['sale_price'].'",
					 "price":"'.$arEncodedItem['price'].'",
					 "currency":"'.$arEncodedItem['currency'].'",
					 "comment":"'.$arEncodedItem['comment'].'",
					 "producer":"'.$arEncodedItem['producer'].'",
					 "importer":"'.$arEncodedItem['importer'].'",
					 "serviceCenters":"'.$arEncodedItem['serviceCenters'].'",
					 "warranty":"'.$arEncodedItem['warranty'].'",
					 "deliveryTownTime":'.$arEncodedItem['deliveryTownTime'].',
					 "deliveryCountryTime":'.$arEncodedItem['deliveryCountryTime'].',
					 "productLifeTime":'.$arEncodedItem['productLifeTime'].',
					 "isCashless":"'.$arEncodedItem['isCashless'].'",
					 "isCredit":"'.$arEncodedItem['isCredit'].'",
					 "stockStatus": "'.$stock_status.'",
					 "termHalva": '.$arEncodedItem['termHalva'].',
					 "priceHalva": "'.$arEncodedItem['priceHalva'].'",
					 "hasOnlinerPrime": "'.$arEncodedItem['hasOnlinerPrime'].'",
					 "courierDeliveryPrices": {
						 "region-1": {
							 "type": "custom",
							 "amount": "'.$arEncodedItem['quantity'].'"
						 }
					 }
				 },';
				
				
			}
			//$arItemsId = array_column($arJsonItems, 'offer_id');
			
			/*
			алгоритм выгрузки:
			
			1) получение по api списка доступных для выгрузки категорий
			1.2) сопоставление имеющихся на сайте категорий с доступными для выгрузки

			2) получение по api списка доступных для каждой выгружаемой и сопоставленной
			категории атрибутов
			2.2) сопоставление имеющихся атрибутов с доступными

			3) получение по api списка доступных значений для каждого атрибута
			3.2) сопоставление имеющихся значений атрибутов с доступными

			4) выполнение выгрузки

			5) проверка результата выгрузки средствами api

			6) ручная проверка результатов выгрузки
			*/
		}
		
		$product_data .= ']';	
		
		//file_put_contents(__DIR__.'/o_26.txt', var_export($product_data, true));		
		//file_put_contents(__DIR__.'/o_27.txt', var_export($products, true));
		
		//return; //test
		
		//$this->onlinerApi_DoAttributeUpload($arJsonItems);
		
		$access_token = $this->getAccessToken();
		
		//https://github.com/onlinerby/onliner-b2b-api/docs/price-lists/import/update.md

		/*
		$product_data = '[
             {
                 "category":"Кабели, адаптеры, разветвители",
                 "vendor":"Apple",
                 "model":"iPod nano 16Gb (7th generation) test",
                 "article":"ipod16gb",
                 "price":"20.16",
                 "currency":"BYN",
                 "comment":"Ваш комментарий",
                 "producer":"Foxconn,No.2,2nd Donghuan Road,10th Yousong Industrial District,Longhua,Baoan,Shenzhen City,Guangdong Province,China",
                 "importer":"ООО Музтрейд, г.Минск, ул. Кропоткина, 12\r\nООО Плеерсервис, г.Гомель, ул. Платонова, 16",
                 "serviceCenters":"ООО Музсервис, г.Минск, ул. П. Бровки, 5\r\nООО Плеерсервис, г.Гомель, ул. Платонова, 16",
                 "warranty":"12",
                 "deliveryTownTime":1,
                 "deliveryCountryTime":5,
                 "productLifeTime":36,
                 "isCashless":"нет",
                 "isCredit":"нет",
                 "stockStatus": "in_stock",
                 "termHalva": 3,
                 "priceHalva": "20.16",
                 "hasOnlinerPrime": "нет",
                 "courierDeliveryPrices": {
                     "region-1": {
                         "type": "custom",
                         "amount": "2.99"
                     }
                 }
             }
        ]';
		*/
		
		// HTTP-запрос			
		$obHttp = new \Bitrix\Main\Web\HttpClient();
		$obHttp->disableSslVerification();
		
		$obHttp->setHeader('Authorization', 'Bearer '.$access_token);
		$obHttp->setHeader('Accept', 'application/json');
		$obHttp->setHeader('Content-Type' ,'application/json');
		
		//
		$strReq = '/pricelists';			
		
		$req = $this->api_url.$strReq;
		//$strResponse = $obHttp->post($req, $product_data);
		$query_result = $obHttp->query($obHttp::HTTP_PATCH, $req, $product_data);
		
		$strResponse = $obHttp->getResult();
		
		//$error = $obHttp->getError();
		$result = json_decode($strResponse, true);
		
		$this->addToLog($strResponse);
		
		//file_put_contents(__DIR__.'/o_25.txt', var_export($strResponse, true));
	}
	
	protected function uploadProductStocks() {
		$arItems = $this->getExportDataItems(null, null);
		
		$obDatabase = \Bitrix\Main\Application::getConnection();
		if(!empty($arItems)){
			$arJsonItems = [];
			$arDataMore = [];
			
			foreach($arItems as $arItem){
				$arEncodedItem = Json::decode($arItem['DATA']);
				$arJsonItems[$arItem['ELEMENT_ID']] = $arEncodedItem;
				$arDataMore[$arItem['ELEMENT_ID']] = unserialize($arItem['DATA_MORE']);
				$redefined_category_name = $this->arRedefinitions[$arEncodedItem['category']];				
				$category_code = $this->getApiCategoryCodeByName($redefined_category_name);
				$arJsonItems[$arItem['ELEMENT_ID']]['category_code'] = $category_code;				
			}
			
			$obHttp = new \Bitrix\Main\Web\HttpClient();
			
			$obHttp->setHeaders([
			  'Accept: application/json',
			]);
			
			$product_data = '<?xml version="1.0" encoding="UTF-8" ?>
<Request>';

			foreach ( $arJsonItems as $item_i => $item_fields )
			{
				$product_data .= '<Product>';
				
				$product_status = 'active';
				if ( $item_fields['status'] == 'Y' )
				{
					$product_data .= '<SellerSku>'.$item_fields['sku'].'</SellerSku>';
	

					$product_data .= '<Quantity>'.$item_fields['quantity'].'</Quantity></Product>';
				}				
				
			}			
  
			$product_data .= '</Request>';
			
			//file_put_contents(__DIR__.'/f32.txt', var_export($product_data, true));
			
			$strReq = 'Action=ProductStockUpdate&Format=JSON&Timestamp='.rawurlencode(date(DATE_ATOM)).'&UserID='.rawurlencode($this->arProfile['PARAMS']['COMPANY_ID']).'&Version=1.0';
			
			$hash = hash_hmac('sha256', $strReq, $this->arProfile['PARAMS']['AUTH_TOKEN']);
			$strReq .= '&Signature='.$hash;
			
			//$req = $this->api_url.$this->arProfile['PARAMS']['API_REGION_URL'].'?'.$strReq;
			$req = $this->api_url.'?'.$strReq;

			//return; //test
			
			$strResponse = $obHttp->post(				
				$req, $product_data);
		
			$this->addToLog($strResponse);
		}
	}
	
	/**
	 *	Export data by API (one step)
	 */
	protected function stepExport_ExportApi_Step(&$arSession, $arStep){
		
		//if ( $this->arProfile['PARAMS']['API_UPDATE_STOCKS'] == 'Y' )
		//{
		//	$this->uploadProductStocks();
		//}
		//else {
			$this->uploadProducts();
		//}		
	}
	
	/**
	 *	Correct value types (for example, "vat": 0 => "vat": "0")
	 */
	protected function correctFieldTypes(&$arItem, $arElement, $arFields, $arElementSections, $arDataMore){
		$arStringFields = ['offer_id', 'price', 'old_price', 'premium_price', 'vat'];
		foreach($arStringFields as $strKey){
			if(array_key_exists($strKey, $arItem)){
				$arItem[$strKey] = strVal($arItem[$strKey]);
			}
		}
		$arFloatFields = ['depth', 'height', 'weight', 'width'];
		foreach($arFloatFields as $strKey){
			if(array_key_exists($strKey, $arItem)){
				$arItem[$strKey] = floatVal($arItem[$strKey]);
			}
		}
	}
	
	/**
	 *	Handler on generate json for single product
	 */
	protected function onUpBuildJson(&$arItem, &$arElement, &$arFields, &$arElementSections, &$arDataMore){
		$this->test_count++;
		$arIBlockParams = $this->getIBlockParams($arElement['IBLOCK_ID']);
		# Correct int/string types for height, depth, price, vat, ...
		$this->correctFieldTypes($arItem, $arElement, $arFields, $arElementSections, $arDataMore);
		#
		$arDataMore = [
			'OFFER_ID' => $arItem['offer_id'],
			'STOCK' => null, // General stock
		];
		# Transfer stock from main data to DATA_MORE
		if(is_numeric($arFields['stock'])){
			$arDataMore['STOCK'] = intVal($arFields['stock']);
		}
		unset($arItem['stock'], $arFields['stock']);
		$arUsedCategories = $this->getUsedCategories();
		
		foreach($arItem as $key => $value){
			if ( $key == 'sku')
			{
				//$arItem[$key] = 'B4BKM0153\001\AW2122_'.$value.' / '.$value;				
				$arItem[$key] = $value;
				//$sku_new_val = intval($value + time()) % 1111000000;
				//$arItem[$key] = $sku_new_val;
			}
		
			if ( $key == 'images')
			{
				$images_recs = explode(",", $value);
				$new_value = array();
				foreach ( $images_recs as $img )
				{
					$img_rec = array('url' => $img);
					$new_value[] = $img_rec;
				}
				$arItem[$key] = $new_value;
			}
			if ( $key == 'category')
			{
				//$this->addToLog('category: '.$arItem['category']);
			}
			if ( isset($arUsedCategories[$arElementSections[0]]['NAME']) && $arUsedCategories[$arElementSections[0]]['NAME'] != '' )
				$arItem['category'] = $arUsedCategories[$arElementSections[0]]['NAME'];
			if ( $key == 'attributes')
			{
				//
			}
			
		}
	}	
	
	/**
	 *	Settings
	 */
	
	protected function onUpShowSettings(&$arSettings){		
		$arSettings['AUTH_TOKEN'] = $this->includeHtml(__DIR__.'/include/settings/auth_token.php');
		$arSettings['COMPANY_ID'] = $this->includeHtml(__DIR__.'/include/settings/company_id.php');
	}
	
	/**
	 *	Custom block in subtab 'Categories'
	 */
	public function categoriesCustomActions($intIBlockID, $arIBlockParams){
		return $this->includeHtml(__DIR__.'/include/attribute_update/settings.php', [
			'IBLOCK_ID' => $intIBlockID,
			'IBLOCK_PARAMS' => $arIBlockParams,
		]);
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'category_attributes_update': {
				$this->ajaxUpdateCategories($arParams, $arJsonResult);
				break;
			}
			
			case 'check_access': {
				$this->checkAccess($arParams, $arJsonResult);
				break;
			}
		}
	}
	
	/**
	 *	Update category attributes and dictionaries
	 */	 
	protected function ajaxUpdateCategories($arParams, &$arJsonResult){
		$arSession = &$_SESSION['ACRIT_EXP_onlinerKZ_CAT_ATTR_UPDATE'];
		$arPost = &$arParams['POST'];
		$bStart = false;
		if($arPost['start'] == 'Y'){
			$bStart = true;
			$arJsonResult['Action'] = 'Start';
			$arSession = [
				'ID' => session_id(),
				'CATEGORIES' => $this->getUsedCategories($arPost['iblock_id'], true),
				'ATTRIBUTES' => [],
				'COUNT' => 0,
				'INDEX' => 0,
				'CATEGORY_ID' => false,
				'CATEGORY_NAME' => false,
				'ATTRIBUTE_ID' => false,
				'ATTRIBUTE_NAME' => false,
				'SUB_INDEX' => 0,
				'FORCED' => $arPost['force'] == 'Y',
				'JUST_ATTR' => $arPost['just_attr'] == 'Y',
				#'SUB_COUNT' => 0, // Unfortunately, ozon does not provide this information :(
			];
			
			$arSession['COUNT'] = count($arSession['CATEGORIES']);
			$arJsonResult['Continue'] = true;

			$this->onlinerApi_getApiDataBeforeUpload();
			$arUpdateResult = $this->updateAttributeValues($arSession['ID']);
			$intCategoryId = 1;
			$arAttributes = $this->updateCategoryAttributes($intCategoryId, $arSession['ID']);
		}
		else{
			
		}
	}
	
	/**
	 *	Update dictionary
	 *	@return true if process is not finished (by has_next)
	 */
	protected function updateAttributeValues($strSessionId){
		$arResult = [
			'LAST_ID' => false,
			'CONTINUE' => false,
			'COUNT_SUCCESS' => 0,
		];
		if ( isset($this->api_attributes) )
		{
			$attr_count = count($this->api_attributes);
			if ( $attr_count > 0 )
			{
				$strSaveData = '';
				//\Bitrix\Main\Application::getConnection()->startTransaction();
				$test_1 = array();
				foreach ($this->api_attributes as $arCatItemCode => $arItem){
					foreach ($arItem as $arItemIndex => $arItemFields){
						$api_cat_id = $this->getApiCategoryIdByCode($arCatItemCode);
						$api_attr_id = $this->getApiAttributeIdByCode($arCatItemCode, $arItemFields['code']);
						$arFields = [
							'CATEGORY_ID' => $api_cat_id,
							'ATTRIBUTE_ID' => $api_attr_id,
							'DICTIONARY_ID' => 1,
							'VALUE_ID' => 1,
							'VALUE' => '',
							'SESSION_ID' => $strSessionId,
							'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
						];
						$arFilter = [
							'CATEGORY_ID' => $arFields['CATEGORY_ID'],
							'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
							'DICTIONARY_ID' => 1,
							'VALUE_ID' => 1,
						];
						$resDBItem = AttributeValue::getList(['filter' => $arFilter, 'select' => ['ID', 'VALUE']]);
						$arDbItem = $resDBItem->fetch();
						//$test_1[] = $arDbItem;
						if($arDbItem){
							AttributeValue::update($arDbItem['ID'], $arFields);
						}
						else{
							AttributeValue::add($arFields);
						}
						
						$arResult['LAST_ID'] = $arItem['id'];
						$arResult['COUNT_SUCCESS']++;
					}
				}
			}
		}
		
	}
	
	/**
	 *	Update attributes for categories
	 */
	protected function updateCategoryAttributes($arCategoryId, $strSessionId){
		if(!is_array($arCategoryId)){
			$arCategoryId = [$arCategoryId];
		}
		else{
			$arCategoryId = array_values($arCategoryId);
		}
		$arJsonRequest = [
			'attribute_type' => 'ALL',
			'category_id' => $arCategoryId,
			'language' => 'DEFAULT',
		];
		$arJsonResponse = [];
		$arJsonResponse['result'] = $this->api_attributes;
		$test_1 = [];
		if(is_array($arJsonResponse['result']) && !empty($arJsonResponse['result'])){
			$arResult = [];
			foreach($arJsonResponse['result'] as $arCategory){
				foreach($arCategory as $a_i => $arItem){
					$api_cat_id = -1;
					$api_attr_id = -1;
					foreach ($this->api_attributes as $arCatItemCode => $arItem_at){
						foreach ($arItem_at as $arItemIndex => $arItemFields){
							if ( $arItemFields['code'] == $arItem['code'] )
							{
								$api_cat_id = $this->getApiCategoryIdByCode($arCatItemCode);
								$api_attr_id = $this->getApiAttributeIdByCode($arCatItemCode, $arItem['code']);
								break 2;
							}
						}
					}
					$delete_no_mandatory_field = false;
					if ( $arItem['mandatory'] )
					{
						//
					}
					else {
						$delete_no_mandatory_field = true;
					}
					$arFields = [
						'CATEGORY_ID' => $api_cat_id,
						'ATTRIBUTE_ID' => $api_attr_id,
						'DICTIONARY_ID' => 1,
						'NAME' => $arItem['code'],
						'DESCRIPTION' => $arItem['code'],
						'TYPE' => $arItem['type'],
						'IS_COLLECTION' => 'N',
						'IS_REQUIRED' => 'Y',
						'GROUP_ID' => 1,
						'GROUP_NAME' => 1,
						'SESSION_ID' => $strSessionId,
						'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
					];
					$arFilter = [
						'CATEGORY_ID' => $api_cat_id,
						'ATTRIBUTE_ID' => $api_attr_id,
					];
					$arSelect = [
						'ID',
						'NAME',
						'LAST_VALUES_COUNT',
						'LAST_VALUES_DATETIME',
					];
					$resDBItem = Attribute::getList(['filter' => $arFilter, 'select' => $arSelect]);
					
					if ($arDbItem = $resDBItem->fetch()){
						if ( $delete_no_mandatory_field )
							Attribute::delete($arDbItem['ID']);
					}
					
					if($arDbItem = $resDBItem->fetch()){
						$test_1[] = $arDbItem;
						Attribute::update($arDbItem['ID'], $arFields);
						$arFields['LAST_VALUES_COUNT'] = $arDbItem['LAST_VALUES_COUNT'];
						if(is_object($arDbItem['LAST_VALUES_DATETIME'])){
							$bActual = microtime(true) - $arDbItem['LAST_VALUES_DATETIME']->getTimestamp() < static::CACHE_VALID_TIME;
							if($bActual){
								unset($arFields);
							}
						}
					}
					else{
						if ( !$delete_no_mandatory_field )
							Attribute::add($arFields);
					}
					
					if($arFields){
						$arResult[$arItem['id']] = $arFields;
					}
				}
			}
			Attribute::deleteByFilter([
				'CATEGORY_ID' => $arCategoryId,
				'!SESSION_ID' => $strSessionId,
			]);
			unset($arJsonResponse['result']);
			return $arResult;
		}
		else{
			$this->addToLog(static::getMessage('ERROR_UPDATE_ATTRIBUTES', ['#CATEGORY_ID#' => implode(', ', $arCategoryId), '#ERROR#' => print_r($arJsonResponse, true)]));
			return false;
		}		
		return false;
	}
	
	/**
	 *	Get saved categories, if not exists - download it
	 */
	public function getCategoriesList($intProfileId){
		$intLastUpdateTime = $this->getCategoriesDate();
		if(!$intLastUpdateTime || $intLastUpdateTime <= time() - 24*60*60){
			$this->updateCategories($this->intProfileId);
		}
		$arResult = [];
		$resCategories = Category::getList(['order' => ['NAME' => 'ASC'], 'select' => ['CATEGORY_ID', 'NAME']]);
		while($arCategory = $resCategories->fetch()){
			$arResult[$arCategory['CATEGORY_ID']] = $arCategory['NAME'];
		}
		// Sort categories, but numerical (such a "18+") in the end of the list
		uasort($arResult, function($a, $b){
			if(is_numeric(substr($a, 0, 1)) xor is_numeric(substr($b, 0, 1))){
				return strnatcmp($a, $b) * -1;
			}
			return strnatcmp($a, $b);
		});
		array_walk($arResult, function(&$value, $key) {
			$value = $this->formatCategoryName($key, $value);
		});
		unset($resCategories, $arCategory);
		return $arResult;
	}
	
	/**
	 *	Convert categories tree to plain list (recursively)
	 */
	protected function processUpdatedCategory($arCategoriesCurrent, $strSessionId, $arName=[], $bRecurred=false){
		if(is_array($arCategoriesCurrent)){	
			foreach( $arCategoriesCurrent as $cat_index => $cat_data){				
				foreach( $cat_data as $cat_d_index => $cat_d_fields){
					$category_id = '';
					if ( isset($cat_d_fields['CategoryId']) )
						$category_id = $cat_d_fields['CategoryId'];
					if ( is_numeric($category_id) )
					{	
						$arNameChain = array_merge($arName, [$category_id => $cat_d_fields['Name']]);
						if(!empty($cat_d_fields['Children'])){
							$this->processUpdatedCategory($cat_d_fields['Children'], $strSessionId, $arNameChain, true);
						}
						else{
							$arFields = [
								'CATEGORY_ID' => $category_id,
								'NAME' => $cat_d_fields['Name'],
								'SESSION_ID' => session_id(),
								'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
							];
							$arFilter = [
								'CATEGORY_ID' => $arFields['CATEGORY_ID'],
							];
							$resDBItem = Category::getList(['filter' => $arFilter, 'select' => ['ID']]);
							if($arDbItem = $resDBItem->fetch()){
								Category::update($arDbItem['ID'], $arFields);
							}
							else{
								Category::add($arFields);
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 *	Update categories from server using API
	 */
	public function updateCategories($intProfileId){
		$this->onlinerApi_getAvailableUploadCategories();
		$arJsonResponse = array();
		$arJsonResponse['result'] = $this->api_categories;
		
		$strSessionId = session_id();
		if ( true ) //test
		{
			$this->processUpdatedCategory($arJsonResponse['result'], $strSessionId);
		}
		else{
			$strLogMessage = static::getMessage('ERROR_CATEGORIES_EMPTY_ANSWER', ['#URL#' => $strCommand]);
			$this->addToLog($strLogMessage);
		}
		Category::deleteByFilter([
			'!SESSION_ID' => $strSessionId,
		]);
		return true;
	}
	
	/**
	 *	get access token
	*/
	protected function getAccessToken() {
		$strApiKey = $this->arProfile['PARAMS']['AUTH_TOKEN'];
		$strClientId = $this->arProfile['PARAMS']['COMPANY_ID'];
		$access_token = '';
		
		$arFields =
		[
			'PROFILE_ID' => $this->intProfileId,
			'ACCESS_TOKEN' => '',
			'EXPIRES_IN' => 0,
			'TIME' => new \Bitrix\Main\Type\Datetime(),
		];
		
		$arFilter = ['PROFILE_ID' => $this->intProfileId,];
		$resDBItem = Accesstoken::getList(['filter' => $arFilter, 'select' => ['ID', 'ACCESS_TOKEN', 'EXPIRES_IN', 'TIME']]);
		
		$arDbItem = $resDBItem->fetch();		
		$get_new_access_token = false;		
		if ( $arDbItem['ACCESS_TOKEN'] == '' )
		{
			$get_new_access_token = true;
		}
		else {
			$cur_time = time();	
			
			$delta_time = $cur_time - $arDbItem['TIME']->getTimestamp();
			
			if ( $delta_time > $arDbItem['EXPIRES_IN'] )
			{
				$get_new_access_token = true;
			}
			else {
				$access_token = $arDbItem['ACCESS_TOKEN'];
			}
		}		
		
		$method = '';
		if ( $get_new_access_token )
		{
			$method = 'https://b2bapi.onliner.by/oauth/token';
			$strRequestUrl = $method;			
			
			$obHttp = new \Bitrix\Main\Web\HttpClient();			
			
			$obHttp->disableSslVerification();			
			$obHttp->setHeader('Authorization', 'Basic '.base64_encode($strClientId.":".$strApiKey));
			//$obHttp->setHeader('Authorization', 'Bearer '.base64_encode($strClientId.":".$strApiKey));
			$arData = 'grant_type=client_credentials';
			$strJsonRequest = $arData;
			$obHttp->setHeader('Accept', 'application/json');
			
			$strJsonResponse = $obHttp->post($strRequestUrl, $strJsonRequest);			
			
			unset($strJsonRequest);
			
			unset($obHttp);
			
			if ( $strJsonResponse != '' )
			{
				$strJsonResponce = Json::decode($strJsonResponse);				
				$arFields =
				[
					'PROFILE_ID' => $this->intProfileId,
					'ACCESS_TOKEN' => $strJsonResponce['access_token'],
					'EXPIRES_IN' => $strJsonResponce['expires_in'],
					'TIME' => new \Bitrix\Main\Type\Datetime(),
				];
				$access_token = $strJsonResponce['access_token'];
				
				if ( $arDbItem )
				{
					Accesstoken::update($arDbItem['ID'], $arFields);
				}
				else{
					Accesstoken::add($arFields);
				}
			
			}
		}
		return $access_token;
	}
	
	/**
	 *	Check clientId and apiKey (for info only)
	*/
	protected function checkAccess($arParams, &$arJsonResult){		
		$access_token = $this->getAccessToken();
		
		$arJsonResult['Success'] = false;		
		$arQueryResult['SuccessResponse'] = null;		
		
		if ( $access_token != '' )
		{
			$arQueryResult['SuccessResponse'] = 1;
		}
		
		if ( isset($arQueryResult['SuccessResponse']) ){
			$arJsonResult['Success'] = true;
			$arJsonResult['Message'] = static::getMessage('MESSAGE_CHECK_ACCESS_SUCCESS');
		}
		else{
			$arJsonResult['Message'] = static::getMessage('MESSAGE_CHECK_ACCESS_DENIED');
		}
	}
	
	/**
	 *	Include own classes and files
	 */
	public function includeClasses(){
		Helper::includeJsPopupHint();
		require_once __DIR__.'/include/classes/api.php';
		require_once __DIR__.'/include/classes/attribute.php';
		require_once __DIR__.'/include/classes/attributevalue.php';
		require_once __DIR__.'/include/classes/category.php';
		require_once __DIR__.'/include/classes/accesstoken.php';
		require_once __DIR__.'/include/db_table_create.php';
	}
}

?>