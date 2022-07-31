<?php /*
<fusedoc>
	<io>
		<in>
			<structure name="$xfa">
				<string name="editDoc" />
				<string name="enableDoc" />
				<string name="disableDoc" />
				<string name="deleteDoc" />
			</structure>
		</in>
		<out />
	</io>
</fusedoc>
*/ ?>
<div id="pdf-builder-doc-button"><?php
	// edit
	if ( isset($xfa['editDoc']) ) :
		?><a 
			href="<?php echo F::url($xfa['editDoc']); ?>"
			class="btn btn-sm btn-light b-1 ml-1"
			data-toggle="ajax-modal"
			data-target="#global-modal-sm"
		><i class="fa fa-pen"></i> Edit</a><?php
	endif;
	// enable
	if ( isset($xfa['enableDoc']) ) :
		?><a 
			href="<?php echo F::url($xfa['enableDoc']); ?>"
			class="btn btn-sm btn-success ml-1"
			data-toggle="ajax-load"
			data-target="#pdf-builder-doc-form"
		><i class="fa fa-undo"></i> Enable</a><?php
	// disable
	elseif ( isset($xfa['disableDoc']) ) :
		?><a 
			href="<?php echo F::url($xfa['disableDoc']); ?>"
			class="btn btn-sm btn-warning text-white ml-1"
			data-toggle="ajax-load"
			data-target="#pdf-builder-doc-form"
		><i class="far fa-trash-alt"></i> Disable</a><?php
	endif;
	// delete
	if ( isset($xfa['deleteDoc']) ) :
		?><a 
			href="<?php echo F::url($xfa['deleteDoc']); ?>"
			class="btn btn-sm btn-danger ml-1"
			onclick="return confirm('You cannot undo this. Are you sure to delete?');"
		><i class="fa fa-exclamation-triangle"></i> Delete</a><?php
	endif;
?></div>