<?php /*
<fusedoc>
	<io>
		<in>
			<object name="$bean" type="pdfrow" />
		</in>
		<out />
	</io>
</fusedoc>
*/
// capture original output
ob_start();
include F::appPath('view/scaffold/row.php');
$doc = Util::phpQuery(ob_get_clean());


// preview row value
if ( $bean->type != 'pagebreak' ) $rowOutput = Util_PDF::array2html([ $bean->export() ]);
else $rowOutput = '
	<div class="row op-60">
		<div class="col pr-0"><hr class="b-secondary" style="border-style: dashed;" /></div>
		<div class="col-1 p-0 text-center text-muted small" style="line-height: 1.75rem;"><b>PAGEBREAK</b></div>
		<div class="col pl-0"><hr class="b-secondary" style="border-style: dashed;" /></div>
	</div>
';
$doc->find('td.col-value')->addClass('bx-1 by-0 b-dark px-5')->html($rowOutput);


// row type
$doc->find('div.col-type')->addClass('badge badge-light b-1 text-uppercase ml-1');



// done!
echo $doc;