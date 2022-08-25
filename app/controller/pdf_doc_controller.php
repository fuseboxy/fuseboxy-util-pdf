<?php
F::redirect('auth&callback='.base64_encode($_SERVER['REQUEST_URI']), !Auth::user());
F::error('Forbidden', !Auth::userInRole('SUPER,ADMIN'));


// extra button
$xfa['editLayout'] = 'pdf_row';


// config
$scaffold = array_merge([
	'beanType' => 'pdfdoc',
	'editMode' => 'inline',
	'allowDelete' => Auth::userInRole('SUPER'),
	'layoutPath' => F::appPath('view/pdf_doc/layout.php'),
	'listOrder' => 'ORDER BY alias ',
	'listField' => array(
		'id' => '60',
		'alias' => '15%',
		'title|remark' => '40%',
	),
	'fieldConfig' => array(
		'alias' => array('required' => true),
		'title' => array('placeholder' => true, 'required' => true),
		'remark' => array('format' => 'textarea', 'style' => 'height: 5rem', 'placeholder' => true),
	),
	'scriptPath' => array(
		'row' => F::appPath('view/pdf_doc/row.php'),
	),
	'writeLog' => class_exists('Log'),
], $pdfDocScaffold ?? $pdf_doc_scaffold ?? []);


// run!
include F::appPath('controller/scaffold_controller.php');