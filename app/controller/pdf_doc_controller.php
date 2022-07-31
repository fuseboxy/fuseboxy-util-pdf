<?php
F::redirect('auth', !Auth::user());
F::error('Forbidden', !Auth::userInRole('SUPER,ADMIN'));


// run!
switch ( $fusebox->action ) :


	case 'index':
		break;


	// crud operations
	default:
		// config
		$scaffold = array(
			'beanType' => 'pdfdoc',
			'editMode' => 'classic',
			'allowDelete' => Auth::userInRole('SUPER'),
			'layoutPath' => dirname(__DIR__).('/view/pdf_builder/layout.php'),
			'fieldConfig' => array(
				'id',
				'alias',
				'title',
				'body',
			),
			'writeLog' => class_exists('Log'),
		);
		// component
		include F::appPath('controller/scaffold_controller.php');


endswitch;