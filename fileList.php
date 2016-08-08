<?php
	include('connection.php');
	
	$selSql = 'SELECT * FROM image_uploads as iu ORDER BY id DESC';
	$res = $link->query($selSql);
?>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-thumbs.css" />
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-buttons.css" />
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	<div class="col-sm-8">
	<h3>Uploaded File list</h3>
	<table class="table table-bordered table-striped">
		<tr>
			<th>Sr.No</th>
			<th>File Name</th>
			<th>Image Path</th>
		</tr>
<?php
	if($res) {
		$r = mysqli_num_rows($res);
		if($r > 0) {
			$i = 1;
			while($row = mysqli_fetch_assoc($res)) {
				$id = $row['id'];
				$fileName = $row['name'];
				// $ImageName = $row['name'];
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $fileName; ?></td>
					<td><a id="manual<?php echo $id.'-'.$i; ?>" onclick="viewGallery('manual<?php echo $id.'-'.$i; ?>','<?php echo $id; ?>')" href="javascript:;">View In Image</a></td>
				</tr>
<?php
				$i++;
			}
		}
	}
?>
	</table><br>
	<a href="index.php">Go To Upload File</a>
	</div>
	<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>

	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-buttons.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-thumbs.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-media.js"></script>
	<!--[if IE 6]>
	<script src="http://fancybox.net/js/DD_belatedPNG_0.0.8a-min.js"></script>
		<script>
			DD_belatedPNG.fix('.png_bg');
		</script>
	<![endif]-->
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<script type="text/javascript">
	function viewGallery(id, ImageId) {
		console.log(id+'---'+ ImageId);
		// $("#"+id).click(function() {
			$.ajax({
				type	: "POST",
				cache	: false,
				url		: "viewInImage.php?id="+ImageId,
				data	: $(this).serializeArray(),
				dataType: 'json',
				success: function(data) {
					console.log(data);
					$.fancybox.open(data, {
						'padding'			: 0,
						'transitionIn'		: 'none',
						'transitionOut'		: 'none',
						'type'              : 'image',
						'changeFade'        : 0
					});
				}
			});
		// });
	}
	
	</script>