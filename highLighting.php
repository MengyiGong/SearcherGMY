<html>
<body>
	<span style="background-color:#B4009E;color:#ffffff;">FALL 2013<br>CS546 Searcher<br>Author: Mengyi Gong</span><br><br>
	<?php

		class highlight{
	        public $output_text;
	        function __construct($text, $words){
	            $split_words = explode( " " , $words );
	            foreach ($split_words as $word){
	                $color = "#daa732";
	                $text = preg_replace("|($word)|Ui" , "<span style=\"background:".$color.";\"><b>$1</b></span>" , $text );
	            }
	            $this->output_text = $text;
	        }
	    }

		$keyword = $_COOKIE['kw'];
		echo "HIGH LIGHTING KEY WORD IS:    ".$keyword."<br>";

		if(isset($_POST['openURL'])){
			$getURL = $_POST['openURL'];
			$entire_web = file_get_contents($getURL);
			$highlight = new highlight($entire_web , $keyword);
    		echo $highlight->output_text;
		}else{
			echo "URL passing error!";
		}
	?>
</body>
</html>