<?php
App::import('Vendor', 'PDF_Japanese', array('file' => 'fpdf/japanese.php'));

if (!defined('PARAGRAPH_STRING')) define('PARAGRAPH_STRING', '~~~');

define('FPDF_FONTPATH', APP . 'vendors/fpdf/font/');
define('FPDFHELPER_INTERNAL_ENCODING', 'UTF-8');

class FpdfHelper extends PDF_Japanese {

	public $helpers = array();

	function __construct() {
		$this->PDF_Japanese();
	}

	function setup($orientation='P',$unit='mm',$format='A4') {
		$this->PDF_Japanese($orientation, $unit, $format);
	}

	function FpdfOutput ($name = 'page.pdf', $destination = 's') {
		return $this->Output($name, $destination);
	}

	function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='') {
		parent::Cell($w,$h,$this->conv_sjis($txt),$border,$ln,$align,$fill,$link);
	}

	function Text($x,$y,$txt) {
		parent::Text($x,$y,$this->conv_sjis($txt));
	}

	function conv_sjis($txt) {
		return mb_convert_encoding($txt, "SJIS-win", FPDFHELPER_INTERNAL_ENCODING);
	}
}
?>