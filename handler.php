<?php
$cfg=[];
$cfg['upload_path'] = './uploads/';
$cfg['max_filename_length'] = 60;

$cfg['allowed_extensions'] = array("gif", "jpeg", "jpg", "png");
$cfg['allowed_types'] = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/png", "image/x-png");

$cfg['additionalData'] = !empty($_GET) ? $_GET : false;  //additional form data (limited)
$cfg['files'] = isset($_FILES) && !empty($_FILES) ? $_FILES : false;

$r=[];
$r['success'] = false;

if($_SERVER['REQUEST_METHOD']=='POST'){
	if ($cfg['files']) {
		// create upload directory if it doesn't exist
		if(!is_dir($cfg['upload_path'])){
			mkdir($cfg['upload_path'],0755,true);
		}
		
		$r['success'] = true;
		foreach($cfg['files'] as $field => $file){
			
			$r[$field]['success'] = false;  // overwritten later on success
			
			// replace underscore in field name
			$field = str_replace('_','-',$field);
			
			$ex = explode(".", $file["name"]);

			// remove the last exploded element into $ext
			$ext = array_pop($ex);
			
			// truncate long filenames
			$basename = substr(implode($ex),0,$cfg['max_filename_length']);
			
			if(in_array($file["type"], $cfg['allowed_types'])){
				if(in_array($ext, $cfg['allowed_extensions'])){
					
					$cfg['savename'] = $field.'_'.$basename.'.'.$ext;
					$fudge = 1;
					while(file_exists($cfg['upload_path'].$cfg['savename'])){
						$cfg['savename'] = $field.'-'.$fudge.'_'.$basename.'.'.$ext;   // e.g. "upload_1_originalFilename.jpg" to "upload_1-1_originalFilename.jpg"
						$fudge++;
					}
					if(move_uploaded_file($file["tmp_name"],$cfg['upload_path'].$cfg['savename'])){
						// good
						$r[$field]['success'] = true;
					}else{
						$r['success'] = false;
						$r['errors'][] = 'Upload failed';
						// cannot move upload, delete orifinal
						if(unlink($file["tmp_name"])){
							// uploaded temp file deleted
							$r[$field]['unlink'] = true;
							$r['errors'][] = 'Unlink failed upload fail';
						}else{
							// cannot delete uploaded temp file
							$r[$field]['unlink'] = false;
							$r['errors'][] = 'Bad extension';
						}
					}
				}else{
					// bad ext
					$r[$field]['extension'] = false;
					$r['errors'][] = 'Bad extension';
				}
			}else{
				// bad type
				$r[$field]['type'] = false;
				$r['errors'][] = 'Bad image type';
			}	
		}
	}else{
		$r['error'] = 'No files to process';
	}
}else{
	$r['error'] = 'Incorrect request method';
}

echo json_encode($r,JSON_NUMERIC_CHECK);
