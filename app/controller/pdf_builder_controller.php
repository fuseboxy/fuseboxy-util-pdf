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


	// determine row type (for field config)
	case 'edit':
		F::error('Argument [id] is required', empty($arguments['id']));
		$rowBean = ORM::get('pdfrow', $arguments['id']);
		F::error(ORM::error(), $rowBean === false);
		$arguments['rowType'] = $rowBean->type;
	case 'new':
		F::error('Argument [rowType] is required', empty($arguments['rowType']));
	// crud operations for PDF row
	default:
		$arguments['rowType'] = $arguments['rowType'] ?? '';
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
				'value|value_output',
				'align|size|color' => '160',
				'bold|italic|underline' => '120',
			),
			'fieldConfig' => array(
'id',
'url',
				'pdfdoc_id' => array(
					'label' => false,
					'value' => $arguments['docID'],
					'readonly' => true,
				),
				'seq' => array(
					'format' => 'number',
				),
				'type' => array(
					'label' => false,
					'format' => 'hidden',
					'default' => $arguments['rowType'],
				),
				'value' => array(
					'label' => false,
					'inline-label' => '<strong class="d-inline-block" style="width: 50px;">'.strtoupper($arguments['rowType']).'</strong>',
					'format' => in_array($arguments['rowType'], ['div','p','small','ol','ul']) ? 'textarea' : 'text',
					'style' => in_array($arguments['rowType'], ['div','p','small','ol','ul']) ? 'height: 82px' : false,
/*
'pre-help' => '<div class="row op-50">
	<div class="col pr-0"><hr class="b-secondary" style="border-style: dashed;"></div>
	<div class="col-1 p-0 text-center text-muted"><sub><b>PAGEBREAK</b></sub></div>
	<div class="col pl-0"><hr class="b-secondary" style="border-style: dashed;"></div>
</div>',
*/
				),
				'value_output' => array(
					'label' => false,
					'format' => 'output',
					'value' => '',
				),
				'bold' => array(
					'label' => false,
					'format' => 'checkbox',
					'options' => array('1' => '<b>Bold</b>'),
				),
				'italic' => array(
					'label' => false,
					'format' => 'checkbox',
					'options' => array('1' => '<i>Italic</i>'),
				),
				'underline' => array(
					'label' => false,
					'format' => 'checkbox',
					'options' => array('1' => '<u>Underline</u>'),
				),
				'align' => array(
					'label' => false,
					'inline-label' => '<b class="d-inline-block text-center text-muted" style="width: 35px;">ALIGN</b>',
					'options' => array(
						'left' => 'Left',
						'right' => 'Right',
						'center' => 'Center',
						'justify' => 'Justify',
					),
				),
				'size' => array(
					'label' => false,
					'inline-label' => '<b class="d-inline-block text-center text-muted" style="width: 35px;">SIZE</b>',
					'options' => call_user_func(function(){
						$options = array();
						for ( $i=8; $i<=48; $i++ ) $options[$i] = $i;
						return $options;
					}),
				),
				'color' => array(
					'label' => false,
					'inline-label' => '<b class="d-inline-block text-center text-muted" style="width: 35px;">COLOR</b>',
				),
				'height',
				'width',
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