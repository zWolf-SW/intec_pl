<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    'NAME' => Loc::getMessage('C_INTEC_SEO_IBLOCKS_METADATA_LOADER_NAME'),
    'DESCRIPTION' => Loc::getMessage('C_INTEC_SEO_IBLOCKS_METADATA_LOADER_DESCRIPTION'),
    'CACHE_PATH' => 'Y',
    'SORT' => 1,
    'PATH' => [
        'ID' => Loc::getMessage('C_INTEC_SEO')
    ]
];