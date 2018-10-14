<?php

	if(isset($_GET["delete"]) && $_GET["delete"] == true)
	{
		$name = $_POST["filename"];
		if(file_exists('./uploads/'.$name))
		{
			unlink('./uploads/'.$name);
			$link = mysql_connect("localhost", "root", "");
			mysql_select_db("dropzone", $link);
			mysql_query("DELETE FROM uploads WHERE name = '$name'", $link);
			mysql_close($link);
			echo json_encode(array("res" => true));
		}
		else
		{
			echo json_encode(array("res" => false));
		}
	}