<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/sidepanel.layout.bundle.css',
	'js' => 'dist/sidepanel.layout.bundle.js',
	'rel' => [
		'ui.fonts.opensans',
		'sidepanel',
		'main.core',
		'main.core.events',
		'ui.buttons',
		'ui.sidepanel.menu',
	],
	'skip_core' => false,
];
