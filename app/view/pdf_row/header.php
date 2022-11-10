<?php /*
<fusedoc>
	<io>
		<in>
			<structure name="$xfa">
				<string name="new" />
			</structure>
		</in>
		<out>
			<string name="type" scope="url" oncondition="xfa.new" />
		</out>
	</io>
</fusedoc>
*/
// capture original output
ob_start();
include F::appPath('view/scaffold/header.php');
$doc = Util::phpQuery(ob_get_clean());


// button : new row
if ( isset($xfa['new']) ) :
	ob_start();
	// new row position
	$positions = array(
		'top' => [
			'btnText' => 'Add to Top',
			'dataMode' => 'after',
			'dataTarget' => '.scaffold-list > .scaffold-first-row',
		],
		'bottom' => [
			'btnText' => 'Add to Bottom',
			'dataMode' => 'before',
			'dataTarget' => '.scaffold-list > .scaffold-last-row',
		],
	);
	?><div id="btn-group-row-position" class="btn-group btn-group-xs" role="group"><?php
		foreach ( $positions as $posKey => $posItem ) :
			?><button 
				type="button"
				class="btn btn-outline-primary px-2 small <?php if ( $posKey == 'top' ) echo 'active'; ?>"
				data-position="<?php echo $posKey; ?>"
				data-mode="<?php echo $posItem['dataMode']; ?>"
				data-target="<?php echo $posItem['dataTarget']; ?>"
				onclick="$(this).addClass('active').siblings().removeClass('active');"
			><?php echo $posItem['btnText']; ?></button><?php
		endforeach;
	?></div><?php
	// different row types
	$types = array(
		['div','p','small'],
		['h1','h2','h3','h4','h5','h6'],
		['ul','ol'],
		['img'],
		['br','hr','pagebreak'],
	);
	foreach ( $types as $group ) :
		?><div id="btn-group-row-type" class="btn-group ml-2" role="group"><?php
			foreach ( $group as $rowType ) :
				?><a 
					href="<?php echo F::url($xfa['new'].'&rowType='.$rowType); ?>"
					class="btn btn-xs btn-light b-1"
					data-toggle="ajax-load"
					data-mode="after"
					data-overlay="none"
					onclick="
						// determine ajax-load [mode & target] according to [btn-row-position]
						var $btnRowPosition = $('#btn-group-row-position .btn.active');
						$(this).attr('data-mode', $btnRowPosition.attr('data-mode'));
						$(this).attr('data-target', $btnRowPosition.attr('data-target'));
					"
				><span class="px-2 mx-1"><?php echo $rowType; ?></span></a><?php
			endforeach; // foreach-type
		?></div><?php
	endforeach; // foreach-group
	// put into header
	$doc->find('th.col-button')->addClass('text-nowrap')->html(ob_get_clean());
endif;


// display
echo $doc;