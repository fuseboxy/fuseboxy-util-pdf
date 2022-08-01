<?php /*
<fusedoc>
	<io>
		<in>
			<structure name="$xfa">
				<string name="editDoc" optional="yes" />
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
<div id="pdf-builder-doc-view" class="card alert-primary">
	<h5 class="card-header b-0"><?php
		// buttons
		?><div class="float-right mt-n1 mb-n1 mr-n2"><?php
			// edit
			if ( isset($xfa['editDoc']) ) :
				?><a 
					href="<?php echo F::url($xfa['editDoc']); ?>"
					class="btn btn-sm btn-light ml-2"
					data-toggle="ajax-modal"
					data-target="#global-modal"
				><i class="fa fa-pen"></i> Edit</a><?php
			endif;
			// preview
			if ( isset($xfa['previewDoc']) ) :
				?><a 
					href="<?php echo F::url($xfa['previewDoc']); ?>"
					class="btn btn-sm btn-primary ml-2"
					target="_blank"
				><i class="fa fa-search fa-fw"></i> Preview</a><?php
			endif;
		?></div><?php
		// title
		if ( !empty($docBean->title) ) echo $docBean->title;
	?></h5><?php
	if ( !empty($docBean->body) ) :
		?><div class="card-body bg-white rounded-bottom">
			<p class="small text-muted mb-0"><?php echo nl2br($docBean->body); ?></p>
		</div><?php
	endif;
?></div>