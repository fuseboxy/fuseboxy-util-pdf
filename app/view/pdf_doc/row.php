<?php /*
<fusedoc>
	<io>
		<in>
			<structure name="$xfa">
				<string name="editLayout" />
			</structure>
			<object name="$bean" type="pdfdoc">
				<number name="id" />
			</object>
		</in>
		<out>
			<number name="docID" scope="url" oncondition="xfa.editLayout" />
		</out>
	</io>
</fusedoc>
*/
// capture original output
ob_start();
include F::appPath('view/scaffold/row.php');
$doc = Util::phpQuery(ob_get_clean());


// layout button
if ( isset($xfa['editLayout']) ) :
	ob_start();
	?><a 
		href="<?php echo F::url($xfa['editLayout'].'&docID='.$bean->id); ?>"
		class="btn btn-xs btn-primary"
		data-toggle="ajax-modal"
		data-target="#global-modal-xxl"
	><i class="fa fa-file-pdf"></i> PDF Layout</a> <?php
	$doc->find('.scaffold-btn-edit')->before(ob_get_clean());
endif;


// done!
echo $doc;