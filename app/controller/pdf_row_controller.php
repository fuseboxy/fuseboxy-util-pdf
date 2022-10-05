<?php
F::redirect('auth&callback='.base64_encode($_SERVER['REQUEST_URI']), !Auth::user());
F::error('Forbidden', !Auth::userInRole('SUPER,ADMIN'));
F::error('Argument [docID] is required', empty($arguments['docID']));


// run!
switch ( $fusebox->action ) :


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
		// force [rowType=img] when upload
		if ( F::is('*.upload_file') ) $arguments['rowType'] = 'img';
		// config
		$scaffold = array(
			'beanType' => 'pdfrow',
			'editMode' => 'inline',
			'retainParam' => array('docID' => $arguments['docID']),
			'allowDelete' => Auth::userInRole('SUPER'),
			'layoutPath' => F::appPath('view/pdf_row/layout.php'),
			'listFilter' => array('pdfdoc_id = ? ', array($arguments['docID'])),
			'listOrder' => 'ORDER BY IFNULL(seq, 9999) ASC ',
			'listField' => array_merge([
				'seq|id|pdfdoc_id' => '100',
			], in_array($arguments['rowType'], ['div','p']) ? [
				'value|url' => '60%',
				'align|size|color' => '160',
				'bold|italic|underline' => '120',
			] : ( in_array($arguments['rowType'], ['ul','ol']) ? [
				'value' => '60%',
				'align|size|color' => '160',
				'bold|italic|underline' => '120',
			] : ( in_array($arguments['rowType'], ['small','h1','h2','h3','h4','h5','h6']) ? [
				'value|url' => '60%',
				'align|color' => '160',
				'bold|italic|underline' => '120',
			] : ( in_array($arguments['rowType'], ['img']) ? [
				'value|url' => '60%',
				'align|height|width' => '160',
			] : [
				'value' => '60%',
			]))), [
				'_empty_|type',
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
					'format' => 'image',
					'filetype' => 'gif,jpeg,jpg,png',
					'filesize' => '2MB',
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
				'url' => array(
					'label' => false,
					'format' => 'url',
					'icon' => 'fa fa-link',
					'placeholder' => 'http://',
				),
				'_empty_' => array(
					'label' => false,
					'format' => 'output',
				),
			]),
			'scriptPath' => array(
				'row' => F::appPath('view/pdf_row/row.php'),
				'list' => F::appPath('view/pdf_row/list.php'),
				'header' => F::appPath('view/pdf_row/header.php'),
				'inline_edit' => F::appPath('view/pdf_row/inline_edit.php'),
			),
			'writeLog' => class_exists('Log'),
		);
		// component
		include F::appPath('controller/scaffold_controller.php');


endswitch;