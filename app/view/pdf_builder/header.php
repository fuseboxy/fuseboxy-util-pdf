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
// display PDF doc info
include 'doc.view.php';
echo '<br />';


// capture original output
ob_start();
include F::appPath('view/scaffold/header.php');
$doc = Util::phpQuery(ob_get_clean());


// custom set of new buttons
$all = array(
	['div','p','small'],
	['ul','ol'],
	['h1','h2','h3','h4','h5','h6'],
	['img'],
	['br','hr','pagebreak'],
);
ob_start();
if ( isset($xfa['new']) ) :
	foreach ( $all as $group ) :
		?><div class="btn-group ml-2"><?php
			foreach ( $group as $rowType ) :
				?><a 
					href="<?php echo F::url($xfa['new'].'&rowType='.$rowType); ?>"
					class="btn btn-xs btn-light b-1"
					data-toggle="ajax-load"
					data-mode="after"
					data-overlay="none"
					data-target="#pdf_builder-header"
				><span class="px-2 mx-1"><?php echo $rowType; ?></span></a><?php
			endforeach; // foreach-type
		?></div><?php
	endforeach; // foreach-group
endif;
$doc->find('th.col-button')->html(ob_get_clean());


// display
echo $doc;