<?php

	$output = shell_exec('./moss.pl -l cc *.cpp');
	$arr = explode("\n", $output);
	$mossSite=$arr[count($arr)-2];
	echo "<pre>$mossSite</pre>";
?>
