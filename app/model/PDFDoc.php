<?php
class PDFDoc {


	// get (latest) error message
	private static $error;
	public static function error() { return self::$error; }




	/**
	<fusedoc>
		<description>
			delete specific pdf-doc record & write log
		</description>
		<io>
			<in>
				<mixed name="$docID" />
			</in>
			<out>
				<boolean name="~return~" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function delete($docID) {
		// load record
		$bean = self::load($docID);
		if ( $bean === false ) return false;
		// proceed to delete
		$deleted = ORM::delete($bean);
		if ( $deleted === false ) {
			self::$error = '[PDFDoc::delete] '.ORM::error();
			return false;
		}
		// write log
/*


		if ( $logged === false ) {
			self::$error = '[PDFDoc::delete] Error writing log ('.Log::error().')';
			return false;
		}
*/
		// done!
		return true;
	}




	/**
	<fusedoc>
		<description>
			load first pdf-doc
		</description>
		<io>
			<in>
				<string name="$field" optional="yes" />
			</in>
			<out>
				<object name="~return~" type="pdfdoc" optional="yes" oncondition="when {field} not specified" />
				<mixed name="~return~" optional="yes" oncondition="when {field} specified" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function first($field=null) {
		// load record
		$bean = ORM::first('pdfdoc', 'ORDER BY alias, id ');
		if ( $bean === false ) {
			self::$error = '[PDFDoc::first] Error loading record ('.ORM::error().')';
			return false;
		} elseif ( empty($bean->id) ) {
			self::$error = '[PDFDoc::first] Record not found';
			return false;
		}
		// done!
		return empty($field) ? $bean : ( $bean->{$field} ?? null );
	}




	/**
	<fusedoc>
		<description>
			create new blank pdf-doc when none available
		</description>
		<io>
			<in />
			<out>
				<boolean name="~return~" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function init() {
		// check any record available
		$count = ORM::count('pdfdoc');
		if ( $count === false ) {
			self::$error = '[PDFDoc::init] Error counting records ('.ORM::error().')';
			return false;
		}
		// create record (when necessary)
		if ( !$count ) {
			$saved = ORM::saveNew('pdfdoc', [ 'alias' => 'blank' ]);
			if ( $saved === false ) {
				self::$error = '[PDFDoc::init] Error saving new record ('.ORM::error().')';
				return false;
			}
		}
		// done!
		return false;
	}




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
				<mixed name="$docID" />
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
	public static function render($docID, $format='pdf') {
		$format = strtolower($format);
		// validation
		if ( !in_array($format, ['pdf','html']) ) {
			self::$error = "[PDFDoc::render] Invalid format to render ({$format})";
			return false;
		}
		// load record
		$bean = self::load($docID);
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




	/**
	<fusedoc>
		<description>
			save pdf-doc record & write log
		</description>
		<io>
			<in>
				<structure name="$data">
					<number name="id" />
					<string name="alias" />
					<string name="title" />
					<string name="body" />
					<boolean name="disabled" />
				</structure>
			</in>
			<out>
				<number name="~return~" value="~lastInsertID~|~updateRecordID~" />
			</out>
		</io>
	</fusedoc>
	*/
	public static function save($data) {
		// load record, or...
		if ( !empty($data['id']) ) {
			$bean = self::load($data['id']);
			if ( $bean === false ) return false;
		// create empty container
		} else {
			$bean = ORM::new('pdfdoc');
			if ( $bean === false ) {
				self::$error = '[PDFDoc::save] Error creating container ('.ORM::error().')';
				return false;
			}
		}
		// modify record data
		foreach ( $data as $fieldName => $fieldValue ) $bean->{$fieldName} = $fieldValue;
		// proceed to save
		$result = ORM::save($bean);
		if ( $result === false ) {
			self::$error = '[PDFDoc::save] Error saving record ('.ORM::error().')';
			return false;
		}
		// write log
/*
		$logged = Log::write([
			'action'      => empty($arguments['docID']) ? 'CREATE_PDFDOC' : 'UPDATE_PDFDOC'
			'entity_type' => 'pdfdoc',
			'entity_id'   => $arguments['data']['id'],
			'remark' => !empty($arguments['data']['id']) ? Bean::diff($beanBeforeSave, $bean) : Bean::toString($bean),
		]);
		if ( $logged === false ) {
			self::$error = '[PDFDoc::save] Error writing log ('.Log::error().')';
			return false;
		}
*/
		// done!
		return $result;
	}


} // class