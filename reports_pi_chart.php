<?php

  $nav_selected = "REPORTS"; 
  $left_buttons = "YES"; 
  $left_selected = "PICHART"; 

  include("./nav.php");
  global $db;

  
  /*
  FP6
Application Report based on the Status (app_status report)
Component Report based on the Status (cmp_status report)
Request Report based on the Status (request_status report)
Request Step Report based on the Step (request_step report)
Use Google Charts to display a PI Chart for each of the Reports. 
Plug in this functionality under "Report" menu. Add a left-hand side menu option called 
"BOM Reports".
When "Reports --> BOM Reports" is clicked, these 4 pi-charts need to show up at the top of the page. 
Clicking on any slice of the PI chart will show the details of that slice in a TABLE below the charts. 
(Note: We will support the Bar charts in a future iteration).
  */
  
  
  ?>
  
  <div class="right-content">
    <div class="container">

      <h3 style = "color: #01B0F1;">Reports -> Pie Chart</h3>
	  
	  
<html>
  <head>
  
<?php
			// https://www.w3schools.com/php/func_mysqli_multi_query.asp
			// God awful multi-query, it's not pretty

			// Arrays to hold respective results
			$app_stats = [];
			$cmp_stats = [];
			$req_stats = [];
			$req_steps = [];
			
			
			$sql = "SELECT COUNT(app_status) FROM sbom WHERE app_status='released';";
			$sql .= "SELECT COUNT(app_status) FROM sbom WHERE app_status='in_progress';";
			$sql .= "SELECT COUNT(app_status) FROM sbom WHERE app_status='cancelled';";
			
			$sql .= "SELECT COUNT(cmp_status) FROM sbom WHERE cmp_status='released';";
			$sql .= "SELECT COUNT(cmp_status) FROM sbom WHERE cmp_status='approved';";
			$sql .= "SELECT COUNT(cmp_status) FROM sbom WHERE cmp_status='pending';";
			$sql .= "SELECT COUNT(cmp_status) FROM sbom WHERE cmp_status='submitted';";
			$sql .= "SELECT COUNT(cmp_status) FROM sbom WHERE cmp_status='in_review';";
			
			$sql .= "SELECT COUNT(request_status) FROM sbom WHERE request_status='submitted';";
			$sql .= "SELECT COUNT(request_status) FROM sbom WHERE request_status='approved';";
			$sql .= "SELECT COUNT(request_status) FROM sbom WHERE request_status='pending';";
			
			$sql .= "SELECT COUNT(request_step) FROM sbom WHERE request_step='Review Step';";
			$sql .= "SELECT COUNT(request_step) FROM sbom WHERE request_step='Approval Step';";
			$sql .= "SELECT COUNT(request_step) FROM sbom WHERE request_step='Inspection Step';";
			
			
			$result = $db->query($sql);
			
			// Keep track of which results are being stored
			$tracker = 0;
	
			 if (mysqli_multi_query($db,$sql)) {
               
			    do{
					if($result=mysqli_store_result($db)){
						while($row=mysqli_fetch_row($result)){
							
							if($tracker < 3){
								$app_stats[] = $row[0];
							}
							else if($tracker < 8){
								$cmp_stats[] = $row[0];
							}
							else if($tracker < 11){
								$req_stats[] = $row[0];
							}
							else{
								$req_steps[] = $row[0];
							}
							
							$tracker++;
						}
						
					 mysqli_free_result($result);
					}
				}while(mysqli_next_result($db));
				
             }
			 mysqli_close($db);
