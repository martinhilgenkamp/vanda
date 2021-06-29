<?php
	//Upload script for task form
	$valid_types = array("jpg", "pdf", "txt", "png","jpeg","gif","xls","xlsx","doc","docx");
	
	if (isset($_FILES['file']['name'])) {
		$ext = (explode(".", $_FILES['file']['name']));
		if(!in_array(strtolower(end($ext)), $valid_types)){
			echo 'filetype';
		} 
		else {		
			if (0 < $_FILES['file']['error']) {
				echo 'error';
			} else {
				if (file_exists('../upload/' . $_FILES['file']['name'])) {
					echo 'exists';
				} else {
					move_uploaded_file($_FILES['file']['tmp_name'], '../upload/' . $_FILES['file']['name']);
					echo 'success';
				}
			}
		}
	} else {
		echo 'none';
	}
?>