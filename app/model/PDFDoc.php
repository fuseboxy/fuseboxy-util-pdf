<?php
class PDFDoc {


	// get (latest) error message
	private static $error;
	public static function error() { return self::$error; }




	/**
	<fusedoc>
		<description>
			load specific pdf-doc record (when necessary)
		</description>
		<io>
			<in>
				<mixed name=$doc" comments="id|alias|object" />
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
				self::$error = '[PDFDoc::load] '.Bean::error();
				return false;
			} elseif ( $beanType != 'pdfdoc' ) {
				self::$error = "[PDFDoc::load] Invalid object type ({$beanType})";
				return false;
			}
		// load record (when necessary)
		} elseif ( is_numeric($doc) or is_string($doc) ) {
			$bean = is_numeric($doc) ? ORM::get('pdfdoc', $doc) : ORM::first('pdfdoc', 'alias = ? ORDER BY alias, id ', [ $doc ]);
			if ( $bean === false ) {
				self::$error = '[PDFDoc::load] '.ORM::error();
				return false;
			} elseif ( empty($bean->id) ) {
				self::$error = "[PDFDoc::load] PDF doc not found (docID={$doc})";
				return false;
			}
		// invalid...
		} else {
			self::$error = '[PDFDoc::load] Invalid doc format';
			return false;
		}
		// check status
		if ( !empty($bean->disabled) ) {
			self::$error = "[PDFDoc::load] PDF doc was disabled ({$bean->alias})";
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
			self::$error = "[PDFDoc::render] Invalid format to render ({$format})";
			return false;
		}
		// load record
		$bean = self::load($doc);
		if ( $bean === false ) return false;
		// get related rows
		$beanRows = ORM::get('pdfrow', 'disabled = 0 AND pdfdoc_id = ? ORDER BY IFNULL(seq, 9999) ', array($bean->id));
		if ( $beanRows === false ) {
			self::$error = "[PDFDoc::render] Error loading PDF rows (docID={$bean->id})";
			return false;
		}
		// transform each item from object to array
		$data = array();
		foreach ( $beanRows as $rowID => $rowItem ) {
			$data[] = Bean::export($rowItem);
			if ( $data[array_key_last($data)] === false ) {
				self::$error = "[PDFDoc::render] Error exporting PDF row (rowID={$rowID})";
				return false;
			}
		}
		// display as pdf directly (or capture html output)
		$result = ( $format == 'pdf' ) ? Util_PDF::array2pdf($data) : Util_PDF::array2html($data);
		if ( $result === false ) {
			self::$error = '[PDFDoc::render] '.Util_PDF::error();
			return false;
		}
		// done!
		return $result;
	}
	// alias method
	public static function renderHtml($doc) { return self::render($doc, 'html'); }


} // class