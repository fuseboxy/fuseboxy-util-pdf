<?php /*
<fusedoc>
	<io>
		<in>
			<structure name="$xfa">
				<string name="preview" />
				<string name="editLayout" />
			</structure>
			<object name="$bean" type="pdfdoc">
				<number name="id" />
			</object>
		</in>
		<out>
			<number name="docID" scope="url" oncondition="xfa.preview|xfa.editLayout" />
		</out>
	</io>
</fusedoc>
*/
// capture original output
ob_start();
include F::appPath('view/scaffold/row.php');
$doc = Util::phpQuery(ob_get_clean());


ob_start();
// layout button
if ( isset($xfa['editLayout']) ) :
	?><a 
		href="<?php echo F::url($xfa['editLayout'].'&docID='.$bean->id); ?>"
		class="btn btn-xs btn-primary"
		data-toggle="ajax-modal"
		data-target="#global-modal-xl"
	><i class="fa fa-file-pdf"></i> PDF Layout</a> <?php
endif;
// preview button
if ( isset($xfa['preview']) ) :
	?><a 
		href="<?php echo F::url($xfa['preview'].'&docID='.$bean->id); ?>"
		class="btn btn-xs btn-light b-1"
		target="_blank"
	><i class="fa fa-search"></i> Preview</a> <?php
endif;
$doc->find('.scaffold-btn-edit')->before(ob_get_clean());


// done!
echo $doc;