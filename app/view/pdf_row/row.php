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


// add paper border
$doc->find('td.col-value')->addClass('bx-1 by-0 b-dark px-5');


// row type
$doc->find('div.col-type')->addClass('badge badge-light b-1 text-uppercase ml-1');
if ( $bean->type == 'br' ) $doc->find('div.col-type')->after('<small class="text-muted">('.$bean->value.')');


// preview pagebreak
if ( $bean->type == 'pagebreak') :
	ob_start();
	?><div class="row op-60">
		<div class="col pr-0"><hr class="b-secondary" style="border-style: dashed;" /></div>
		<div class="col-1 p-0 text-center text-muted small" style="line-height: 1.75rem;"><b>PAGEBREAK</b></div>
		<div class="col pl-0"><hr class="b-secondary" style="border-style: dashed;" /></div>
	</div><?php
	$doc->find('div.col-value')->html(ob_get_clean());
// preview others
else :
	$doc->find('div.col-value')->html( Util_PDF::array2html([ Bean::export($bean) ]));
endif;


// prevent row content overflow
// ===> phpQuery has bug in css() method
// ===> use attr(style) method instead...
$tableStyle = $doc->find('table')->attr('style');
$divStyle = $doc->find('div.col-value')->attr('style');
$doc->find('table')->attr('style', 'table-layout:fixed;'.$tableStyle);
$doc->find('div.col-value')->attr('style', 'overflow:hidden;'.$divStyle);


// done!
echo $doc;