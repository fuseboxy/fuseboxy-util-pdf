<?php /*
<fusedoc>
	<io>
		<in>
		</in>
		<out>
			<number name="docID" scope="url" oncondition="xfa.editTemplate" />
		</out>
	</io>
</fusedoc>
*/
// capture original output
ob_start();
include F::appPath('view/scaffold/row.php');
$doc = ob_get_clean();


// done!
echo $doc;