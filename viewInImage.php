<?php 
	include('connection.php');
	$id = $_GET['id'];
	$selSql = 'SELECT * FROM image_conversion as ic WHERE ic.image_id = "'.$id.'"';
	$res = $link->query($selSql);
	$arr = array();
	if($res) {
		$r = mysqli_num_rows($res);
		if($r > 0) {
			$i = 1;
			while($row = mysqli_fetch_assoc($res)) {
				$arr[] = $site_upload_dir.$id.'/'.$row['image_convrt_path']; 
			}
		}
	}
	echo json_encode($arr);
?>