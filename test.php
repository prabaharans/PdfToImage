<?php
ini_set('display_errors',1);
// if you are using composer, just use this
include 'vendor/autoload.php';

// initiate
$pdf = new Gufy\PdfToHtml\Pdf('file.pdf');

// convert to html string
$html = $pdf->html();

// convert to html and return it as [Dom Object](https://github.com/paquettg/php-html-parser)
$dom = $pdf->getDom();

// check if your pdf has more than one pages
$total_pages = $pdf->getPages();

$contentType = '<style>	
							@font-face {
								font-family: "DejaVu Sans";
								src: url("http://kendo.cdn.telerik.com/2014.3.1314/styles/fonts/DejaVu/DejaVuSans.ttf");
							}
							@font-face {
								font-family: "DejaVu Sans";
								src: url("http://kendo.cdn.telerik.com/2014.3.1314/styles/fonts/DejaVu/DejaVuSans-Bold.ttf");
								font-weight: bold;
							}
							@font-face {
								font-family: "DejaVu Sans";
								src: url("http://kendo.cdn.telerik.com/2014.3.1314/styles/fonts/DejaVu/DejaVuSans-Oblique.ttf");
								font-style: italic, oblique;
							}
							@font-face {
								font-family: "DejaVu Sans";
								src: url("http://kendo.cdn.telerik.com/2014.3.1314/styles/fonts/DejaVu/DejaVuSans-Oblique.ttf");
								font-weight: bold;
								font-style: italic, oblique;
							}
							* { font-family: "DejaVu Sans", "Arial", sans-serif; }
						</style>';
		// $html = str_replace('<head>', '<head>' . $contentType, $html);
		$html = $contentType . $html;
echo '<pre>';
print_r($html);
echo '</pre>';
// Your pdf happen to have more than one pages and you want to go another page? Got it. use this command to change the current page to page 3
// $html->goToPage(3);

// and then you can do as you please with that dom, you can find any element you want
// $paragraphs = $html->find('body > p');

// change pdftohtml bin location
\Gufy\PdfToHtml\Config::set('pdftohtml.bin', '/usr/local/bin/pdftohtml');

// change pdfinfo bin location
\Gufy\PdfToHtml\Config::set('pdfinfo.bin', '/usr/local/bin/pdfinfo');
?>