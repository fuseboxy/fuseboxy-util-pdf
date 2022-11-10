<?php
class PDFDoc {


	// get (latest) error message
	private static $error;
	public static function error() { return self::$error; }




	/**
	<fusedoc>
		<description>
			load rows of specific pdf-doc record
			===> convert to array data for util-array2html method
		</description>
		<io>
			<in>
				<mixed name="$doc" comments="id|alias|object" />
			</in>
			<out>
				<array name="~return~">
					<structure name="+">
						<string name="type" default="div" value="div|p|h1|h2|h3|h4|h5|h6|small|ol|ul|br|hr|img|pagebreak" />
						<!-- value -->
						<string name="value" />
						<!-- styling -->
						<boolean name="bold" default="false" />
						<boolean name="underline" default="false" />
						<boolean name="italic" default="false" />
						<string name="color" value="ffccaa|#ffccaa|.." />
						<number name="size" optional="yes" oncondition="div|p|ul|ol|br" />
						<!-- alignment -->
						<string name="align" value="left|right|center|justify" oncondition="div|p|h1..h6|small|img" />
						<!-- options -->
						<number name="repeat" optional="yes" default="1" oncondition="br" />
						<number name="height" optional="yes" oncondition="img" />
						<number name="width" optional="yes" oncondition="img" />
						<string name="url" optional="yes" />
					</structure>
				</array>
			</out>
		</io>
	</fusedoc>
	*/
	public static function array($doc) {
		$result = array();
		// load record
		$bean = self::load($doc);
		if ( $bean === false ) return false;
		// get related rows
		$beanRows = ORM::get('pdfrow', 'disabled = 0 AND pdfdoc_id = ? ORDER BY IFNULL(seq, 9999), id ', array($bean->id));
		if ( $beanRows === false ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Error loading PDF rows (docID='.$bean->id.')';
			return false;
		}
		// transform each item from object to array
		foreach ( $beanRows as $rowID => $rowItem ) {
			$result[] = Bean::export($rowItem);
			if ( $result[array_key_last($result)] === false ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Error exporting PDF row (rowID='.$rowID.')';
				return false;
			}
		}
		// done!
		return $result;
	}




	/**
	<fusedoc>
		<description>
			duplicate specific doc & all rows
		</description>
		<io>
			<in>
				<mixed name="$doc" comments="id|alias|object" />
			</in>
			<out>
				<object name="~return~" type="pdfdoc" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function duplicate($doc) {
		// load source doc
		$doc = self::load($doc);
		if ( $doc === false ) return false;
		// load rows of source doc
		$docRows = ORM::get('pdfrow', 'disabled = 0 AND pdfdoc_id = ? ', array($doc->id));
		if ( $docRows === false ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] '.ORM::error();
			return false;
		}
		// prepare data for new doc
		$docData = Bean::export($doc);
		if ( $docData === false ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] '.Bean::error();
			return false;
		}
		// clear id to create new record
		$docData['id'] = null;
		// prepare new alias
		$newAliasIndex = 0;
		do {
			$newAliasIndex++;
			$newAlias = "{$doc->alias} ({$newAliasIndex})";
			$countSameAlias = ORM::count('pdfdoc', 'alias = ? ', array($newAlias));
			if ( $countSameAlias === false ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] '.ORM::error();
				return false;
			}
		} while ( $countSameAlias > 0 );
		$docData['alias'] = $newAlias;
		// proceed to create new doc
		$newDoc = ORM::saveNew('pdfdoc', $docData);
		if ( $newDoc === false ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] '.ORM::error();
			return false;
		}
		// create rows for new doc
		foreach ( $docRows as $docRowItem ) {
			// prepare data for new row
			$rowData = Bean::export($docRowItem);
			if ( $rowData === false ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] '.Bean::error();
				return false;
			}
			// clear id to create new record
			$rowData['id'] = null;
			// make to new doc
			$rowData['pdfdoc_id'] = $newDoc->id;
			// proceed to create new row
			$newRow = ORM::saveNew('pdfrow', $rowData);
			if ( $newRow === false ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] '.ORM::error();
				return false;
			}
		}
		// done!
		return $newDoc;
	}




	/**
	<fusedoc>
		<description>
			load specific pdf-doc record
		</description>
		<io>
			<in>
				<mixed name="$doc" comments="id|alias|object" />
			</in>
			<out>
				<object name="~return~" type="pdfdoc" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function load($doc) {
		// check object (when necessary)
		if ( is_object($doc) ) {
			$bean = $doc;
			$beanType = Bean::type($bean);
			if ( $beanType === false ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] '.Bean::error();
				return false;
			} elseif ( $beanType != 'pdfdoc' ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Invalid object type ('.$beanType.')';
				return false;
			}
		// load record (when necessary)
		} elseif ( is_numeric($doc) or is_string($doc) ) {
			$bean = is_numeric($doc) ? ORM::get('pdfdoc', $doc) : ORM::first('pdfdoc', 'alias = ? ORDER BY alias, id ', [ $doc ]);
			if ( $bean === false ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] '.ORM::error();
				return false;
			} elseif ( empty($bean->id) ) {
				self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] PDF doc not found (docID='.$doc.')';
				return false;
			}
		// invalid...
		} else {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Invalid doc format';
			return false;
		}
		// check status
		if ( !empty($bean->disabled) ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] PDF doc was disabled ('.$bean->alias.')';
			return false;
		}
		// done!
		return $bean;
	}




	/**
	<fusedoc>
		<description>
			load rows of document and render as pdf/html
		</description>
		<io>
			<in>
				<mixed name="$doc" comments="id|alias|object" />
				<string name="format" optional="yes" default="pdf" comments="pdf|html" />
			</in>
			<out>
				<!-- success -->
				<string name="~return~" format="html" oncondition="when {format=html}" />
				<!-- failure -->
				<boolean name="~return~" value="false" oncondition="when error" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function render($doc, $format='pdf') {
		$format = strtolower($format);
		// validation
		if ( !in_array($format, ['pdf','html']) ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] Invalid format to render ('.$format.')';
			return false;
		}
		// load data
		$data = self::array($doc);
		if ( $data === false ) return false;
		// display as pdf directly (or capture html output)
		$result = ( $format == 'pdf' ) ? Util_PDF::array2pdf($data) : Util_PDF::array2html($data);
		if ( $result === false ) {
			self::$error = '['.__CLASS__.'::'.__FUNCTION__.'] '.Util_PDF::error();
			return false;
		}
		// done!
		return $result;
	}
	// alias method
	public static function renderHtml($doc) { return self::render($doc, 'html'); }


} // class