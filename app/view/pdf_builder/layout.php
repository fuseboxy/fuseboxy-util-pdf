<?php
// breadcrumb
$arguments['breadcrumb'] = array('PDF Doc', $arguments['doc']);


// tab layout config
$tabLayout = array(
	'style' => 'tabs',
	'position' => 'left',
	'header' => 'PDF Docs',
	'nav' => array_merge(array_map(fn($item)  => [
		'name' => $item->alias,
		'url' => F::url("{$fusebox->controller}&doc={$item->alias}"),
		'active' => ( $arguments['doc'] == $item->alias ),
		'linkClass' => '',
	], ORM::get('pdfdoc', 'ORDER BY alias, id ')), array([
		'name' => '+ New Doc',
		'url' => F::url("{$fusebox->controller}&doc=~NEW~"),
		'active' => ( $arguments['doc'] == '~NEW~' ),
		'linkClass' => 'font-italic text-muted',
	])),
);


// tab layout
ob_start();
include F::appPath('view/tab/layout.php');
$layout['content'] = ob_get_clean();


// global layout
$layout['width'] = 'full';
include F::appPath('view/global/layout.php');