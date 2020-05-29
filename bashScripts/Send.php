<?php	
	//function to send files to bash
	function SendtoMoss($langNo,$base) {	
		switch ($langNo){   //This determines what coding language we should send
			case 0:
				$lang = "cc";
			case 1:
				$lang = "java";
			case 2:
				$lang = "c";
			case 3:
				$lang = "csharp";
			case 4:
				$lang = "python";
			case 5: 
				$lang = "matlab";
			case 6:
				$lang = "javascript";
			default:
				$lang = "cc";
		}
		
		$old_path = getcwd();

		//chdir('Moss');//ensures that code is done in specific directory of server
		if ($base) {
			$mossSite = shell_exec("./sendbashbase.sh '".$lang."'");// THis executes our bash code that communicates with moss with a base file
		}else{
			$mossSite = shell_exec("./sendbash.sh '".$lang."'");// This executes our bash code that comunicates with moss without a base file
		}
		chdir($old_path);
		return $mossSite;
	}
	
	echo  SendtoMoss(0,false);	
?>
