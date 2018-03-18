<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
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
        // Create database
        $db_sql = "CREATE DATABASE IF NOT EXISTS assignment";
        if ($conn->query($db_sql) === TRUE) {
            //echo "Database created successfully </br>";
        } else {
            echo "Error creating database: " . $conn->error."</br>";
        }
        //$mysqli->query("Create database if not exists MyDB");
        
        //Select database
        mysqli_select_db($conn,"assignment");
        
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
            echo "Error creating table: " . $conn->error."</br>";
        }


        $line_array=array();
        
        //Read text file
        $myfile = fopen("delegated-apnic-latestDatabase.txt", "r") or die("Unable to open file!");
        
        //Loop through text file line by line
        while(! feof($myfile))
        { 
            //adding all lines into an array.
             array_push($line_array,fgets($myfile));

        }
        fclose($myfile);
        //Loop through array
        foreach ($line_array as $line){
            // fist character to identify the commented lines
             $first_charactor = $line[0];
             if($first_charactor!='#'){
                 $data_array=explode( "|",$line);
                 //getting lines with first value "alphanic" and remove summary lines
                 if($data_array[0]=="apnic" && trim($data_array[5])!= "summary"){
                     // Insert data into database
                     $insert = "INSERT INTO `datastore`( `registry`, `cc`, `type`, `start`, `value`, `date`, `status`,`opaqueid`) VALUES ('". $data_array[0]."','".$data_array[1]."','".
                              $data_array[2]."','".$data_array[3]."','".$data_array[4]."','".$data_array[5]."','".$data_array[6]."','')";
                     //$insert="insert into datastore values ()";

                     if ($conn->query($insert) === TRUE) {} else {
                        echo "Error: " . $insert . "<br>" . $conn->error;
                    }
                     
                    
                 }
                 
            }
        }
        
        $report_sql = "SELECT `cc`,`type`, COUNT(`value`) as Count,`date` FROM `datastore` WHERE YEAR(`date`)= 2016 AND `type`='asn' GROUP BY `cc`";
        $result = mysqli_query($conn, $report_sql);
        
        
        //var_dump($data_array);
        
        //Generate report using Google API
        ?>
        <script src="js/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>  
           <script type="text/javascript">  
           google.charts.load('current', {'packages':['corechart']});  
           google.charts.setOnLoadCallback(drawChart);  
           function drawChart()  
           {  
                var data = google.visualization.arrayToDataTable([  
                          ['Country', 'Count'],  
                          <?php  
                          while($row = mysqli_fetch_array($result))  
                          {  
                               echo "['".$row["cc"]."', ".$row["Count"]."],";  
                          }  
                          ?>  
                     ]);  
                var options = {  
                      title: 'Count of value against countries',  
                      //is3D:true,  
                      pieHole: 0.4  
                     };  
                var chart = new google.visualization.PieChart(document.getElementById('piechart'));  
                chart.draw(data, options);  
           }  
           
           var ctx = document.getElementById("myChart");
var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["2015-01", "2015-02", "2015-03", "2015-04", "2015-05", "2015-06", "2015-07", "2015-08", "2015-09", "2015-10", "2015-11", "2015-12"],
    datasets: [{
      label: 'Count',
      data: [12, 19, 3, 5, 2, 3, 20, 3, 5, 6, 2, 1],
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'
      ],
      borderColor: [
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
      ],
      borderWidth: 1
    }]
  },
  options: {
    responsive: false,
    scales: {
      xAxes: [{
        ticks: {
          maxRotation: 90,
          minRotation: 80
        }
      }],
      yAxes: [{
        ticks: {
          beginAtZero: true
        }
      }]
    }
  }
});
           </script>  
        <br /><br />  
           <div style="width:900px;">  
                <h3 align="center">Report</h3>  
                <br />  
                <div id="piechart" style="width: 900px; height: 500px;"></div>  
           </div>  
        
    </body>
</html>
