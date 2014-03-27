<html>
	
	<body>
		<span style="background-color:#B4009E;color:#ffffff;">FALL 2013<br>CS546 Searcher<br>Author: Mengyi Gong</span><br><br>
		<form action="searcher.php" method="post">
			Please Choose a Search Way:<br />
			<input type="checkbox" name="userChoice[]" value="fullText" />Full Text Search<br>
			<input type="checkbox" name="userChoice[]" value="metaInfo" />META Word Search<br>
			<input type="text" name="userKeyword" placeholder="Enter the Keyword"><br>
			<input type="submit" name="formSubmit" value="Submit" />
			
		</form>

		<?php
			include("./include/class.FastTemplate.php");
			date_default_timezone_set('America/New_York');
			
			$MySQL_Host = "localhost"; 
			$MySQL_User = "root"; 
			$MySQL_Pwd = "isucceed1108";
			$MySQL_Db = "mgong1_indexer";

			$fullTextSearch = "fullText";
			$metaInfoSearch = "metaInfo";
			
			function superExplode($str, $sep){
				$i = 0;
				$arr[$i++] = strtok($str, $sep);
				while ($token = strtok($sep))
					$arr[$i++] = $token;
				return $arr;
			}

			function connect(){
				global $MySQL_Host, $MySQL_User, $MySQL_Pwd, $MySQL_Db;
				$con=mysql_connect($MySQL_Host, $MySQL_User, $MySQL_Pwd) or die("Could not connect: " . mysql_error());
				mysql_select_db($MySQL_Db);
				return $con;
			}

			function search($searchWay, $word){
				global $fullTextSearch, $metaInfoSearch;
				$con = connect();
				$originalWord = $word;
				$word = str_replace("*","%", $word);
				if($con){
					if($searchWay == $fullTextSearch){
						echo '<table border="1">';
						echo '<tr>';
							echo '<td colspan="5">Full Text Search ---->Keyword:   ' .$originalWord. '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<th>Word</th>';
							echo '<th>Count</th>';
							echo '<th>File Name</th>';
							echo '<th>File Link</th>';
							echo '<th>Highlight</th>';
						echo '</tr>';
						
						$getWid = mysql_query("SELECT * FROM words WHERE word LIKE'".$word."'");
						$i = 0;
						$widStore = array();
						$wordStore = array();
						while($row = mysql_fetch_array($getWid)){
							$widStore[$i] = $row['wid'];
							$wordStore[$i] = $row['word'];
							$i ++;
						}
						for($j = 0; $j < $i; $j ++){
							$getCount = mysql_query("SELECT * FROM file_word WHERE wid='".$widStore[$j]."'");
							while($row = mysql_fetch_array($getCount)){
								$count = $row['count'];
								$fid = $row['fid'];
								$getFileInfo = mysql_query("SELECT * FROM files WHERE fid='".$fid."'");
								$frow = mysql_fetch_array($getFileInfo);
								$fileName = $frow['name'];
								$url = $frow['url'];
								
								$fileLink = "<a href= searcher.php?dir=".$url.">".$url."</a>";
								echo '<tr>';
									echo '<td>'.$wordStore[$j].'</td>';
									echo '<td>'.$count.'</td>';
									echo '<td>'.$fileName.'</td>';
									echo '<td>'.$fileLink.'</td>';
									echo '<td>';
										echo '<form method="post" action="highLighting.php">';
								    		echo '<button type="submit" value="' . $url. '" name="openURL">HighLight</button>';
								    	echo '</form>';
								    echo '</td>';
								echo '</tr>';
							}
						}
						echo '</table>';
					}else if($searchWay == $metaInfoSearch){
						echo '<table border="1">';
						echo '<tr>';
							echo '<td colspan="5">META Search ---->Keyword:   ' .$originalWord. '</td>';
						echo '</tr>';
						echo '<tr>';
							echo '<th>Word</th>';
							echo '<th>Type</th>';
							echo '<th>Content</th>';
							echo '<th>File Name</th>';
							echo '<th>File Link</th>';
						echo '</tr>';
						if($word == $originalWord){ // not like *word*
							$sqlMetaSearch = mysql_query("SELECT * FROM meta_info");
							$i = 0;
							while($row = mysql_fetch_array($sqlMetaSearch)){
								$findWord = 0;
								$type = strtolower($row['type']);
								$content = strtolower($row['content']);
								$sep = "\t\n\r ";
								$type = superExplode($type, $sep);
						        $content = superExplode($content, $sep);
						        $typeCount=count($type);
						        $conCount=count($content);
						        for($ti = 0; $ti < $typeCount; $ti ++){//check if the word exist in type
							        if($type[$ti]==$word)
							            $findWord = $findWord + 1;
						        }
						        for($ci = 0; $ci < $conCount; $ci ++){//check if the word exist in content
						       		if($content[$ci]==$word)
						            	$findWord = $findWord + 1;
						        }
						        if($findWord != 0){
						       		$fid = $row['fid'];
						       		$type = $row['type'];
									$content = $row['content'];
						       		$getFileInfo = mysql_query("SELECT * FROM files WHERE fid='".$fid."'");
						       		$frow = mysql_fetch_array($getFileInfo);
						       		$fileName = $frow['name'];
									$url = $frow['url'];
									$fileLink = "<a href= searcher.php?dir=".$url.">".$url."</a>";
									echo '<tr>';
										echo '<td>'.$word.'</td>';
										echo '<td>'.$type.'</td>';
										echo '<td>'.$content.'</td>';
										echo '<td>'.$fileName.'</td>';
										echo '<td>'.$fileLink.'</td>';
									echo '</tr>';
						        }
								$i ++;
							}
						}else{
							$sqlMetaSearch = mysql_query("SELECT * FROM meta_info WHERE type LIKE'" .$word. "'OR content LIKE'".$word."'");
							$typeStore = array();
							$contentStore = array();
							$fidStore = array();
							$i = 0;
							while($row = mysql_fetch_array($sqlMetaSearch)){
								$typeStore[$i] = $row['type'];
								$contentStore[$i] = $row['content'];
								$fidStore[$i] = $row['fid'];
								$i ++;
							}
							for($j = 0; $j < $i; $j ++){
								$getFileInfo = mysql_query("SELECT * FROM files WHERE fid='".$fidStore[$j]."'");
								$frow = mysql_fetch_array($getFileInfo);
								$fileName = $frow['name'];
								$url = $frow['url'];
								$fileLink = "<a href= searcher.php?dir=".$url.">".$url."</a>";
								echo '<tr>';
									echo '<td>'.$originalWord.'</td>';
									echo '<td>'.$typeStore[$j].'</td>';
									echo '<td>'.$contentStore[$j].'</td>';
									echo '<td>'.$fileName.'</td>';
									echo '<td>'.$fileLink.'</td>';
								echo '</tr>';
							}
						}
						echo '</table>';
					}else{
						echo "passing value error!<br>";
					}
				}
			}


			if(isset($_POST['formSubmit'])){
				if(!empty($_POST['userChoice'])){
					if(!empty($_POST['userKeyword'])){
						$choice = $_POST['userChoice'];
						$keyword = $_POST['userKeyword'];
						$tempWord = str_replace("*","", $keyword);
						setcookie('kw', $tempWord);
						$choiceNum = count($choice);
						if($choiceNum == 1){
							foreach($choice as $checkValue){
				                if($checkValue == $fullTextSearch){
				                    search($fullTextSearch, $keyword);
				                }else if($checkValue == $metaInfoSearch){
				                	search($metaInfoSearch, $keyword);
				                }else{
				                	echo "STH MUST BE WRONG!";
				                }
			            	}
						}else{ //choiceNum == 2
							search($fullTextSearch, $keyword);
				            search($metaInfoSearch, $keyword);
						}
					}else{//if user didn't enter the keyword for searching
						echo "Please Enter a Keyword for Searching... <br>";
					}			
				}else{ //if user didn't select anything from the check box
					echo "Please Select at Least One Search Way... <br>";
				}
			}
		?>
	</body>
</html>