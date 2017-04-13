<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<style>
	.error{
		color: red;
		font-size: 10pt;
	}
</style>
<body>

	<form action="" method="POST" enctype="multipart/form-data" >
		<input type="text" name="feild" placeholder="Ведіть шість комбінацій"><br>
		<input type=file name=uploadfile><br>
		<input type="submit" name="btn-send" value="Перевірити"><br>
	</form>

	</body>
</html>

<?php 
	// default data
	$uploaddir = './csv/';
	$error = array("font_found_file" => "<b class='error'>Помилка при завантаженні файлу!</b>",
				   "exceeding_limit" => "<b class='error'>Error: кількість комбінацій повина дорівнювати 6</b>",
				   "exceeding_range" => "<b class='error'>Error: комбінація перевищує діапазон (1 - 52)</b>", 
				   "diget_repeat"    => "<b class='error'>Error: числа повторются</b>");
	$limit = array("min" => 4, "max" => 9);	
	$length = 1000;
	$limit_exceeding = array("begin" => 1, "end" => 52);
	// --------------

	if (isset($_POST['btn-send'])){	

		$feild = explode(",",$_POST['feild']);
		if (count($feild) != 6) {
			echo $error["exceeding_limit"];
			exit(0);
		}
		for ($i = 0; $i < count($feild); $i++ ){
			if ($feild[$i] < $limit_exceeding["begin"] or $feild[$i] > $limit_exceeding["end"]){
				echo $error["exceeding_range"];
				exit(0);
			}
		for ($j =0; $j < count($feild); $j++){
			if ($j == $i) continue;
			if ($feild[$i] == $feild[$j]){
				echo $error["diget_repeat"];
				exit(0);
				}
			}
		}

	}else exit(0);

	$uploadfile = $uploaddir.basename($_FILES['uploadfile']['name']);

	if (copy($_FILES['uploadfile']['tmp_name'], $uploadfile)){
		$row = 1;
		if (($handle = fopen($uploaddir.$_FILES['uploadfile']['name'], "r")) !== FALSE) {
			$array = [0,0,0,0,0,0];
			$dump = [];
		    while (($data = fgetcsv($handle, $length, ";")) !== FALSE) {
		        $num = count($data);
		        $row++;
		    	if ($row == 2) continue;

		        for ($c = 0; $c < $num; $c++) {
					if ($c >= $limit['min'] and $c <= $limit['max'])
						$dump[] = $data[$c];
		        }

		        if (($test = count(array_intersect($feild, $dump))) > 0){
		        	$array[$test-1] ++;
		        }
		        $dump = [];

		    }
		    fclose($handle);
		}	
	}
	else { 
		echo $error['font_found_file']; 
		exit(0); 
	}



?>

<table border=1>
	<thead>
		<tr>
			<td>Вгадано</td>
			<td>Кількість разів</td>
		</tr>
	</thead>
	<tbody>
		<?php
			for ($i = 1; $i <= count($array);$i++){
				echo "
					<tr>
						<td>(${i}/6)</td>
						<td> ${array[$i-1]}</td>
					</tr>
					 ";		
			}
		?>
	</tbody>
</table>