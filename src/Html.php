<?php namespace Gufy\PdfToHtml;

use PHPHtmlParser\Dom;

define('WATERMARK_MARGIN_ADJUST', 5);
define('WATERMARK_FONT_REALPATH', '/var/www/html/temp/pdf-to-image/dejavu-sans/');
define('WATERMARK_OUTPUT_QUALITY', 90);
define('UPLOADED_IMAGE_DESTINATION', 'output/');
define('PROCESSED_IMAGE_DESTINATION', 'images/');

	
class Html extends Dom
{
  protected $contents, $total_pages, $current_page, $pdf_file, $locked = false;
  public function __construct($pdf_file, $id = false)
  {
    $this->getContents($pdf_file, $id);
    return $this;
  }
  private function getContents($pdf_file, $id = false)
  {
	  global $new_output_dir, $basepath;
    $this->locked = true;
    $info = new Pdf($pdf_file);
    $pdf = new Base($pdf_file, array(
      'singlePage'=>true,
      'noFrames'=>false,
	  'zoom'=>5,
    ));
    $pages = $info->getPages();
    $random_dir = uniqid();
    $outputDir = Config::get('pdftoimage.output', $basepath.'/output/'.$random_dir);
    // $outputDir = Config::get('pdftoimage.output', $new_output_dir);
    if(!file_exists($outputDir))
    mkdir($outputDir, 0777, true);
    $pdf->setOutputDirectory($outputDir);
    $pdf->generate();
    $fileinfo = pathinfo($pdf_file);
    $base_path = $image_path = $pdf->outputDir.'/'.$fileinfo['filename'];
	$contents = array();
    for($i=1;$i<=$pages;$i++)
    {
		$html = $base_path.'-'.$i.'.html';
		$image = $image_path.'-'.$i.'.jpg';
      $content = file_get_contents($html);
      $content = str_replace("Â","",$content);  
      file_put_contents($html, $content);
      $contents[$i] = file_get_contents($html);
	  $this->htmlToImage($html,$image,$id, $pdf->outputDir);
    }
	
    $this->contents = $contents;
    $this->goToPage(1);
  }
  
	function htmlToImage($html,$image, $id = false, $outputDir = false) {
		global $basepath, $new_output_dir;
		$url = escapeshellarg($html);
		$command = $basepath."/vendor/h4cc/wkhtmltoimage-i386/bin/wkhtmltoimage-i386  --quality 100 --zoom 5 $url $image";
		echo $command;
		passthru($command, $status);
		if ($status != 0) {
			echo "There was an error executing the command. Died with exit code: $status";
		}
		$result = $this->process_image($image, $new_output_dir);
		if($result) {
			$this->updateTable($id, $image, $result[1]);
			// unlink($html);
		}
		return $result;
	}
  
	function utf8_code_deep($input, $b_encode = TRUE, $b_entity_replace = TRUE)
    {
        if (is_string($input))
        {
            if($b_encode)
            {
                $input = utf8_encode($input);

                //return Entities to UTF8 characters
                //important for interfaces to blackbox-pages to send the correct UTF8-Characters and not Entities.
                if($b_entity_replace)
                {
                    $input = html_entity_decode($input, ENT_NOQUOTES/* | ENT_HTML5*/, 'UTF-8'); //ENT_HTML5 is a PHP 5.4 Parameter.
                }
            }
            else
            {
                //Replace NON-ISO Characters with their Entities to stop setting them to '?'-Characters.
                if($b_entity_replace)
                {
                    $input = preg_replace("/([\304-\337])([\200-\277])/e", "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'", $input);
                }

                $input = utf8_decode($input);
            }
        }
        elseif (is_array($input))
        {
            foreach ($input as &$value)
            {
                $value = $this->utf8_code_deep($value, $b_encode, $b_entity_replace);
            }
        }
        elseif (is_object($input))
        {
            $vars = array_keys(get_object_vars($input));

            if(get_class($input) == 'SimpleXMLElement')
            {
                //DOES NOT WORK!
                return '';
            }

            foreach ($vars as $var)
            {
                $input->$var = $this->utf8_code_deep($input->$var, $b_encode, $b_entity_replace);
            }
        }

        return $input;
    }
  public function goToPage($page=1)
  {
    if($page>count($this->contents))
    throw new \Exception("You're asking to go to page {$page} but max page of this document is ".count($this->contents));
    $this->current_page = $page;
    return $this->load($this->contents[$page]);
  }

