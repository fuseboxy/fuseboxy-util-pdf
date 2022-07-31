<?php
F::redirect('auth', !Auth::user());
F::error('Forbidden', !Auth::userInRole('SUPER,ADMIN'));


require_once dirname(dirname(dirname(__DIR__))).'/fuseboxy-util/app/model/Util_PDF.php';
require_once dirname(dirname(dirname(__DIR__))).'/fuseboxy-pdfbuilder/app/model/PDFDoc.php';
// run!
switch ( $fusebox->action ) :


	// crud operations of PDF doc
	case 'doc-edit':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		$docBean = PDFDoc::load($arguments['docID']);
		F::error(PDFDoc::error(), $docBean === false);
		$xfa['delete'] = "{$fusebox->controller}.doc-delete&docID={$arguments['docID']}";
		// *** no break ***
	case 'doc-new':
		$xfa['submit'] = "{$fusebox->controller}.doc-save";
		include dirname(__DIR__).'/view/pdf_builder/doc.form.php';
		break;
	case 'doc-save':
		F::error('No data submitted', empty($arguments['data']));
		$saved = PDFDoc::save($arguments['data']);
		F::error(PDFDoc::error(), $saved === false);
		F::redirect("{$fusebox->controller}&docID={$arguments['docID']}");
		break;
	case 'doc-delete':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		$deleted = PDFDoc::delete($arguments['docID']);
		F::error(PDFDoc::error(), $deleted === false);
		F::redirect($fusebox->controller);
		break;
	case 'doc-preview':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		$rendered = PDFDoc::render($arguments['docID']);
		F::error(PDFDoc::error(), $rendered === false);
		break;


	// crud operations of PDF row
	case 'new':
		F::error('Argument [rowType] is required', empty($arguments['rowType']));
	default:
		// create new blank doc when none available
		$docCount = ORM::count('pdfdoc');
		F::error(ORM::error(), $docCount === false);
		if ( !$docCount ) $saved = ORM::saveNew('pdfdoc', [ 'alias' => 'blank' ]);
		F::error(ORM::error(), !$docCount and $saved === false);
		// default document
		$arguments['docID'] = $arguments['docID'] ?? ORM::first('pdfdoc', 'ORDER BY alias, id ')->id;
		// load record
		$docBean = ORM::get('pdfdoc', $arguments['docID']);
		F::error(ORM::error(), $docBean === false);
		// header buttons
		if ( empty($docBean->disabled) ) {
			$xfa['editDoc'] = "{$fusebox->controller}.doc-edit&docID={$arguments['docID']}";
			$xfa['previewDoc'] = "{$fusebox->controller}.doc-preview&docID={$arguments['docID']}";
		} else {
			$xfa['enableDoc'] = "{$fusebox->controller}.doc-toggle&docID={$arguments['docID']}&disabled=0";
		}
		// config
		$scaffold = array(
			'beanType' => 'pdfrow',
			'editMode' => 'inline',
			'retainParam' => array('docID' => $arguments['docID']),
			'allowDelete' => Auth::userInRole('SUPER'),
			'layoutPath' => dirname(__DIR__).('/view/pdf_builder/layout.php'),
			'listFilter' => array('pdfdoc_id = ? ', array($arguments['docID'])),
			'listOrder' => 'ORDER BY IFNULL(seq, 9999) ASC ',
			'listField' => array(
				'id|pdfdoc_id' => '60',
				'seq|type' => '100',
				'value',
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
				'pdfdoc_id' => array('label' => false, 'value' => $arguments['docID'], 'readonly' => true),
				'type' => array('label' => false, 'default' => $arguments['rowType'] ?? '', 'readonly' => true),
				'value' => array(
					'label' => false,
					'inline-label' => '<strong class="d-inline-block" style="width: 50px;">'.strtoupper(call_user_func(function() use ($arguments){
						if ( !empty($arguments['rowType']) ) return $arguments['rowType'];
						if ( !empty($arguments['id']) ) return ORM::get('pdfrow', $arguments['id'])->type;
						return '';
					})).'</strong>',
				),
				'url',
				'align',
				'color',
				'size',
				'bold',
				'italic',
				'underline',
				'height',
				'width',
				'seq' => array('format' => 'number'),
			),
			'scriptPath' => array(
				'row' => dirname(__DIR__).('/view/pdf_builder/row.php'),
				'header' => dirname(__DIR__).('/view/pdf_builder/header.php'),
				'inline_edit' => dirname(__DIR__).('/view/pdf_builder/inline_edit.php'),
			),
			'writeLog' => class_exists('Log'),
		);
		// component
		include F::appPath('controller/scaffold_controller.php');


endswitch;