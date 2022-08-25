<?php
F::redirect('auth&callback='.base64_encode($_SERVER['REQUEST_URI']), !Auth::user());
F::error('Forbidden', !Auth::userInRole('SUPER,ADMIN'));
F::error('Argument [docID] is required', empty($arguments['docID']));





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
$scaffold = array_merge([
	'beanType' => 'pdfrow',
	'editMode' => 'inline',
	'retainParam' => array('docID' => $arguments['docID']),
	'allowDelete' => Auth::userInRole('SUPER'),
	'layoutPath' => dirname(__DIR__).('/view/pdf_builder/layout.php'),
	'listFilter' => array('pdfdoc_id = ? ', array($arguments['docID'])),
	'listOrder' => 'ORDER BY IFNULL(seq, 9999) ASC ',
	'listField' => array_merge([
		'seq|id|pdfdoc_id' => '100',
		'value' => '60%',
	], in_array($arguments['rowType'], ['div','p','ul','ol']) ? [
		'align|size|color' => '160',
		'bold|italic|underline' => '120',
	] : ( in_array($arguments['rowType'], ['small','h1','h2','h3','h4','h5','h6']) ? [
		'align|color' => '160',
		'bold|italic|underline' => '120',
	] : ( in_array($arguments['rowType'], ['img']) ? [
		'align|height|width' => '160',
	] : [])), [
		'__empty__|type',
	]),
	'fieldConfig' => array_merge([
		'seq' => array('format' => 'number', 'label' => '<i class="fa fa-sort-amount-down-alt"></i>', 'default' => 0),
		'id' => array('format' => 'hidden', 'label' => false),
		'type' => array('format' => F::is('*.new,*.edit') ? 'hidden' : 'text', 'label' => false, 'default' => $arguments['rowType'] ),
		'pdfdoc_id' => array('format' => 'hidden', 'label' => false, 'value' => $arguments['docID']),
		'value' => array_merge([
			'label' => false,
		], in_array($arguments['rowType'], ['div','p','small','ol','ul']) ? [
			'inline-label' => '<strong class="d-inline-block" style="width: 50px;">'.strtoupper($arguments['rowType']).'</strong>',
			'format' => 'textarea',
			'style' => 'height: 82px',
		] : ( in_array($arguments['rowType'], ['hr']) ? [
			'format' => 'hidden',
			'help' => '<hr style="border: solid black; border-width: 1px 0 0 0;" />',
		] : ( in_array($arguments['rowType'], ['pagebreak']) ? [
			'format' => 'hidden',
			'help' => '
				<div class="row op-60">
					<div class="col pr-0"><hr class="b-secondary" style="border-style: dashed;" /></div>
					<div class="col-1 p-0 text-center text-muted small" style="line-height: 1.75rem;"><b>PAGEBREAK</b></div>
					<div class="col pl-0"><hr class="b-secondary" style="border-style: dashed;" /></div>
				</div>
			',
		] : ( in_array($arguments['rowType'], ['img']) ? [

		] : ( in_array($arguments['rowType'], ['br']) ? [
			'inline-label' => '<strong class="d-inline-block" style="width: 50px;">'.strtoupper($arguments['rowType']).'</strong>',
			'format' => 'dropdown',
			'options' => array_filter(range(0, 9)),
			'default' => 1,
		] : [
			'inline-label' => '<strong class="d-inline-block" style="width: 50px;">'.strtoupper($arguments['rowType']).'</strong>',
		]))))),
	], [
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
		'height' => array(
			'label' => false,
			'inline-label' => '<b class="d-inline-block text-center text-muted" style="width: 35px;">HEIGHT</b>',
		),
		'width' => array(
			'label' => false,
			'inline-label' => '<b class="d-inline-block text-center text-muted" style="width: 35px;">WIDTH</b>',
		),
'url',
		'__empty__' => array(
			'label' => false,
			'format' => 'output',
		),
	]),
	'scriptPath' => array(
		'row' => dirname(__DIR__).('/view/pdf_builder/row.php'),
		'list' => dirname(__DIR__).('/view/pdf_builder/list.php'),
		'header' => dirname(__DIR__).('/view/pdf_builder/header.php'),
		'inline_edit' => dirname(__DIR__).('/view/pdf_builder/inline_edit.php'),
	),
	'writeLog' => class_exists('Log'),
], $pdfBuilderScaffold ?? $pdf_builder_scaffold ?? []);
// component
include F::appPath('controller/scaffold_controller.php');








