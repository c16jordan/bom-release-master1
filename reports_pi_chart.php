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
			
			$sql .= "SELECT COUNT(request_status) FROM sbom WHERE request_status='Submitted';";
			$sql .= "SELECT COUNT(request_status) FROM sbom WHERE request_status='Approved';";
			$sql .= "SELECT COUNT(request_status) FROM sbom WHERE request_status='Pending';";
			
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

        var chart = new google.visualization.PieChart(document.getElementById('piechart1'));

        chart.draw(data, options);
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

        var chart = new google.visualization.PieChart(document.getElementById('piechart2'));

        chart.draw(data, options);
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

        var chart = new google.visualization.PieChart(document.getElementById('piechart3'));

        chart.draw(data, options);
      }
    </script>

	
	<script type="text/javascript">
      
	  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
		  
		<?php 
		
			$rq_submitted = $req_steps[0];
			$rq_approved = $req_steps[1];
			$rq_pending = $req_steps[2];
		
		?>
	  
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Percent'],
          ['Submitted', <?php echo $rq_submitted;?>],
          ['Approved', <?php echo $rq_approved;?>],
          ['Pending', <?php echo $rq_pending;?>]
        ]);

        var options = {
          title: 'Request Step Report'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart4'));

        chart.draw(data, options);
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