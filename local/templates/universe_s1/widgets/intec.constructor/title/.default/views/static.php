<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\constructor\models\Build;
use intec\constructor\models\build\Template as BuildTemplate;
use intec\constructor\structure\widget\Template as WidgetTemplate;

global $APPLICATION;

/**
 * @var array $properties
 * @var Build $build
 * @var BuildTemplate $template
 * @var WidgetTemplate $this
 */

?>
<?
$url = $_SERVER['REQUEST_URI'];
$url = explode('?', $url);
$url = $url[0];
//echo $url;
?>
<?php if (!($url == "/company/")) :?>
<div class="intec-content">
    <div class="intec-content-wrapper">
        <h1 id="pagetitle">
            <? $APPLICATION->ShowTitle("header") ?>
        </h1>
    </div>
</div>
<?php endif ?>
