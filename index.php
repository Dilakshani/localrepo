<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>APNIC Report</title>

        <!-- Bootstrap core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="css/small-business.css" rel="stylesheet">

    </head>

    <body>
        <?php
        $servername = "127.0.0.1";
        $username = "root";
        $password = "";
        //$dbname = "assignment";
        //creating required database and table
        $conn = new mysqli($servername, $username, $password);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        //Drop database if exists
        $drop_db_sql = "DROP DATABASE IF EXISTS assignment";
        if ($conn->query($drop_db_sql) === TRUE) {
            //echo "Database created successfully </br>";
        } else {
            echo "Error dropping database: " . $conn->error . "</br>";
        }
       
        // Create database
        $db_sql = "CREATE DATABASE IF NOT EXISTS assignment";
        if ($conn->query($db_sql) === TRUE) {
            //echo "Database created successfully </br>";
        } else {
            echo "Error creating database: " . $conn->error . "</br>";
        }
        //$mysqli->query("Create database if not exists MyDB");
        //Select database
        mysqli_select_db($conn, "assignment");

        //Create Table
        $tb_sql = "CREATE TABLE IF NOT EXISTS datastore 
                   (
                    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                    registry VARCHAR(255),
                    cc VARCHAR(255),
                    type VARCHAR(255),
                    start VARCHAR(255),
                    value VARCHAR(255),
                    date VARCHAR(255),
                    status VARCHAR(255),
                    opaqueid VARCHAR(255)
                   )";

        if ($conn->query($tb_sql) === TRUE) {
            //echo "Table datastore created successfully </br>";
        } else {
            echo "Error creating table: " . $conn->error . "</br>";
        }


        $line_array = array();

        //Read text file
        $myfile = fopen("delegated-apnic-latestDatabase.txt", "r") or die("Unable to open file!");

        //Loop through text file line by line
        while (!feof($myfile)) {
            //adding all lines into an array.
            array_push($line_array, fgets($myfile));
        }
        fclose($myfile);
        //Loop through array
        foreach ($line_array as $line) {
            // fist character to identify the commented lines
            $first_charactor = $line[0];
            if ($first_charactor != '#') {
                $data_array = explode("|", $line);
                //getting lines with first value "alphanic" and remove summary lines
                if ($data_array[0] == "apnic" && trim($data_array[5]) != "summary") {
                    // Insert data into database
                    $insert = "INSERT INTO `datastore`( `registry`, `cc`, `type`, `start`, `value`, `date`, `status`,`opaqueid`) VALUES ('" . $data_array[0] . "','" . $data_array[1] . "','" .
                            $data_array[2] . "','" . $data_array[3] . "','" . $data_array[4] . "','" . $data_array[5] . "','" . $data_array[6] . "','')";
                    //$insert="insert into datastore values ()";

                    if ($conn->query($insert) === TRUE) {
                        
                    } else {
                        echo "Error: " . $insert . "<br>" . $conn->error;
                    }
                }
            }
        }

        $report_sql = "SELECT `cc`,`type`, COUNT(`value`) as Count,`date` FROM `datastore` WHERE YEAR(`date`)= 2016 AND `type`='asn' GROUP BY `cc`";
        //$result = mysqli_query($conn, $report_sql);
        $result = $conn->query($report_sql);

        //Get Country list
        $sql_counties = "SELECT distinct `cc` FROM `datastore` WHERE `type`='asn' GROUP BY `cc`";
        $result_countries = $conn->query($sql_counties);

        //Get Year list
        $sql_year = "SELECT DISTINCT YEAR(`date`) FROM `datastore` WHERE `type`='asn' GROUP BY `date`";
        $result_year = $conn->query($sql_year);

        //Get Type list
        $sql_type = "SELECT DISTINCT `type` FROM `datastore` GROUP BY `type`";
        $result_type = $conn->query($sql_type);
        ?>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">APNIC</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item active">
                            <a>Report Generator</a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container">

            <div class="row my-4">
                <div class="col-md-12">
                    </br></br></br>
                    <form class="form-horizontal" method="POST">
                        <fieldset>

                            <!-- Select Basic -->
                            <div class="form-group">
                                <label class="row col-md-4 control-label" for="select">Select parameters to generate report</label>
                                <div class="row col-md-12">
                                    <table border="0" cellpadding="10">
                                        <tr>
                                            <th width="25%">Year</th>
                                            <th width="25%" colspan="2">Type</th>
                                        </tr>
                                        <tr>
                                            <td width="25%">

                                                <select id="selectyear" name="selectyear" class="form-control">
                                                    <option value="-1">---SELECT YEAR---</option>
                                                    <?php
                                                    while ($row = $result_year->fetch_array()) {
                                                        echo "<option value=\"{$row['YEAR(`date`)']}\">";
                                                        echo $row['YEAR(`date`)'];
                                                        echo "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td width="25%">
                                                <select id="selecttype" name="selecttype" class="form-control">
                                                    <option value="-1">---SELECT TYPE---</option>
                                                        <?php
                                                        while ($row = $result_type->fetch_assoc()) {
                                                            echo "<option value=\"{$row['type']}\">";
                                                            echo $row['type'];
                                                            echo "</option>";
                                                        }
                                                        ?>
                                                </select>
                                            </td>
                                            <!-- Button -->
                                            <td width="25%">
                                                <button type="submit" name="generate" class="btn btn-primary" onclick="showDiv()">Generate Report</button>
                                            </td>
                                        </tr>
                                        <script type="text/javascript">
                                            function showDiv() {
                                                document.getElementById('table').style.display = "block";
                                            }
                                        </script>

                                    </table>

                                </div>

                            </div>

                        </fieldset>
                    </form>

                </div>
            </div>
            <div class="row col-md-12">
                <?php 
                if(isset($_POST['selectyear'])){
                    $selected_year = $_POST['selectyear'];
                } else {
                    $selected_year = 2016;
                }
                if(isset($_POST['selecttype'])){
                    $selected_type = $_POST['selecttype'];
                } else {
                    $selected_type = 'asn';
                }
                ?>
                <h6>Below Graph represent total <b><?php echo $selected_type;?></b> count according to the year <b><?php echo $selected_year;?></b> </h6>
                
                </br>
                <?php include_once './linegraph.html'; ?>
            </div>

                <?php
                $report_sql = "SELECT `cc`, COUNT(`value`) as Count FROM `datastore` WHERE YEAR(`date`)= 2016 AND `type`='asn' GROUP BY `cc`";
                $result = mysqli_query($conn, $report_sql);
                ?>   

        </div>
        <!-- /.container -->

        <!-- Footer -->
        <footer class="py-5 bg-dark">
            <div class="container">
                <p class="m-0 text-center text-white">Copyright &copy; Lisanka Nagahatenna 2018</p>
            </div>
            <!-- /.container -->
        </footer>

        <!-- Bootstrap core JavaScript -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    </body>

</html>
