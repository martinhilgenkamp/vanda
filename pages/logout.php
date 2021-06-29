<?php
	session_destroy();
	setcookie('username',null,time()-100);
	echo "<script>location.reload();</script>";
?>