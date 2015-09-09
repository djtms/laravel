<?php 
if(!$_FILES['uploadfile']['error']){
	$uploaddir = 'uploads/';
	exit($uploaddir);
	$file = $_FILES['uploadfile'];
	$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
	$ext = strtolower($ext);
	$fileName = uniqid(time(), false).'.'.$ext;
	$destinationfile = $uploaddir . $fileName;
	if(move_uploaded_file($file['tmp_name'], $destinationfile)){
		echo $fileName;
	} else {
		echo 'upload_error';
	}
} else {
	echo 'upload_error';
}

?>