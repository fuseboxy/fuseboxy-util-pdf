<?php /*
<fusedoc>
	<io>
		<in>
			<structure name="$xfa">
				<string name="editDoc" optional="yes" />
				<string name="enableDoc" optional="yes" />
				<string name="previewDoc" optional="yes" />
			</structure>
			<object name="$docBean" type="pdfdoc">
				<number name="id" />
				<string name="alias" />
				<string name="title" />
				<string name="body" />
			</object>
		</in>
		<out />
	</io>
</fusedoc>
*/ ?>
<div id="pdf-builder-doc-view" class="card card-light">
	<div class="card-body row">
		<div class="col-9"><?php
			if ( !empty($docBean->title) ) :
				?><h1><?php echo $docBean->title; ?></h1><?php
			endif;
			if ( !empty($docBean->body) ) :
				?><p><?php echo nl2br($docBean->body); ?></p><?php
			endif;
		?></div>
		<div class="col-3 text-right"><?php
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
			endif;
			// preview
			if ( isset($xfa['previewDoc']) ) :
				?><a 
					href="<?php echo F::url($xfa['previewDoc']); ?>"
					class="btn btn-sm btn-primary ml-1"
					target="_blank"
				><i class="fa fa-search fa-fw"></i></a><?php
			endif;
		?></div>
	</div>
</div>