<?php
	//function to send files to bash
	function SendtoMoss($langNo) {	
		switch ($langNo){   //This determines what coding language we should send
			case 0:
				$lang = "cc";
			default:
				$lang = "cc";
		}
		
		$old_path = getcwd();

		//chdir('Moss');//ensures that code is done in specific directory of server
		$output = shell_exec("./sendbash.sh '".$lang."'");// This executes our bash code that comunicates with moss
		chdir($old_path);
		$arr = explode("\n", $output);
		$mossSite=$arr[count($arr)-2];
		return $mossSite;
	}

	echo  SendtoMoss(0);	
?>
