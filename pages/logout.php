<?php
	session_destroy();
	setcookie('token',null,time()-100);
	echo "<script>location.reload();</script>";
?>