/*

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
		$scaffold = array_merge([
			'beanType' => 'pdfrow',
			'editMode' => 'inline',
			'retainParam' => array('docID' => $arguments['docID']),
			'allowDelete' => Auth::userInRole('SUPER'),
			'layoutPath' => dirname(__DIR__).('/view/pdf_builder/layout.php'),
			'listFilter' => array('pdfdoc_id = ? ', array($arguments['docID'])),
			'listOrder' => 'ORDER BY IFNULL(seq, 9999) ASC ',
			'listField' => array_merge([
				'seq|id|pdfdoc_id' => '100',
				'value' => '60%',
			], in_array($arguments['rowType'], ['div','p','ul','ol']) ? [
				'align|size|color' => '160',
				'bold|italic|underline' => '120',
			] : ( in_array($arguments['rowType'], ['small','h1','h2','h3','h4','h5','h6']) ? [
				'align|color' => '160',
				'bold|italic|underline' => '120',
			] : ( in_array($arguments['rowType'], ['img']) ? [
				'align|height|width' => '160',
			] : [])), [
				'__empty__|type',
			]),
			'fieldConfig' => array_merge([
				'seq' => array('format' => 'number', 'label' => '<i class="fa fa-sort-amount-down-alt"></i>', 'default' => 0),
				'id' => array('format' => 'hidden', 'label' => false),
				'type' => array('format' => F::is('*.new,*.edit') ? 'hidden' : 'text', 'label' => false, 'default' => $arguments['rowType'] ),
				'pdfdoc_id' => array('format' => 'hidden', 'label' => false, 'value' => $arguments['docID']),
				'value' => array_merge([
					'label' => false,
				], in_array($arguments['rowType'], ['div','p','small','ol','ul']) ? [
					'inline-label' => '<strong class="d-inline-block" style="width: 50px;">'.strtoupper($arguments['rowType']).'</strong>',
					'format' => 'textarea',
					'style' => 'height: 82px',
				] : ( in_array($arguments['rowType'], ['hr']) ? [
					'format' => 'hidden',
					'help' => '<hr style="border: solid black; border-width: 1px 0 0 0;" />',
				] : ( in_array($arguments['rowType'], ['pagebreak']) ? [
					'format' => 'hidden',
					'help' => '
						<div class="row op-60">
							<div class="col pr-0"><hr class="b-secondary" style="border-style: dashed;" /></div>
							<div class="col-1 p-0 text-center text-muted small" style="line-height: 1.75rem;"><b>PAGEBREAK</b></div>
							<div class="col pl-0"><hr class="b-secondary" style="border-style: dashed;" /></div>
						</div>
					',
				] : ( in_array($arguments['rowType'], ['img']) ? [

				] : ( in_array($arguments['rowType'], ['br']) ? [
					'inline-label' => '<strong class="d-inline-block" style="width: 50px;">'.strtoupper($arguments['rowType']).'</strong>',
					'format' => 'dropdown',
					'options' => array_filter(range(0, 9)),
					'default' => 1,
				] : [
					'inline-label' => '<strong class="d-inline-block" style="width: 50px;">'.strtoupper($arguments['rowType']).'</strong>',
				]))))),
			], [
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
				'height' => array(
					'label' => false,
					'inline-label' => '<b class="d-inline-block text-center text-muted" style="width: 35px;">HEIGHT</b>',
				),
				'width' => array(
					'label' => false,
					'inline-label' => '<b class="d-inline-block text-center text-muted" style="width: 35px;">WIDTH</b>',
				),
'url',
				'__empty__' => array(
					'label' => false,
					'format' => 'output',
				),
			]),
			'scriptPath' => array(
				'row' => dirname(__DIR__).('/view/pdf_builder/row.php'),
				'list' => dirname(__DIR__).('/view/pdf_builder/list.php'),
				'header' => dirname(__DIR__).('/view/pdf_builder/header.php'),
				'inline_edit' => dirname(__DIR__).('/view/pdf_builder/inline_edit.php'),
			),
			'writeLog' => class_exists('Log'),
		], $pdfBuilderScaffold ?? $pdf_builder_scaffold ?? []);
		// component
		include F::appPath('controller/scaffold_controller.php');


endswitch;


*/