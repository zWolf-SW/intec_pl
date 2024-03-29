<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Lamoda.kz api';

$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';
$MESS[$strSName.'API_REGION_URL'] = 'url региона api';
	$MESS[$strSHint.'API_REGION_URL'] = 'Выберите url для вашего региона';	
$MESS[$strSName.'AUTH_TOKEN'] = 'Ключ API';
	$MESS[$strSHint.'AUTH_TOKEN'] = 'Ключ API';
	$MESS[$strLang.'API_KEY_CHECK'] = 'Проверить доступ';
$MESS[$strSName.'COMPANY_NAME'] = 'Название компании';
	$MESS[$strSHint.'COMPANY_NAME'] = 'Название вашей компании';
$MESS[$strSName.'COMPANY_ID'] = 'ID пользователя';
	$MESS[$strSHint.'COMPANY_ID'] = 'Идентификатор пользователя в системе lamoda.kz';
$MESS[$strSName.'USE_CACHE'] = 'Использовать кеш';
	$MESS[$strSHint.'USE_CACHE'] = 'Использовать кеш обращений по lamoda.kz api';
$MESS[$strSName.'API_UPDATE_STOCKS'] = 'обновить только остатки';
	$MESS[$strSHint.'API_UPDATE_STOCKS'] = 'обновить только остатки по складу';
$MESS[$strSName.'API_UPDATE_PRICES'] = 'обновить только цены';
	$MESS[$strSHint.'API_UPDATE_PRICES'] = 'обновить только цены';
// Fields: General

$MESS[$strName.'sku'] = 'Идентификатор';
	$MESS[$strHint.'sku'] = 'Идентификатор';	
$MESS[$strName.'model'] = 'Название';
	$MESS[$strHint.'model'] = 'Название товара';
$MESS[$strName.'title'] = 'Название';
	$MESS[$strHint.'title'] = 'Название товара';	
$MESS[$strName.'brand'] = 'Бренд';
	$MESS[$strHint.'brand'] = 'Производитель товара';
$MESS[$strName.'category'] = 'Категория';
	$MESS[$strHint.'category'] = 'Категория товара';
$MESS[$strName.'attributes'] = 'Характеристики';
	$MESS[$strHint.'attributes'] = 'Характеристики товара';
$MESS[$strName.'images'] = 'Изображения';
	$MESS[$strHint.'images'] = 'Изображения товара';	
$MESS[$strName.'description'] = 'Описание';
	$MESS[$strHint.'description'] = 'Описание товара';	
$MESS[$strName.'availabilities.availability@available'] = 'Доступность товара на пункте самовывоза';
	$MESS[$strHint.'availabilities.availability@available'] = 'Доступность товара на пункте самовывоза';
$MESS[$strName.'availabilities.availability@storeId'] = 'Идентификатор пункта самовывоза';
	$MESS[$strHint.'availabilities.availability@storeId'] = 'Идентификатор пункта самовывоза';
$MESS[$strName.'cityprices'] = 'Цена, включающая НДС по городу';
	$MESS[$strHint.'cityprices'] = 'Цена, включающая НДС по городу';
$MESS[$strName.'cityprices.cityprice@cityId'] = 'Идентификатор города';
	$MESS[$strHint.'cityprices.cityprice@cityId'] = 'Идентификатор города';
$MESS[$strName.'cityprices.cityprice'] = 'Цена, включающая НДС в городе';
	$MESS[$strHint.'cityprices.cityprice'] = 'Цена, включающая НДС в городе';	
$MESS[$strName.'price'] = 'Цена, включающая НДС';
	$MESS[$strHint.'price'] = 'Цена, включающая НДС';
$MESS[$strName.'sale_price'] = 'Цена, включающая скидку';
	$MESS[$strHint.'sale_price'] = 'Цена, включающая скидку';
$MESS[$strName.'quantity'] = 'Общий остаток';
	$MESS[$strHint.'quantity'] = 'Общий остаток';
$MESS[$strName.'status'] = 'Доступность к покупке';
	$MESS[$strHint.'status'] = 'Доступность к покупке';
$MESS[$strName.'sale_start_date'] = 'Дата начала активности';
	$MESS[$strHint.'sale_start_date'] = 'Дата начала активности';	
$MESS[$strName.'sale_end_date'] = 'Дата конца активности';
	$MESS[$strHint.'sale_end_date'] = 'Дата конца активности';	
$MESS[$strName.'variation'] = 'Разновидность товара';
	$MESS[$strHint.'variation'] = 'Разновидность товара';	
$MESS[$strName.'product_id'] = 'Универсальный код продукта UPC';
	$MESS[$strHint.'product_id'] = 'Универсальный код продукта UPC';		
$MESS[$strLang.'MESSAGE_CHECK_ACCESS_SUCCESS'] = 'Проверка успешна. Доступ разрешен.';
$MESS[$strLang.'MESSAGE_CHECK_ACCESS_DENIED'] = 'Указаны некорректные данные (ClientId и/или ApiKey).';	
	
?>