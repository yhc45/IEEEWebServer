<!DOCTYPE html>

<head>
	<link rel="stylesheet" type="text/css" href="weathers.css">
	<link rel="stylesheet" type="text/css" href="format.css">
	<link rel="stylesheet" type="text/css" href="analytics.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
	
  <!-- omit following line for demo -->
  <link rel="stylesheet" type="text/css" href="../navbar.css"> 
	
  <title>WeatherBox QP Fall 2016</title>
  <?php
   	date_default_timezone_set('America/Los_Angeles');
 
  ?>
  <?php
    include "navbar.html";
	  echo "</div>";
    function connectMongo() {
		  $connection = new MongoClient("mongodb://admin:admin@ds019926.mlab.com:19926/qpdemo");
		  $database = $connection->qpdemo;
		  return $database;
	  }
  ?>


</head>

<body class="temperature_container">
  
  <div class="table">
    <table>
      <tr>
        <td>Date Time</td>
        <td>Humidity (%)</td>
      </tr>

      <?php
        $db = connectMongo();
        $collection = $db->hum;
        $cursor = $collection->find();
        $cursor = $cursor->sort(array('num' => -1));
        $cursor = $cursor->limit(50);
        foreach ($cursor as $doc) {
      ?>

        <tr>
          <td><?php echo $doc['time']; ?></td>
          <td><?php echo number_format($doc['val'],1); ?></td>
        </tr>

      <?php
        }
      ?>
    </table>
  </div>

  <div class="line">
    <h3>Previous 24 Measurements. Time vs Percent Humidity</h3><br>
    <canvas id="line_graph" width="800" height="225"></canvas>
  </div>

  <div class="bar" id="bar">
    <h3>Percentage of Measurements in Ranges</h3><br>
    <canvas id="bar_graph" width="800" height="225"></canvas>
    <div id="bar_legend"></div>
  </div>

  <div class="pi">
    <h3>Percentage of Measurements in Each Configuration Zone</h3><br>
    <canvas id="pi_chart" width="800" height="225"></canvas>
  </div>

  <?php
    $db = connectMongo();
    $collection = $db->hum;
    $num_entries = $collection->count();
    // Configure data for line graph
    $cursor = $collection->find();
    $cursor = $cursor->sort(array('num' => -1));
    $limCursor = $cursor->limit(24);
    $line_labels = "";
    $line_data = "";
    foreach ($limCursor as $doc) {
      $time = split(" ", $doc['time'], 2)[1];
      $time = substr($time,0,5);
      $val = number_format($doc['val'],1);
      $line_labels =  '"' . $time . 
                      '",' . $line_labels;
      $line_data = $val . "," . $line_data;
    }
    $line_labels = trim($line_labels, ",");
    $line_data = trim($line_data, ",");
    // END LINE DATA
    // Configure data for bar graph
    $cursor = $collection->find();
    $cursor = $cursor->sort(array('num' => -1));
    $bardata = "";
    $count = array(0,0,0,0,0,0,0,0,0,0);
    foreach ($cursor as $doc) {
      $count[$doc['val']/10]++;
    }
    
    $lows = count[0] + count[1] + count[2] + count[3];
    $bardata =  floor($lows/$num_entries*100).",".
                floor($count[4]/$num_entries*100).",".
                floor($count[5]/$num_entries*100).",".
                floor($count[6]/$num_entries*100).",".
                floor($count[7]/$num_entries*100).",".
                floor($count[8]/$num_entries*100).",".
                floor($count[9]/$num_entries*100);
    // END BAR DATA
    // Configure data for pie chart
    $cPref = $db->pref;
    $configCursor = $cPref->find(array('name' => 'thresholds'));
    $configdoc = $configCursor->getNext();
    $cursor = $collection->find();
    $cursor = $cursor->sort(array('num' => -1));
    $low;
    $good;
    $high;
    foreach ($cursor as $doc) {
      if ($doc['val'] > $configdoc['hum_high']) {
        $high++;
      } else if ($doc['val'] < $configdoc['hum_low']) {
        $low++;
      } else {
        $good++;
      }
    }
    $high = floor($high/$num_entries*100);
    $good = floor($good/$num_entries*100);
    $low = floor($low/$num_entries*100);
  ?>

  <script>
    var line_data = {
      labels : [<?php echo $line_labels; ?>],
      datasets : [
        {
          fillColor : "rgba(172,194,132,0.4)",
          strokeColor : "#ACC26D",
          pointColor : "fff",
          pointStrokeColor : "#9DB86D",
          data : [<?php echo $line_data; ?>]
        }
      ]
    }
    var line_graph = document.getElementById('line_graph').getContext('2d');
    new Chart(line_graph).Line(line_data);
    
    var bar_data = {
      labels : ["0-40","40-50","50-60","60-70","70-80","80-90","90-100"],
      datasets : [
        {
          label: "Percentage of measurements in Ranges",
          fillColor : "#48A497",
          strokeColor : "#48A4D1",
          data : [<?php echo $bardata; ?>]
        }
      ]
    }
    var bar_graph = document.getElementById('bar_graph').getContext('2d');
    new Chart(bar_graph).Bar(bar_data);
    var pi_data = [
      {
        label: "Hot",
        value: <?php echo $high; ?>,
        color:"#cc0000"
      },
      {
        label : "Cold",
        value : <?php echo $low; ?>,
        color : "#0000cc"
      },
      {
        label : "Comfortable",
        value : <?php echo $good; ?>,
        color : "#00cc00"
      }
    ];
    var pi_chart = document.getElementById('pi_chart').getContext('2d');
    new Chart(pi_chart).Pie(pi_data);
  </script>

</body>

</html>
