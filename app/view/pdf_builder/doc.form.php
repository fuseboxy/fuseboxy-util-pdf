<?php /*
<fusedoc>
	<io>
		<in>
			<structure name="$xfa">
				<string name="submit" optional="yes" />
				<string name="delete" optional="yes" />
			</structure>
			<object name="$docBean" type="pdfdoc">
				<number name="id" />
				<string name="alias" />
				<string name="title" />
				<string name="body" />
				<boolean name="disabled" />
			</object>
		</in>
		<out>
			<structure name="data" scope="form">
				<number name="id" />
				<string name="alias" />
				<string name="title" />
				<string name="body" />
				<boolean name="disabled" />
			</structure>
		</out>
	</io>
</fusedoc>
*/ ?>
<form 
	id="pdf-builder-doc-form"
	class="modal-content"
	<?php if ( isset($xfa['submit']) ) : ?>
		method="post"
		action="<?php echo F::url($xfa['submit']); ?>"
	<?php else : ?>
		onsubmit="return false;"
	<?php endif; ?>
>
	<header class="modal-header">
		<h5 class="modal-title">New Doc</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</header>
	<div class="modal-body">
		<input type="hidden" name="data[id]" value ="<?php echo $docBean->id ?? ''; ?>" />
<?php /*
<div class="modal-content"><div>
	<header class="modal-header"></header><div class="modal-body"><div class="form-row"><label class="col-2 col-form-label col-form-label-sm text-right"><span>Alias</span></label><div class="col">
				<div class="row"><div class="scaffold-col col col-alias"><div class="scaffold-input form-group mb-1"><div class="input-group input-group-sm"><input type="text" class="form-control scaffold-input-text " name="data[alias]" value="">
 </div></div><!--/.form-group--></div></div><!--/.row-->
			</div><!--/.col-->
		</div><!--/.form-group--><div class="form-row"><label class="col-2 col-form-label col-form-label-sm text-right"><span>Title</span></label><div class="col">
				<div class="row"><div class="scaffold-col col col-title"><div class="scaffold-input form-group mb-1"><div class="input-group input-group-sm"><input type="text" class="form-control scaffold-input-text " name="data[title]" value="">
 </div></div><!--/.form-group--></div></div><!--/.row-->
			</div><!--/.col-->
		</div><!--/.form-group--><div class="form-row"><label class="col-2 col-form-label col-form-label-sm text-right"><span>Body</span></label><div class="col">
				<div class="row"><div class="scaffold-col col col-body"><div class="scaffold-input form-group mb-1"><div class="input-group input-group-sm"><input type="text" class="form-control scaffold-input-text " name="data[body]" value="">
 </div></div><!--/.form-group--></div></div><!--/.row-->
			</div><!--/.col-->
		</div><!--/.form-group--><div class="form-row"><label class="col-2 col-form-label col-form-label-sm text-right"><span>Disabled</span></label><div class="col">
				<div class="row"><div class="scaffold-col col col-disabled"><div class="scaffold-input form-group mb-1"><div class="input-group input-group-sm"><select class="custom-select " name="data[disabled]"><option value=""></option><option value="0" selected="">Enable</option><option value="1">Disable</option></select>
</div></div><!--/.form-group--></div></div><!--/.row-->
			</div><!--/.col-->
		</div><!--/.form-group--></div><footer class="modal-footer">
			<button type="button" class="btn btn-link text-dark scaffold-btn-close" data-dismiss="modal">Close</button></footer>
</form></div></div>
*/ ?>
	</div>
	<footer class="modal-footer"><?php
		// delete button
		if ( isset($xfa['delete']) ) :
/*
			?><a 
			    href="<?php echo F::url($xfa['deleteDoc']); ?>"
			    class="btn btn-sm btn-danger ml-1"
			    onclick="return confirm('You cannot undo this. Are you sure to delete?');"
			><i class="fa fa-exclamation-triangle"></i> Delete</a><?php
*/
		endif;
		// close button
		?><button type="button" class="btn btn-link text-dark" data-dismiss="modal">Close</button><?php
		// save button
		if ( isset($xfa['submit']) ) :
			?><button type="submit" class="btn btn-primary">Save Changes</button><?php
		endif;
	?></footer>
</form>