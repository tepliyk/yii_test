<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <title>Document</title>
</head>

<body>
<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div class="panel panel-primary" style="text-align: center; padding: 10px; ">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" name="feild" placeholder="Ведіть шість комбінацій" style="
    width: 300px;
    height: 50px;
"><br>
                <input class="btn btn-default" type=file name=uploadfile style="
    margin: 0 auto;
"><br>
                <input class="btn btn-success" type="submit" name="btn-send" value="Перевірити"><br>
            </form>

            <?php
            // default data
            $uploaddir = './csv/';
            $error = array("font_found_file" => "<b class='error'>Помилка при завантаженні файлу!</b>",
                "exceeding_limit" => "<b class='error'>Error: кількість комбінацій повина дорівнювати 6</b>",
                "exceeding_range" => "<b class='error'>Error: комбінація перевищує діапазон (1 - 52)</b>",
                "diget_repeat" => "<b class='error'>Error: числа повторются</b>");
            $limit = array("min" => 4, "max" => 9);
            $length = 1000;
            $limit_exceeding = array("begin" => 1, "end" => 52);
            // --------------

            if (isset($_POST['btn-send'])) {

                $feild = explode(",", $_POST['feild']);
                if (count($feild) != 6) {
                    echo '<p class="bg-danger">' . $error["exceeding_limit"] . '</p>';
                    exit(0);
                }
                for ($i = 0; $i < count($feild); $i++) {
                    if ($feild[$i] < $limit_exceeding["begin"] or $feild[$i] > $limit_exceeding["end"]) {
                        echo '<p class="bg-danger">' . $error["exceeding_range"] . '</p>';
                        exit(0);
                    }
                    for ($j = 0; $j < count($feild); $j++) {
                        if ($j == $i) continue;
                        if ($feild[$i] == $feild[$j]) {
                            echo '<p class="bg-danger">' . $error["exceeding_range"] . '</p>';
                            exit(0);
                        }
                    }
                }

            } else exit(0);

            $uploadfile = $uploaddir . basename($_FILES['uploadfile']['name']);

            if (copy($_FILES['uploadfile']['tmp_name'], $uploadfile)) {
                $row = 1;
                if (($handle = fopen($uploaddir . $_FILES['uploadfile']['name'], "r")) !== FALSE) {
                    $array = [0, 0, 0, 0, 0, 0];
                    $dump = [];
                    while (($data = fgetcsv($handle, $length, ";")) !== FALSE) {
                        $num = count($data);
                        $row++;
                        if ($row == 2) continue;

                        for ($c = 0; $c < $num; $c++) {
                            if ($c >= $limit['min'] and $c <= $limit['max'])
                                $dump[] = $data[$c];
                        }

                        if (($test = count(array_intersect($feild, $dump))) > 0) {
                            $array[$test - 1]++;
                        }
                        $dump = [];

                    }
                    fclose($handle);
                }
            } else {
                echo '<p class="bg-danger">' . $error['font_found_file'] . '</p>';
                exit(0);
            }

            ?>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>
</body>
</html>
<table class="table table-hover table-bordered"">
<thead>
<tr>
    <td>Вгадано</td>
    <td>Кількість разів</td>
</tr>
</thead>
<tbody>
<?php
for ($i = 1; $i <= count($array); $i++) {
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