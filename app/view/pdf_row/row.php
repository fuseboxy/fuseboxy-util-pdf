<?php /*
<fusedoc>
	<io>
		<in>
			<object name="$bean" type="pdfrow">
				<number name="id" />
				<number name="pdfdoc_id" />
				<string name="type" />
				<mixed name="value" />
				<string name="url" />
				<string name="align" />
				<string name="color" />
				<number name="size" />
				<boolean name="bold" />
				<boolean name="italic" />
				<boolean name="underline" />
				<mixed name="height" />
				<mixed name="width" />
				<number name="seq" />
				<boolean name="disabled" />
			</object>
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
$doc->find('td.col-value')->html(( $bean->type == 'pagebreak' ) ? '
	<div class="row op-60">
		<div class="col pr-0"><hr class="b-secondary" style="border-style: dashed;" /></div>
		<div class="col-1 p-0 text-center text-muted small" style="line-height: 1.75rem;"><b>PAGEBREAK</b></div>
		<div class="col pl-0"><hr class="b-secondary" style="border-style: dashed;" /></div>
	</div>
' : Util_PDF::array2html([ $bean->export() ]));


// row type
$doc->find('div.col-type')->addClass('badge badge-light b-1 text-uppercase ml-1');
if ( $bean->type == 'br' ) $doc->find('div.col-type')->after('<small class="text-muted">('.$bean->value.')');


// done!
echo $doc;