?>
  
  
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	

	<script type="text/javascript">
      
	  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

	  <?php
	  
		$released  = $app_stats[0];
		$in_prog   = $app_stats[1];
		$cancelled = $app_stats[2];
	  
	  
	  ?>
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Percent'],
		  
          ['Released', <?php echo $released;?>],
		  ['In_Progress', <?php echo $in_prog;?>],	  
		  ['Cancelled', <?php echo $cancelled;?>]
			
		]);

        var options = {
          title: 'Application Report'
        };

        var chart1 = new google.visualization.PieChart(document.getElementById('piechart1'));
		
		
			function selectHandler() {
			var selectedItem = chart1.getSelection()[0];
			
			if (selectedItem) {
				var value = data.getValue(selectedItem.row, 0);
				
				value = prepareParam(value);
				drawTable('request_status' ,value);
			}
			
		}

		// Listen for the 'select' event, and call my function selectHandler() when
		// the user selects something on the chart.
		google.visualization.events.addListener(chart1, 'select', selectHandler);

		
		
        chart1.draw(data, options);
      }
    </script>
	
	
	<script type="text/javascript">
      
	  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

	  <?php
		$c_released   = $cmp_stats[0];
		$c_approved   = $cmp_stats[1];
		$c_pending    = $cmp_stats[2];
		$c_submitted  = $cmp_stats[3];
		$c_in_review  = $cmp_stats[4];
	   ?>  
	   
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Percent'],
          ['Released', <?php echo $c_released;?>],
          ['Approved', <?php echo $c_approved;?>],
          ['Pending', <?php echo $c_pending;?>],
          ['Submitted', <?php echo $c_submitted;?>],
          ['In Review', <?php echo $c_in_review;?>]
		  
        ]);

        var options = {
          title: 'Component Report'
        };

        var chart2 = new google.visualization.PieChart(document.getElementById('piechart2'));

		function selectHandler() {
			var selectedItem = chart2.getSelection()[0];
			
			if (selectedItem) {
				var value = data.getValue(selectedItem.row, 0);
				
				value = prepareParam(value);
				drawTable('cmp_status' ,value);
			}
			
		}

		// Listen for the 'select' event, and call my function selectHandler() when
		// the user selects something on the chart.
		google.visualization.events.addListener(chart2, 'select', selectHandler);

        chart2.draw(data, options);
      }
    </script>
	
	<script type="text/javascript">
      
	  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

	  <?php

			$r_submitted = $req_stats[0];
			$r_approved = $req_stats[1];
			$r_pending = $req_stats[2];
	  
	  ?>
	  
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Percent per category'],
          ['Submitted', <?php echo $r_submitted;?>],
          ['Approved', <?php echo $r_approved;?>],
          ['Pending', <?php echo $r_pending;?>]
        ]);

        var options = {
          title: 'Request Report'
        };

        var chart3 = new google.visualization.PieChart(document.getElementById('piechart3'));
		
		function selectHandler() {
			var selectedItem = chart3.getSelection()[0];
			
			if (selectedItem) {
				var value = data.getValue(selectedItem.row, 0);
				
				value = prepareParam(value);
				drawTable('report_status' ,value);
			}
	
		}

		// Listen for the 'select' event, and call my function selectHandler() when
		// the user selects something on the chart.
		google.visualization.events.addListener(chart3, 'select', selectHandler);
		
        chart3.draw(data, options);
      }
    </script>

	
	<script type="text/javascript">
      
	  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
		  
		<?php 
		
			$rq_review = $req_steps[0];
			$rq_approval = $req_steps[1];
			$rq_inspection = $req_steps[2];
		
		?>
	  
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Percent'],
          ['Review Step', <?php echo $rq_review;?>],
          ['Approval Step', <?php echo $rq_approval;?>],
          ['Inspection Step', <?php echo $rq_inspection;?>]
        ]);

        var options = {
          title: 'Request Step Report'
        };

        var chart4 = new google.visualization.PieChart(document.getElementById('piechart4'));

		
		function selectHandler() {
			var selectedItem = chart4.getSelection()[0];
			
			if (selectedItem) {
				var value = data.getValue(selectedItem.row, 0);
				
				value = prepareParam(value);
				drawTable('request_step' ,value);
			}
	
		}

		// Listen for the 'select' event, and call my function selectHandler() when
		// the user selects something on the chart.
		google.visualization.events.addListener(chart4, 'select', selectHandler);

        chart4.draw(data, options);
      }
    </script>

	<script>
	
	function drawTable(object, category){
		//alert("Drawing the table for "+object);
		var query_params = object + '/' + category;
		
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		
		if (this.readyState == 4 && this.status == 200) {
			//var myObj = JSON.parse(this.responseText);
			document.getElementById("slice_table").innerHTML = this.responseText;
			}
		};
		xmlhttp.open("POST", "db_pichart.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(query_params); 
	}
	
	// Convert slice names into database names 
	function prepareParam(param){
		
		param = param.toLowerCase();
		
		if(param.includes(" ")){
			param = param.replace(/\s/g, "_");
		}
						
		return param;	
	}
	
	</script>
	

  </head>
  
  <body>
    
	<table class="pies">
	
	<tr>
		<td><div id="piechart1"></div></td>
		<td><div id="piechart2"></div></td>
	</tr>
	
	<tr>
		<td><div id="piechart3"></div></td>
		<td><div id="piechart4"></div></td>
	</tr>
	
	</table>

	<div id="slice_table"></div>
	<p id="test"> <?php
	/*
		echo $cmp_stats[0];
		echo $cmp_stats[1];
		echo $cmp_stats[2];
		echo $cmp_stats[3];
		echo $cmp_stats[4];
	
	
					echo "<pre>";
					echo $released."<br />";
					echo $in_prog."<br />";
					echo $cancelled."<br />";
					//print_r($app_stats); echo "<br />"; 
					//print_r($cmp_stats); echo "<br />";
					//print_r($req_stats); echo "<br />";
					//print_r($req_steps); 
					echo "</pre>";
					
	*/
				  ?>
	</p>
	
  </body>
</html>


	  
	  
	</div>
</div>