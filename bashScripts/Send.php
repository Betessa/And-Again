<?php

	$old_path = getcwd();
	chdir('/Devo-Onions/bashScripts/');//ensures that code is done in specific directory of server
	$output = shell_exec('./sendbash');
	chdir($old_path);
	echo "<pre>$output</pre>";
?>