  public function raw($page = 1){
    return $this->contents[$page];
  }
  public function getTotalPages()
  {
    return count($this->contents);
  }
  public function getCurrentPage()
  {
    return $this->current_page;
  }
  public function inlineCss()
  {
    return Config::get('pdftoimage.inlineCss', true);
  }
  
	  /*
	 * PHP function to text-watermark an image
	 * http://salman-w.blogspot.com/2008/11/watermark-your-images-with-text-using.html
	 *
	 * Writes the given text on a GD image resource using
	 * the specified true-type font, size, color, etc
	 */

	function render_text_on_gd_image(&$source_gd_image, $text, $font, $size, $color, $opacity, $rotation, $align)
	{
		$source_width = imagesx($source_gd_image);
		$source_height = imagesy($source_gd_image);
		$bb = $this->imagettfbbox_fixed($size, $rotation, $font, $text);
		$x0 = min($bb[0], $bb[2], $bb[4], $bb[6]) - WATERMARK_MARGIN_ADJUST;
		$x1 = max($bb[0], $bb[2], $bb[4], $bb[6]) + WATERMARK_MARGIN_ADJUST;
		$y0 = min($bb[1], $bb[3], $bb[5], $bb[7]) - WATERMARK_MARGIN_ADJUST;
		$y1 = max($bb[1], $bb[3], $bb[5], $bb[7]) + WATERMARK_MARGIN_ADJUST;
		$bb_width = abs($x1 - $x0);
		$bb_height = abs($y1 - $y0);
		switch ($align) {
			case 11:
				$bpy = -$y0;
				$bpx = -$x0;
				break;
			case 12:
				$bpy = -$y0;
				$bpx = $source_width / 2 - $bb_width / 2 - $x0;
				break;
			case 13:
				$bpy = -$y0;
				$bpx = $source_width - $x1;
				break;
			case 21:
				$bpy = $source_height / 2 - $bb_height / 2 - $y0;
				$bpx = -$x0;
				break;
			case 22:
				$bpy = $source_height / 2 - $bb_height / 2 - $y0;
				$bpx = $source_width / 2 - $bb_width / 2 - $x0;
				break;
			case 23:
				$bpy = $source_height / 2 - $bb_height / 2 - $y0;
				$bpx = $source_width - $x1;
				break;
			case 31:
				$bpy = $source_height - $y1;
				$bpx = -$x0;
				break;
			case 32:
				$bpy = $source_height - $y1;
				$bpx = $source_width / 2 - $bb_width / 2 - $x0;
				break;
			case 33;
				$bpy = $source_height - $y1;
				$bpx = $source_width - $x1;
				break;
		}
		$alpha_color = imagecolorallocatealpha(
			$source_gd_image,
			hexdec(substr($color, 0, 2)),
			hexdec(substr($color, 2, 2)),
			hexdec(substr($color, 4, 2)),
			127 * (100 - $opacity) / 100
		);
		return imagettftext($source_gd_image, $size, $rotation, $bpx, $bpy, $alpha_color, WATERMARK_FONT_REALPATH . $font, $text);
	}

	/*
	 * Fix for the buggy imagettfbbox implementation in gd library
	 */

