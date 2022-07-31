<?php /*
<fusedoc>
	<io>
		<in>
			<object name="$docBean">
				<number name="id" />
				<string name="alias" />
			</object>
		</in>
		<out />
	</io>
</fusedoc>
*/
// breadcrumb
$arguments['breadcrumb'] = array('PDF Doc', $docBean->alias);


// tab layout config
$tabLayout = array(
	'style' => 'tabs',
	'position' => 'left',
	'header' => 'PDF Docs',
	'nav' => array_merge(array_map(fn($docItem)  => [
		'name' => $docItem->alias,
		'url' => F::url("{$fusebox->controller}&docID={$docItem->id}"),
		'active' => ( $arguments['docID'] == $docItem->id ),
		'class' => !empty($docItem->disabled) ? 'del' : false,
		'linkClass' => !empty($docItem->disabled) ? 'text-muted' : false,
	], ORM::get('pdfdoc', 'ORDER BY alias, id ')), array([
		'name' => '+ New Doc',
		'url' => F::url("{$fusebox->controller}.doc-new"),
		'linkClass' => 'font-italic text-muted',
		'linkAttr' => array(
			'data-toggle' => 'ajax-modal',
			'data-target' => '#global-modal-sm',
		),
	])),
);


// tab layout
ob_start();
include F::appPath('view/tab/layout.php');
$layout['content'] = ob_get_clean();


// global layout
$layout['width'] = 'full';
include F::appPath('view/global/layout.php');