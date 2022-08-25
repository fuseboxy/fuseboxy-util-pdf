<?php /*
<fusedoc>
	<io>
		<in>
			<number name="docID" scope="$arguments" />
		</in>
		<out>
			<structure name="$modalLayout" comments="pass to {view/modal/layout.php}">
				<string name="title" />
			</structure>
		</out>
	</io>
</fusedoc>
*/
// modal title
$pdfDoc = ORM::get('pdfdoc', $arguments['docID']);
F::error(ORM::error(), $pdfDoc === false);
$modalLayout['title'] = $layout['title'] = '<i class="fa fa-file-pdf text-danger fa-lg mr-1"></i> '.$pdfDoc->title;

// display in modal (when necessary)
include F::appPath( F::ajaxRequest() ? 'view/modal/layout.php' : 'view/global/layout.php' );