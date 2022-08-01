<?php
F::redirect('auth', !Auth::user());
F::error('Forbidden', !Auth::userInRole('SUPER,ADMIN'));


require_once dirname(dirname(dirname(__DIR__))).'/fuseboxy-util/app/model/Util_PDF.php';
require_once dirname(dirname(dirname(__DIR__))).'/fuseboxy-pdfbuilder/app/model/PDFDoc.php';
// run!
switch ( $fusebox->action ) :


	// crud operations for PDF doc
	case 'doc_edit':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		$docBean = PDFDoc::load($arguments['docID']);
		F::error(PDFDoc::error(), $docBean === false);
		if ( Auth::userInRole('SUPER') ) $xfa['delete'] = "{$fusebox->controller}.doc_delete&docID={$arguments['docID']}";
		// *** no break ***
	case 'doc_new':
		$xfa['submit'] = "{$fusebox->controller}.doc_save";
		include dirname(__DIR__).'/view/pdf_builder/doc.form.php';
		break;
	case 'doc_save':
		F::error('No data submitted', empty($arguments['data']));
		$saved = PDFDoc::save($arguments['data']);
		F::error(PDFDoc::error(), $saved === false);
		F::redirect("{$fusebox->controller}&docID={$arguments['data']['id']}");
		break;
	case 'doc_delete':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		$deleted = PDFDoc::delete($arguments['docID']);
		F::error(PDFDoc::error(), $deleted === false);
		F::redirect($fusebox->controller);
		break;
	case 'doc_preview':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		$rendered = PDFDoc::render($arguments['docID']);
		F::error(PDFDoc::error(), $rendered === false);
		break;


	// crud operations for PDF row
	case 'new':
		F::error('Argument [rowType] is required', empty($arguments['rowType']));
	default:
		// default document
		$inited = PDFDoc::init();
		F::error(PDFDoc::error(), $inited);
		$arguments['docID'] = $arguments['docID'] ?? PDFDoc::first('id');
		// load record for header
		$docBean = ORM::get('pdfdoc', $arguments['docID']);
		F::error(ORM::error(), $docBean === false);
		// buttons for header
		$xfa['editDoc'] = "{$fusebox->controller}.doc_edit&docID={$arguments['docID']}";
		$xfa['previewDoc'] = "{$fusebox->controller}.doc_preview&docID={$arguments['docID']}";
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