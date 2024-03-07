<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

/**
 * @global $APPLICATION
 */

$APPLICATION->SetTitle("Статьи");

?>
<?php $APPLICATION->IncludeComponent(
	"bitrix:news", 
	"news.1", 
	array(
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "71",
		"NEWS_COUNT" => "20",
		"USE_SEARCH" => "N",
		"FILTER" => "arrFilter",
		"SETTINGS_USE" => "Y",
		"SETTINGS_PROFILE" => "blog",
		"USE_RSS" => "N",
		"USE_RATING" => "N",
		"USE_CATEGORIES" => "N",
		"USE_REVIEW" => "N",
		"USE_FILTER" => "N",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "DESC",
		"CHECK_DATES" => "Y",
		"PROPERTY_TAGS" => "TAGS",
		"TAGS_USE" => "N",
		"TAGS_VARIABLE" => "tags",
		"TAGS_HEADER_SHOW" => "Y",
		"TAGS_HEADER_TEXT" => "Популярное сейчас",
		"TAGS_TEMPLATE" => "template.2",
		"TAGS_SECTION_SUBSECTIONS" => "Y",
		"TAGS_COUNT" => "Y",
		"TAGS_USED" => "Y",
		"TOP_USE" => "Y",
		"TOP_PAGES" => "all",
		"TOP_COUNT" => "5",
		"TOP_HEADER_SHOW" => "Y",
		"TOP_HEADER_TEXT" => "Самые читаемые",
		"TOP_DATE_SHOW" => "Y",
		"TOP_SORT_BY" => "ACTIVE_FROM",
		"TOP_ORDER_BY" => "DESC",
		"TOP_DATE_TYPE" => "DATE_ACTIVE_FROM",
		"TOP_LINK_USE" => "Y",
		"TOP_LINK_BLANK" => "Y",
		"TOP_TAGS_SHOW" => "Y",
		"TOP_TAGS_MODE" => "active",
		"SUBSCRIBE_USE" => "N",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/company/articles/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_TITLE" => "Y",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"USE_PERMISSIONS" => "N",
		"STRICT_SECTION_CHECK" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"LIST_ACTIVE_DATE_FORMAT" => "j M Y",
		"LIST_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "TAGS",
			1 => "ASSOCIATED",
			2 => "",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PANEL_SHOW" => "Y",
		"PANEL_VARIABLE" => "year",
		"PANEL_VIEW" => "1",
		"LIST_TEMPLATE" => "tile.2",
		"LIST_COLUMNS" => "3",
		"LIST_VIEW" => "big",
		"LIST_ROUNDED" => "Y",
		"LIST_LINK_BLANK" => "N",
		"LIST_PREVIEW_SHOW" => "Y",
		"LIST_PREVIEW_TRUNCATE_USE" => "Y",
		"LIST_PREVIEW_TRUNCATE_COUNT" => "30",
		"LIST_TAGS_SHOW" => "Y",
		"LIST_TAGS_MODE" => "active",
		"DISPLAY_NAME" => "Y",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"BROWSER_TITLE" => "-",
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DETAIL_ACTIVE_DATE_FORMAT" => "j M Y",
		"DETAIL_FIELD_CODE" => array(
			0 => "CODE",
			1 => "",
		),
		"DETAIL_PROPERTY_CODE" => array(
			0 => "TAGS",
			1 => "ASSOCIATED",
			2 => "LINK_GOODS",
			3 => "",
		),
		"DETAIL_TEMPLATE" => "default.1",
		"DETAIL_PROPERTY_ADDITIONAL_NEWS" => "ASSOCIATED",
		"DETAIL_DATE_SHOW" => "Y",
		"DETAIL_DATE_TYPE" => "DATE_ACTIVE_FROM",
		"DETAIL_PRINT_SHOW" => "Y",
		"DETAIL_PREVIEW_SHOW" => "Y",
		"DETAIL_IMAGE_SHOW" => "Y",
		"DETAIL_ADDITIONAL_NEWS_SHOW" => "Y",
		"DETAIL_ADDITIONAL_NEWS_HEADER_SHOW" => "Y",
		"DETAIL_ADDITIONAL_NEWS_HEADER_TEXT" => "Читайте также",
		"DETAIL_ADDITIONAL_NEWS_DATE_SHOW" => "Y",
		"DETAIL_ADDITIONAL_NEWS_LINK_USE" => "Y",
		"DETAIL_ADDITIONAL_NEWS_LINK_BLANK" => "Y",
		"DETAIL_ADDITIONAL_NEWS_COLUMNS" => "2",
		"DETAIL_ADDITIONAL_NEWS_SLIDER_LOOP" => "N",
		"DETAIL_ADDITIONAL_NEWS_SLIDER_AUTO_USE" => "N",
		"DETAIL_BUTTON_BACK_SHOW" => "Y",
		"DETAIL_BUTTON_SOCIAL_SHOW" => "N",
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
		"DETAIL_PAGER_TITLE" => "Страница",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_SHOW_ALL" => "Y",
		"DETAIL_TAGS_SHOW" => "Y",
		"DETAIL_TAGS_POSITION" => "top",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "Y",
		"SHOW_404" => "Y",
		"FILE_404" => "/404.php",
		"COMPONENT_TEMPLATE" => "news.1",
		"REGIONALITY_USE" => "N",
		"TOP_NAVIGATION_USE" => "N",
		"LIST_LAZYLOAD_USE" => "N",
		"LIST_DATE_SHOW" => "N",
		"DETAIL_LAZYLOAD_USE" => "N",
		"DETAIL_PROPERTY_ADDITIONAL_PRODUCTS" => "LINK_GOODS",
		"DETAIL_PROPERTY_ADDITIONAL_PRODUCTS_CATEGORIES" => "",
		"DETAIL_ANCHORS_USE" => "N",
		"DETAIL_ADDITIONAL_NEWS_NAVIGATION_USE" => "N",
		"DETAIL_ADDITIONAL_NEWS_LAZYLOAD_USE" => "N",
		"DETAIL_ADDITIONAL_NEWS_PREVIEW_SHOW" => "N",
		"DETAIL_ADDITIONAL_NEWS_PREVIEW_TRUNCATE_USE" => "N",
		"DETAIL_MICRODATA_TYPE" => "Article",
		"DETAIL_MICRODATA_AUTHOR" => "",
		"DETAIL_MICRODATA_PUBLISHER" => "",
		"DETAIL_LINKING_SHOW" => "N",
		"LAZYLOAD_USE" => "N",
		"MAP_VENDOR" => "google",
		"PROPERTY_ADDRESS" => "",
		"PROPERTY_PHONE" => "",
		"PROPERTY_EMAIL" => "",
		"PROPERTY_SCHEDULE" => "",
		"MAP_SHOW" => "N",
		"PHONE_SHOW" => "N",
		"SCHEDULE_SHOW" => "N",
		"NUM_NEWS" => "",
		"NUM_DAYS" => "",
		"YANDEX" => "",
		"MAX_VOTE" => "",
		"VOTE_NAMES" => "",
		"CATEGORY_IBLOCK" => "",
		"CATEGORY_CODE" => "",
		"CATEGORY_ITEMS_COUNT" => "",
		"MESSAGES_PER_PAGE" => "",
		"USE_CAPTCHA" => "",
		"REVIEW_AJAX_POST" => "",
		"PATH_TO_SMILE" => "",
		"FORUM_ID" => "",
		"URL_TEMPLATES_READ" => "",
		"SHOW_LINK_TO_FORUM" => "",
		"FILTER_NAME" => "",
		"FILTER_FIELD_CODE" => "",
		"FILTER_PROPERTY_CODE" => "",
		"PROPERTY_VIDEO" => "",
		"PROPERTY_DOCUMENT" => "",
		"PROPERTY_SERVICES" => "",
		"PROPERTY_CASES" => "",
		"PROPERTY_PERSON_NAME" => "",
		"PROPERTY_PERSON_POSITION" => "",
		"PROPERTY_SITE_URL" => "",
		"FORM_SHOW" => "N",
		"DETAIL_TITLE_SHOW" => "N",
		"DETAIL_ADDITIONAL_PRODUCTS_SHOW" => "N",
		"PROPERTY_DATE_END" => "",
		"PROPERTY_DISCOUNT" => "",
		"PROPERTY_DURATION" => "",
		"DETAIL_BANNER_WIDE" => "N",
		"DETAIL_BANNER_HEIGHT" => "",
		"DETAIL_DESCRIPTION_PROPERTY_DURATION" => "",
		"DETAIL_PROMO_PROPERTY_ELEMENTS" => "LINK_GOODS",
		"DETAIL_PROMO_IBLOCK_TYPE" => "",
		"DETAIL_PROMO_IBLOCK_ID" => "",
		"DETAIL_CONDITIONS_PROPERTY_ELEMENTS" => "",
		"DETAIL_CONDITIONS_HEADER" => "Условия акции",
		"DETAIL_CONDITIONS_HEADER_POSITION" => "left",
		"DETAIL_CONDITIONS_IBLOCK_TYPE" => "",
		"DETAIL_CONDITIONS_IBLOCK_ID" => "",
		"DETAIL_CONDITIONS_COLUMNS" => "5",
		"DETAIL_FORM_SHOW" => "N",
		"DETAIL_VIDEOS_PROPERTY_ELEMENTS" => "",
		"DETAIL_VIDEOS_HEADER" => "Обзоры",
		"DETAIL_VIDEOS_HEADER_POSITION" => "left",
		"DETAIL_VIDEOS_IBLOCK_TYPE" => "",
		"DETAIL_VIDEOS_IBLOCK_ID" => "",
		"DETAIL_VIDEOS_PROPERTY_URL" => "",
		"DETAIL_VIDEOS_COLUMNS" => "3",
		"DETAIL_GALLERY_PROPERTY_ELEMENTS" => "",
		"DETAIL_GALLERY_HEADER" => "Фотографии",
		"DETAIL_GALLERY_HEADER_POSITION" => "left",
		"DETAIL_GALLERY_IBLOCK_TYPE" => "",
		"DETAIL_GALLERY_IBLOCK_ID" => "",
		"DETAIL_GALLERY_LINE_COUNT" => "6",
		"DETAIL_GALLERY_WIDE" => "Y",
		"DETAIL_SECTIONS_PROPERTY_SECTIONS" => "",
		"DETAIL_SECTIONS_HEADER" => "Разделы каталога",
		"DETAIL_SECTIONS_HEADER_POSITION" => "left",
		"DETAIL_SECTIONS_IBLOCK_TYPE" => "",
		"DETAIL_SECTIONS_IBLOCK_ID" => "",
		"DETAIL_SECTIONS_LINE_COUNT" => "5",
		"DETAIL_SERVICES_PROPERTY_ELEMENTS" => "",
		"DETAIL_SERVICES_HEADER" => "Услуги по акции",
		"DETAIL_SERVICES_HEADER_POSITION" => "left",
		"DETAIL_SERVICES_IBLOCK_TYPE" => "",
		"DETAIL_SERVICES_IBLOCK_ID" => "",
		"DETAIL_SERVICES_COLUMNS" => "2",
		"DETAIL_SERVICES_LINK_USE" => "N",
		"DETAIL_SERVICES_INDENT_IMAGE_USE" => "N",
		"DETAIL_SERVICES_DESCRIPTION_USE" => "N",
		"DETAIL_SERVICES_FOOTER_SHOW" => "N",
		"DETAIL_PRODUCTS_PROPERTY_ELEMENTS" => "",
		"DETAIL_PRODUCTS_HEADER" => "Товары по акции",
		"DETAIL_PRODUCTS_HEADER_POSITION" => "left",
		"DETAIL_PRODUCTS_TEMPLATE" => "",
		"DETAIL_PRODUCTS_USE_COMPARE" => "N",
		"DETAIL_PRODUCTS_COMPARE_NAME" => "compare",
		"DETAIL_LINKS_BUTTON" => "Посмотреть все акции",
		"DETAIL_LINKS_SOCIAL_SHOW" => "N",
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "",
			"detail" => "#CODE#/",
		)
	),
	false
); ?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php") ?>