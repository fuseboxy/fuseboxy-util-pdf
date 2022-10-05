<?php /*
<fusedoc>
	<io>
		<in>
			<structure name="$xfa">
				<string name="preview" />
			</structure>
			<number name="docID" scope="$arguments" />
		</in>
		<out>
			<number name="docID" scope="url" oncondition="xfa.preview" />
			<structure name="$modalLayout" comments="for modal layout">
				<string name="title" />
				<string name="footer" comments="show preview button" />
			</structure>
		</out>
	</io>
</fusedoc>
*/
// modal title
$pdfDoc = ORM::get('pdfdoc', $arguments['docID']);
F::error(ORM::error(), $pdfDoc === false);
$modalLayout['title'] = $layout['title'] = '<i class="fa fa-file-pdf text-danger fa-lg mr-1"></i> '.$pdfDoc->title;


// modal footer
ob_start();
include F::appPath('view/modal/layout.footer.php');
$modalLayout['footer'] = ob_get_clean();


// preview button
if ( isset($xfa['preview']) ) :
	$modalLayout['footer'] = Util::phpQuery($modalLayout['footer']);
	ob_start();
	?><a 
		href="<?php echo F::url($xfa['preview'].'&docID='.$bean->id); ?>"
		class="btn btn-light b-1 btn-preview"
		target="_blank"
	><i class="fa fa-search"></i> Preview</a> <?php
	$modalLayout['footer']->find('.btn-close')->after(ob_get_clean())->remove();
endif;


// display in modal (when necessary)
if ( F::ajaxRequest() ) include F::appPath('view/modal/layout.php');
else include F::appPath('view/global/layout.php');