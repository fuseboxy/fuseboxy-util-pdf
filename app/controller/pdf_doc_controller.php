<?php
F::redirect('auth&callback='.base64_encode($_SERVER['REQUEST_URI']), !Auth::user());
F::error('Forbidden', !Auth::userInRole('SUPER,ADMIN'));


// run!
switch ( $fusebox->action ) :


	// view as pdf
	case 'preview':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		$rendered = PDFDoc::render($arguments['docID']);
		F::error(PDFDoc::error(), $rendered === false);
		break;


	// duplicate doc & rows
	case 'duplicate':
		F::error('Argument [docID] is required', empty($arguments['docID']));
		$duplicated = PDFDoc::duplicate($arguments['docID']);
		F::error(PDFDoc::error(), $duplicated === false);
		F::redirect("{$fusebox->controller}.row&id={$duplicated->id}");
		break;


	// crud operations
	default:
		// extra button
		if ( F::is('*.index,*.row') ) {
			$xfa['editLayout'] = 'pdf_row';
			$xfa['makeCopy'] = "{$fusebox->controller}.duplicate";
		}
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
		// component
		include F::appPath('controller/scaffold_controller.php');


endswitch;