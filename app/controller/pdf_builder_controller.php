<?php
F::redirect('auth', !Auth::user());
F::error('Forbidden', !Auth::userInRole('SUPER,ADMIN'));


// run!
switch ( $fusebox->action ) :


	// crud operations of PDF doc
	case 'doc-new':
		$xfa['submit'] = "{$fusebox->controller}.saveDoc";
		break;
	case 'doc-edit':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		$xfa['deleteDoc'] = "{$fusebox->controller}.doc-delete&docID={$arguments['docID']}";
		$xfa['disableDoc'] = "{$fusebox->controller}.doc-toggle&docID={$arguments['docID']}&disabled=1";
		break;
	case 'doc-toggle':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		F::error('Argument [disabled] is required', !isset($arguments['disabled']));
		break;
	case 'doc-save':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		F::error('No data submitted', empty($arguments['data']));
		break;
	case 'doc-delete':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		F::redirect($fusebox->controller);
		break;
	case 'doc-preview':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		$docBean = ORM::get('pdfdoc', $arguments['docID']);
		F::error(ORM::error(), $docBean === false);
		F::error("PDF Doc not found (docID={$arguments['docID']})", empty($docBean->id));
		$rendered = PDFDoc::render($docBean);
		F:error(PDFDoc::error(), $rendered === false);
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
				'seq' => '100',
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
				'pdfdoc_id' => array('label' => false, 'format' => 'hidden', 'value' => $arguments['docID']),
				'type' => array('label' => false, 'default' => $arguments['rowType'] ?? ''),
				'value' => array(
					'label' => false,
					'inline-label' => '<strong class="d-inline-block" style="width: 50px;">'.strtoupper( $arguments['rowType'] ?? '' ).'</strong>',
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