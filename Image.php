<?php namespace prabaharan\PdfToImage;

class Image
{
  protected $file, $info;
  public function __construct($file, $options=array())
  {
    $this->file = $file;
    // initiate
	$pdf = new Gufy\PdfToHtml\Pdf($file);
	
	$html = $pdf->html();
	
	$dom = $pdf->getDom();
	
	$total_pages = $pdf->getPages();
	
	// change pdftohtml bin location
	\Gufy\PdfToHtml\Config::set('pdftohtml.bin', '/usr/local/bin/pdftohtml');

	// change pdfinfo bin location
	\Gufy\PdfToHtml\Config::set('pdfinfo.bin', '/usr/local/bin/pdfinfo');
    return $this;
  }