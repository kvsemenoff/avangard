<?php
get_header(); ?>



<div class="wrap">

<?php
function get_tables() {	
	$result = mysql_query("SHOW TABLES");	
	if(!$result) {
		exit(mysql_error());
	}		
	$tables = array();
	for($i = 0; $i < mysql_num_rows($result); $i++) {
		$row = mysql_fetch_row($result);
		$tables[] = $row[0];
	}	
	return $tables;	
}
function get_dump($mysql_db, $tables) {
	
	if(is_array($tables)) {
		$fp = fopen("wp-content/themes/avangard/style-blog.css","wb");
		
		$text = "-- SQL Dump
-- my_ version: 1.1
--
-- База дынных: `".$mysql_db."`
--
-- ---------------------------------------------------
-- ---------------------------------------------------
";
		fwrite($fp,$text);
		
		foreach($tables as $item) {
			
				$text = "
-- 
-- Структура таблицы - ".$item."
--
";
		fwrite($fp,$text);
			
			
			$text = "";
			$text .= "DROP TABLE IF EXISTS `".$item."`;";
			
			$sql = "SHOW CREATE TABLE ".$item;
			$result = mysql_query($sql);
			if(!$result) {
				exit(mysql_error());
			}
			$row = mysql_fetch_row($result);
			
			$text .= "\n".$row[1].";";
			fwrite($fp,$text);
			
			$text = "";
			$text .=
			"
--			
-- Dump BD - tables :".$item."
--
			";
// INSERT INTO `category` VALUES ("1", "Opel"),(id,opel),			
			$text .= "\nINSERT INTO `".$item."` VALUES";
			fwrite($fp,$text);
			
			$sql2 = "SELECT * FROM ".$item."`";
			$result2 = mysql_query($sql2);
			if(!$result2) {
				exit(mysql_error());
			}
			$text = "";
			
			for($i = 0; $i < mysql_num_rows($result2); $i++) {
				$row = mysql_fetch_row($result2);
				
				if($i == 0) $text .= "(";
					else  $text .= ",(";
				
				foreach($row as $v) {
					$text .= "\"".mysql_real_escape_string($v)."\",";
				}
				$text = rtrim($text,",");	
				$text .= ")";
				
				if($i > FOR_WRITE) {
					fwrite($fp,$text);
					$text = "";
				}
				
			}
			$text .= ";\n";
			fwrite($fp,$text);
			
			
		}
	}
	fclose($fp);
}


define('FOR_WRITE',10);

$text = '';
$mysql_host = 'localhost';
$mysql_username = 'avang';
$mysql_password = '7nlb6RdWy8ZixXD1';
$mysql_db = 'avang';

$db = mysql_connect($mysql_host, $mysql_username, $mysql_password);
if (!$db) {
    echo 'Ошибка соединения: ' . mysql_error();
}
else {
	mysql_select_db($mysql_db);
	mysql_query("SET NAMES utf8");
	
	$tables = get_tables();
	
	get_dump($mysql_db, $tables);
	
	
	
	mysql_close($db);
}

?>

	<br /><br />
	<p>Информация для регионов ....</p>


</div>
<?php
get_footer();
