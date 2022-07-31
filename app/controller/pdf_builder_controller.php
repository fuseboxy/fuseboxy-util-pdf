<?php /*
<fusedoc>
	<description>
		generate UI for pdf-builder CRUD operations
	</description>
	<io>
		<in>
			<structure name="$pdfBuilder" comments="config">
				<!-- essentials -->
				<string name="layoutPath" />
				<string_or_structure name="retainParam" />
				<!-- permissions -->
				<boolean name="allowNew" />
				<boolean name="allowEdit" />
				<boolean name="allowDelete" />
				<boolean name="allowToggle" />
				<boolean name="allowSort" />
				<!-- filter & order -->
				<structure name="listFilter">
					<string name="sql" />
					<array name="param" />
				</structure>
				<string name="listOrder" />
				<!-- others -->
				<boolean name="writeLog" />
			</structure>
		</in>
		<out>
		</out>
	</io>
</fusedoc>
*/
F::redirect('auth', !Auth::user());
F::error('Forbidden', !Auth::userInRole('SUPER,ADMIN'));


// create new blank doc when none available
$docCount = ORM::count('pdfdoc');
F::error(ORM::error(), $docCount === false);
if ( !$docCount ) $saved = ORM::saveNew('pdfdoc', [ 'alias' => 'blank' ]);
F::error(ORM::error(), !$docCount and $saved === false);


// default document
$arguments['doc'] = $arguments['doc'] ?? ORM::first('pdfdoc', 'ORDER BY alias, id ')->alias;


// config
$scaffold = array(
	'beanType' => 'pdfrow',
	'editMode' => 'inline',
	'retainParam' => array('doc' => $arguments['doc']),
	'allowDelete' => Auth::userInRole('SUPER'),
	'layoutPath' => dirname(__DIR__).('/view/pdf_builder/layout.php'),
/*
	'listFilter' => call_user_func(function($arguments){
		if ( isset($arguments['filterField']) and $arguments['filterField'] == 'remark' and !empty($arguments['filterValue']) ) {
			return array(" {$arguments['filterField']} LIKE ? ", array('%'.trim($arguments['filterValue']).'%'));
		} elseif ( isset($arguments['filterField']) and $arguments['filterField'] != 'remark' and isset($arguments['filterValue']) ) {
			return array(" IFNULL({$arguments['filterField']}, '') = ? ", array($arguments['filterValue']));
		} else {
			return false;
		}
	}, $arguments),
	'listOrder' => 'ORDER BY datetime DESC',
*/
	'listField' => array(
		'id' => '60',
/*
		'datetime' => '13%',
		'username|sim_user' => '13%',
		'action|ip' => '13%',
		'entity_type|entity_id' => '13%',
		'remark' => '30%',
*/
	),
	'fieldConfig' => array(
		'id',
/*
		'datetime' => array('label' => 'Date <small class="muted">/ Time</small>'),
		'username',
		'sim_user',
		'action',
		'entity_type' => array('label' => 'Entity'),
		'entity_id' => array('label' => false),
		'remark',
		'ip' => array('label' => 'IP'),
*/
	),
	'scriptPath' => array(
		'row' => dirname(__DIR__).('/view/pdf_builder/row.php'),
		'header' => dirname(__DIR__).('/view/pdf_builder/header.php'),
		'inline_edit' => dirname(__DIR__).('/view/pdf_builder/inline_edit.php'),
	),
	'writeLog' => class_exists('Log'),
);


// run!!
include F::appPath('controller/scaffold_controller.php');