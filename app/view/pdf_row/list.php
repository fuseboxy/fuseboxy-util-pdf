<?php
// capture original output
ob_start();
include F::appPath('view/scaffold/list.php');
$doc = Util::phpQuery(ob_get_clean());


// first row
ob_start();
?><table class="table table-hover table-sm mb-0 scaffold-first-row">
	<tbody>
		<tr>
			<td width="100">&nbsp;</td>
			<td class="bx-1 bt-1 bb-0 b-dark" width="60%">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</tbody>
</table><?php
$doc->find('.scaffold-header')->after(ob_get_clean());


// last row
ob_start();
?><table class="table table-hover table-sm mb-0 scaffold-last-row">
	<tbody>
		<tr>
			<td width="100">&nbsp;</td>
			<td class="bx-1 bt-0 bb-1 b-dark pb-5" width="60%">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</tbody>
</table><?php
$doc->find('.scaffold-list')->append(ob_get_clean());


// done!
echo $doc;