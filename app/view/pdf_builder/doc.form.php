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
		<h5 class="modal-title"><?php echo !empty($docBean->id) ? 'Edit Doc' : 'New Doc'; ?></h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</header>
	<div class="modal-body pt-0">
		<input type="hidden" name="data[id]" value ="<?php echo $docBean->id ?? ''; ?>" />
		<div class="row">
			<div class="form-group col-8">
				<label class="col-form-label col-form-label-sm text-muted"><sub><strong>TITLE</strong></sub></label>
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"><small class="fa fa-pen"></small></span>
					</div>
					<input 
						type="text"
						name="data[title]"
						class="form-control"
						value="<?php echo $docBean->title ?? ''; ?>"
						required
					/>
				</div>
			</div>
			<div class="form-group col-4 pl-0">
				<label class="col-form-label col-form-label-sm text-muted"><sub><strong>ALIAS</strong></sub></label>
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"><small class="fa fa-hashtag"></small></span>
					</div>
					<input 
						type="text"
						name="data[alias]"
						class="form-control"
						value="<?php echo $docBean->alias ?? ''; ?>"
						required
					/>
				</div>
			</div>
		</div>
		<div class="form-group mb-2">
			<textarea 
				name="data[body]"
				placeholder="Description"
				class="form-control form-control-sm"
				rows="5"
			><?php echo $docBean->body ?? ''; ?></textarea>
		</div>
	</div><!--/.modal-body-->
	<footer class="modal-footer">
		<div class="text-right w-100"><?php
			// delete button
			if ( isset($xfa['delete']) ) :
				?><a 
					href="<?php echo F::url($xfa['delete']); ?>"
					class="btn btn-outline-danger float-left"
					onclick="return confirm('You cannot undo this. Are you sure to delete?');"
				><i class="fa fa-exclamation-triangle"></i> Delete</a><?php
			endif;
			// close button
			?><button type="button" class="btn btn-link text-dark ml-1" data-dismiss="modal">Close</button><?php
			// save button
			if ( isset($xfa['submit']) ) :
				?><button type="submit" class="btn btn-primary ml-1">Save Changes</button><?php
			endif;
		?></div>
	</footer>
</form>