	function imagettfbbox_fixed($size, $rotation, $font, $text)
	{
		$bb = imagettfbbox($size, 0, WATERMARK_FONT_REALPATH . $font, $text);
		$aa = deg2rad($rotation);
		$cc = cos($aa);
		$ss = sin($aa);
		$rr = array();
		for ($i = 0; $i < 7; $i += 2) {
			$rr[$i + 0] = round($bb[$i + 0] * $cc + $bb[$i + 1] * $ss);
			$rr[$i + 1] = round($bb[$i + 1] * $cc - $bb[$i + 0] * $ss);
		}
		return $rr;
	}

	/*
	 * Wrapper function for opening file, calling watermark function and saving file
	 */

	function create_watermark_from_string($source_file_path, $output_file_path, $text, $font, $size, $color, $opacity, $rotation, $align)
	{
		list($source_width, $source_height, $source_type) = getimagesize($source_file_path);
		if ($source_type === NULL) {
			return false;
		}
		switch ($source_type) {
			case IMAGETYPE_GIF:
				$source_gd_image = imagecreatefromgif($source_file_path);
				break;
			case IMAGETYPE_JPEG:
				$source_gd_image = imagecreatefromjpeg($source_file_path);
				break;
			case IMAGETYPE_PNG:
				$source_gd_image = imagecreatefrompng($source_file_path);
				break;
			default:
				return false;
		}
		$this->render_text_on_gd_image($source_gd_image, $text, $font, $size, $color, $opacity, $rotation, $align);
		imagejpeg($source_gd_image, $output_file_path, WATERMARK_OUTPUT_QUALITY);
		imagedestroy($source_gd_image);
	}

	function process_image($image, $outputDir = false)
	{
		global $water_mark_text;
		$image_name = basename($image);
		$temp_file_name = time().'_'.$image_name;
		list(, , $temp_type) = getimagesize($image);
		if ($temp_type === NULL) {
			return false;
		}
		switch ($temp_type) {
			case IMAGETYPE_GIF:
				break;
			case IMAGETYPE_JPEG:
				break;
			case IMAGETYPE_PNG:
				break;
			default:
				return false;
		}
		// $uploaded_file_path = UPLOADED_IMAGE_DESTINATION . $temp_file_name;
		$uploaded_file_path = $image;
		$temp_file_name = preg_replace('/\\.[^\\.]+$/', '.jpg', $temp_file_name);
		$processed_file_path = $outputDir . $temp_file_name;
		/*
		 * PARAMETER DESCRIPTION
		 * (1) SOURCE FILE PATH
		 * (2) OUTPUT FILE PATH
		 * (3) THE TEXT TO RENDER
		 * (4) FONT NAME -- MUST BE A *FILE* NAME
		 * (5) FONT SIZE IN POINTS
		 * (6) FONT COLOR AS A HEX STRING
		 * (7) OPACITY -- 0 TO 100
		 * (8) TEXT ANGLE -- 0 TO 360
		 * (9) TEXT ALIGNMENT CODE -- POSSIBLE VALUES ARE 11, 12, 13, 21, 22, 23, 31, 32, 33
		 */
		$result = $this->create_watermark_from_string(
			$uploaded_file_path,
			$processed_file_path,
			$water_mark_text, 
			'DejaVuSans.ttf',
			75,
			'cccccc',
			75,
			0,
			32
		);
		
		if ($result === false) {
			return false;
		} else {
			// unlink($processed_file_path);
			return array($uploaded_file_path, $temp_file_name);
		}
	}
	
	function updateTable($id, $image, $vImageName) {
		global $link;
		if(file_exists($image)) {
			$insSql = 'INSERT INTO image_conversion (image_id, image_convrt_path, created) VALUES ("'.$id.'","'.$vImageName.'", NOW())';
			$r = $link->query($insSql);
			if($r) {
				$upSql = 'UPDATE image_uploads SET status = 2, modified = NOW() WHERE status = 1 AND id = "'.$id.'"';
				$ur = $link->query($upSql);
				if($ur) {
					return true;
				}
			}
		}
		return false;
	}
}
