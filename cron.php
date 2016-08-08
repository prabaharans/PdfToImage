<?php
	$message = 'Start => '.date('Y-m-d H:i:s').'<br>';
	include('connection.php');
	include 'vendor/autoload.php';
	
	$convertFormat = 'jpg';

	$selSql = 'SELECT * FROM image_uploads WHERE status = 1 AND type NOT LIKE "%epub%" LIMIT 50';
	$res = $link->query($selSql);
	if($res) {
		$old_output_dir = $output_dir;
		$r = mysqli_num_rows($res);
		if($r > 0) {
			while($row = mysqli_fetch_assoc($res)) {
				$id = $row['id'];
				$ImageName = $row['image_path'];
				$new_output_dir = $output_dir;
				$new_output_dir .= $id.'/';
				
				$RandomNum   = time();
				
				$rootFilePath = $new_output_dir.$ImageName;
				
				$ImageExt = substr($ImageName, strrpos($ImageName, '.'));
				$ImageExt = str_replace('.','',$ImageExt);
				
				$ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
				$NewImageName = $ImageName.'.'.$ImageExt;
				$name       = $new_output_dir. $NewImageName;				
				
				if($ImageExt != 'epub') {
					if($ImageExt == "doc" || $ImageExt == "odt" || $ImageExt == "docx" || $ImageExt == "ott") {
						$convert_doc_pdf = "unoconv --format pdf --output ".$new_output_dir.' '.$name;
						exec($convert_doc_pdf);
						$NewImageName    = $ImageName.".pdf";
					}
					if(file_exists($name)) {
						$pdf = new Gufy\PdfToHtml\Html($name, $id);
						
						// $html = $pdf->html(1, $id);
						
						// $dom = $pdf->getDom();
						
						// $total_pages = $pdf->getPages();
						
						// change pdftohtml bin location
						\Gufy\PdfToHtml\Config::set('pdftohtml.bin', '/usr/local/bin/pdftohtml');

						// change pdfinfo bin location
						\Gufy\PdfToHtml\Config::set('pdfinfo.bin', '/usr/local/bin/pdfinfo');
						// $base = new Gufy\PdfToHtml\Base();
						// $base->outputDir = $basepath.'/output/';
						// $base->clearOutputDirectory();
					}
					$message .= strtoupper($ImageExt)." converted to ".strtoupper($convertFormat)." sucessfully!!<br>";
				}
			}
		}
	}
	$message .= 'End =>'.date('Y-m-d H:i:s').'<br>';
	$content = $message.'<br />';
	
	echo $content;

?>