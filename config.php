<?php
ini_set('display_errors',1);
$basepath = getcwd();
$upload_dir = "/uploads/";
$output_dir = $basepath.$upload_dir;
$site_upload_dir = 'http://'.$_SERVER['SERVER_NAME']."/temp/ConvertToImages/uploads/";
$site_upload_dir = str_replace($_SERVER['DOCUMENT_ROOT'],'http://'.$_SERVER['SERVER_NAME'],$output_dir);
$water_mark_text = 'Copyrights (c) 2016';
$arrFileTypes = array('pdf','doc','docx','odt','ott','epub');

$host = 'localhost';
$user = 'root';
$pass = 'password';
$db = 'convertion';

?>