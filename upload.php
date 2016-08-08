<?php
include('connection.php');

if(isset($_FILES["myfile"]))
{
	$RandomNum = time();
	$message = '';
	$ImageName =  $OrgFileName = str_replace(' ','-',strtolower($_FILES['myfile']['name']));
	$ImageType = $_FILES['myfile']['type']; //"image/png", image/jpeg etc.
	$ImageSize = $_FILES['myfile']['size']; 
	$fileTypes = implode(',',$arrFileTypes);

	$ImageExt = substr($ImageName, strrpos($ImageName, '.'));
	$ImageExt = strtolower(str_replace('.','',$ImageExt));
	if(!in_array($ImageExt, $arrFileTypes)) {
		// $ImageExt != "pdf" && $ImageExt != "doc" && $ImageExt != "odt" && $ImageExt != "docx" && $ImageExt != "ott" && $ImageExt != 'epub') {
		$message .= "Invalid file format only <b>\"".strtoupper($fileTypes)."\"</b> allowed.";
	} else {
		$ImageName    = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);
		$NewImageName = $ImageName.'-'.$RandomNum.'.'.$ImageExt;
		$insSql = 'INSERT INTO image_uploads (name, image_path, size, type,created) VALUES ("'.$OrgFileName.'","'.$NewImageName.'", "'.$ImageSize.'","'.$ImageType.'",NOW()) ';
		$res = $link->query($insSql);
		if($res) {
			$last_id = $link->insert_id;
			$output_dir .= $last_id.'/';
			mkdir($output_dir,0777,true);
			if(move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir. $NewImageName)) {
				$name     = $output_dir. $NewImageName;
				$message .= $OrgFileName." has uploaded sucessfully!!<br>";
			} else {
				$message .= 'Sorry, '.$OrgFileName." File could not upload!!<br>";
			}
		}
	}
	echo $message;
}
?>
<script type="text/javascript">
setTimeout(function() { window.location.href="fileList.php";}, 3000);
</script>