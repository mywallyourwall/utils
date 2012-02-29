	<?php require_once('jsmin.php'); ?>

<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">

	<style>
	article, aside, details, figcaption, figure, footer, header, hgroup, nav, section { display: block; }
	[hidden] { display: none; }
	html { font-size: 100%; overflow-y: scroll; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
	body, button, input, select, textarea { margin: 0; 
    color: #000;
	-webkit-font-smoothing: antialiased;
	font-smoothing: antialiased;
	font-smooth: always;
	text-rendering: optimizeLegibility;
	font: normal 100% "Book Antiqua", Palatino, "Palatino Linotype", "Palatino LT STD", Georgia, serif}
	body { line-height: 1.231;padding:100px; }
	a { color: #0b610b; 
	-webkit-transition: all .4s ease-in-out;
	-moz-transition: all .4s ease-in-out;
	-o-transition: all .4s ease-in-out;
	-ms-transition: all .4s ease-in-out;
	transition: all .4s ease-in-out;
	}
	a:visited { color: #323232; }
	a:hover { color: #2db9c6; }
	a:focus { outline:none;}
	ul, ol { margin: 1em 0; padding: 0 0 0 40px; }
	form { margin: 0; }
	fieldset { border: 0; margin: 0; padding: 0; }
	label { cursor: pointer; }
	legend { border: 0; *margin-left: -7px; padding: 0;margin:0 0 40px 0;font-size:18px;font-weight:700;}
	button, input, select, textarea { font-size: 100%; margin: 0; vertical-align: baseline; *vertical-align: middle; }
	button, input { line-height: normal; *overflow: visible; }
	button, input[type="button"], input[type="reset"], input[type="submit"] { cursor: pointer; -webkit-appearance: button; }
	input[type="checkbox"], input[type="radio"] { box-sizing: border-box; }
	input[type="search"] { -webkit-appearance: textfield; -moz-box-sizing: content-box; -webkit-box-sizing: content-box; box-sizing: content-box; }
	input[type="search"]::-webkit-search-decoration { -webkit-appearance: none; }
	button::-moz-focus-inner, input::-moz-focus-inner { border: 0; padding: 0; }
	textarea { overflow: auto; vertical-align: top; resize: vertical; }
	input:valid, textarea:valid {  }
	input:invalid, textarea:invalid { background-color: #f0dddd; }
	form label {
		font-weight:700;
		display:block;
		margin:0 10px 10px 0;
		padding:8px;
	-webkit-transition: all .4s ease-in-out;
	-moz-transition: all .4s ease-in-out;
	-o-transition: all .4s ease-in-out;
	-ms-transition: all .4s ease-in-out;
	transition: all .4s ease-in-out;
	}

	form label:hover {
		background:#eee;
	}

	form input,
	form textarea,
	input:required:invalid, 
	input:focus:invalid {
		border-width:2px 1px 1px 1px;
		background:#f1f1f1;
		padding:5px;
	    -moz-box-sizing: border-box;
		box-sizing:border-box;
		-webkit-box-sizing: border-box;
	    font: 1em "Book Antiqua",Palatino,"Palatino Linotype","Palatino LT STD",Georgia,serif;	
	}
	form textarea,
	form input {
		width:300px
	}
	form input:focus,
	form textarea:focus {
	    -moz-box-sizing: border-box;
		box-sizing:border-box;
		-webkit-box-sizing: border-box;
	}

	input:required:invalid, input:focus:invalid {
		border-width:2px 1px 1px 1px;
		border-color:#000;
	}
	input:required:valid { 
	}

	form input[type="submit"],
	form input[type="checkbox"] {
		width:auto;
		margin:10px 0;
	}
	.minify {
		background-color:#eee;
		font-weight:300;
		font-style:italic;
	}
	::-webkit-input-placeholder {
	    color:    #999;
	}
	:-moz-placeholder {
	    color:    #999;
	}	
	#progress {
		border:1px solid #0b610b;
		padding:40px;
		background-color:#9EDB9E;
		margin: 0 0 40px 0;
	}
	#progress p {
		font-size:26px;
	}	
	</style>
</head>

<header>
	<h1>jsProcessor - now ported to PHP!</h1>
</header>


<?php

if (isset($_POST["concat"])) {
	echo "<section id=\"progress\">";
	 echo "<ol>";


    //I've made assumption outfile is in same path as source files
    $path = $_POST["dest_path"];
    $minify = $_POST["minify"];
    $dest_file = $path . $_POST["final_filename"];
    $output_stream = "";
    file_put_contents($dest_file, $output_stream);


	foreach ($_POST["file_names"] as $key) {
		if (isset($key)){
			echo "<li>" . $key . " added</li>";
			$file_to_add = $path . $key;
			$output_stream = $output_stream . file_get_contents($file_to_add);
		}
	}

    file_put_contents($dest_file, $output_stream);


	if (isset($minify) && $minify == "on"){
		 $js = JSMin::minify(file_get_contents($dest_file));
		 file_put_contents($dest_file, $js);
	} 

     echo "</ol>";

    echo "<p>Complete! Your final file  <a href=\"".$dest_file."\">". $_POST["final_filename"] ."</a> is ready. <a href=\"jsProcessor.php\">I wanna do it again!</a></p>";

    echo "</section>";

} elseif (isset($_POST["select_dir_done"])) {

?>

<section>
<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" id="jp_form">
	<legend>Choose which files you'd like to include in the final file</legend>
	<fieldset>
		<label>Name of final file: <input required type="text" name="final_filename" value="" placeholder="eg: payload.js"> </label>
		<p>Select your files or <a href="#" id="select_all">select all</a></p>

<?php
if ($handle = opendir($_POST["dest_path"])) {

    /* Das ist der korrekte Weg, ein Verzeichnis zu durchlaufen. */
    while (false !== ($file = readdir($handle))) {
    	if (preg_match("/[a-zA-Z0-9-_.]+.js/",$file)) {   	
			echo "<label> " . $file . ": <input type=\"checkbox\" name=\"file_names[".$file."]\" value=\"".$file."\"></label>";
		}

    }

    echo "<label class='minify'> Minify?: <input class='minify' type=\"checkbox\" name=\"minify\"></label>";
    closedir($handle);
}
?>

	<input type="hidden" name="dest_path" value="<?php echo $_POST["dest_path"] ?>">
	   <input type="hidden" name="concat" value="true">
	   <input type="submit" class="button" name="submit" value="Do it!">
	</fieldset>   
</form>
</section>


<?php
} else {

?>


<section>
<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" id="jp_form">
	<legend>Where are your *.js files?</legend>
	<fieldset>
		<label>System path relative to this website: <input required type="text" name="dest_path" value="" placeholder="eg: ../myprojects/js/"> </label>
	   <input type="hidden" name="select_dir_done" value="true">
	   <input type="submit" class="button" name="submit" value="Do it!">
	</fieldset>   
</form>
</section>


<?php
} 

?>

  <script>

(function(){
	
	var select_all = document.getElementById('select_all'),
	jp_form = document.getElementById('jp_form'),
	selectOn = true;

	if (select_all && jp_form) {
		var checkboxes = jp_form.getElementsByTagName('input');
		

		select_all.onclick = function(){
			for (var i=0,j=checkboxes.length;i<j;++i){
				if (checkboxes[i].getAttribute('type') == 'checkbox' && checkboxes[i].className !== 'minify') {
					if (selectOn) {
						checkboxes[i].setAttribute('checked',selectOn);
						checkboxes[i].checked = true;
						
					} else {
						checkboxes[i].removeAttribute('checked');
						checkboxes[i].checked = false;
						
					}
					
				} 
				
			}
				
			if (selectOn) {
				this.innerHTML = 'deselect all'
				selectOn = false;
			} else {
				this.innerHTML = 'select all'
				selectOn = true;
			}

			return false;
		};
		
	}

})();

</script>
</body>
</html>