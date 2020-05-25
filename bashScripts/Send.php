<?php

	$old_path = getcwd();
	chdir('Submissions');//ensures that code is done in specific directory of server
	$output = shell_exec('./sendbash.sh');
	chdir($old_path);
	$arr = explode("\n", $output);
	$mossSite=$arr[count($arr)-2];
	echo "<pre>$mossSite</pre>";
?>
