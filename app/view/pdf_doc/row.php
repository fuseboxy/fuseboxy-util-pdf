<?php /*
<fusedoc>
	<io>
		<in>
			<string name="controller" scope="$fusebox" />
			<structure name="$xfa">
				<string name="editLayout" optional="yes" />
				<string name="makeCopy" optional="yes" />
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
// related rows
$rowCount = ORM::count('pdfrow', 'disabled = 0 AND pdfdoc_id = ? ', array($bean->id));
F::error(ORM::error(), $rowCount === false);


// capture original output
ob_start();
include F::appPath('view/scaffold/row.php');
$doc = Util::phpQuery(ob_get_clean());


ob_start();
// button : layout
if ( isset($xfa['editLayout']) ) :
	?><a 
		href="<?php echo F::url("{$xfa['editLayout']}&docID={$bean->id}"); ?>"
		class="btn btn-xs btn-primary"
		data-toggle="ajax-modal"
		data-target="#global-modal-xxl"
	><i class="fa fa-file-pdf"></i> PDF Layout <?php
		if ( $rowCount ) :
			?><small>(<?php echo $rowCount; ?>)</small><?php
		endif;
	?></a> <?php
endif;
// button : copy
if ( isset($xfa['makeCopy']) ) :
	?><a 
		href="<?php echo F::url("{$xfa['makeCopy']}&docID={$bean->id}"); ?>"
		class="btn btn-xs btn-outline-primary text-primary bg-light"
		data-toggle="ajax-load"
		data-mode="after"
		data-target="#<?php echo $fusebox->controller; ?>-header"
		data-confirm="Are you sure to copy [<?php echo $bean->alias; ?>]?"
	><i class="far fa-clone"></i> Make Copy</a> <?php
endif;
$doc->find('.scaffold-btn-edit')->before(ob_get_clean());


// done!
echo $doc;