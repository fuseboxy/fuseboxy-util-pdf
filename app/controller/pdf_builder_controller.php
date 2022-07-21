<?php /*
<fusedoc>
	<description>
		generate UI for pdf-builder CRUD operations
	</description>
	<io>
		<in>
			<structure name="$pdfBuilder" comments="config">
				<!-- essentials -->
				<string name="layoutPath" />
				<string_or_structure name="retainParam" />
				<!-- permissions -->
				<boolean name="allowNew" />
				<boolean name="allowEdit" />
				<boolean name="allowDelete" />
				<boolean name="allowToggle" />
				<boolean name="allowSort" />
				<!-- filter & order -->
				<structure name="listFilter">
					<string name="sql" />
					<array name="param" />
				</structure>
				<string name="listOrder" />
				<!-- others -->
				<boolean name="writeLog" />
			</structure>
		</in>
		<out>
		</out>
	</io>
</fusedoc>
*/



