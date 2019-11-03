<?php

  $nav_selected = "REPORTS"; 
  $left_buttons = "YES"; 
  $left_selected = "BARCHART"; 

  include("./nav.php");
  global $db;

  ?>
  
  
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
			
			$sql .= "SELECT COUNT(request_step) FROM sbom WHERE request_step='review_step';";
			$sql .= "SELECT COUNT(request_step) FROM sbom WHERE request_step='approval_step';";
			$sql .= "SELECT COUNT(request_step) FROM sbom WHERE request_step='inspection_step';";
			
			
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
  
  
  <div class="right-content">
    <div class="container">

      <h3 style = "color: #01B0F1;">Reports -> Bar Chart</h3>
	  
		
	 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	  
	 <script type="text/javascript">
		// Application Report Bar Chart
		
		google.charts.load("current", {packages:["corechart"]});
		google.charts.setOnLoadCallback(drawChart);
			
			
		 <?php
			$released  = $app_stats[0];
			$in_prog   = $app_stats[1];
			$cancelled = $app_stats[2];	
		 ?>
						
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
				["Status", "Percentage", { role: "style" } ],
				['Released', <?php echo $released;?>, "blue"],
				['In_Progress', <?php echo $in_prog;?>, "green"],	  
				['Cancelled', <?php echo $cancelled;?>, "grey"]
			]);

		var view = new google.visualization.DataView(data);
	
		var options = {
			title: "Application Report",
			width: 500,
			height: 200,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
		};
		
		var chart1 = new google.visualization.BarChart(document.getElementById("barchart_values"));
		chart1.draw(view, options);
  }
  </script>




<script type="text/javascript">
		// Component Report Bar Chart
		
		google.charts.load("current", {packages:["corechart"]});
		google.charts.setOnLoadCallback(drawChart);
			
			
		<?php
			$c_released   = $cmp_stats[0];
			$c_approved   = $cmp_stats[1];
			$c_pending    = $cmp_stats[2];
			$c_submitted  = $cmp_stats[3];
			$c_in_review  = $cmp_stats[4];
	   ?>  
						
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
				["Status", "Percentage", { role: "style" } ],
			    ['Released', <?php echo $c_released;?>, "blue"],
				['Approved', <?php echo $c_approved;?>, "red"],
				['Pending', <?php echo $c_pending;?> , "Orange"],
				['Submitted', <?php echo $c_submitted;?>, "Green"],
				['In Review', <?php echo $c_in_review;?>, "Purple"]
			]);

		var view = new google.visualization.DataView(data);
			
		var options = {
			title: "Component Report",
			width: 500,
			height: 200,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
		};
		
		var chart2 = new google.visualization.BarChart(document.getElementById("barchart_values2"));
		chart2.draw(view, options);
  }
  </script>

  
  
  <script type="text/javascript">
		// Request Report Bar Chart
		
		google.charts.load("current", {packages:["corechart"]});
		google.charts.setOnLoadCallback(drawChart);
			
			
		  <?php

			$r_submitted = $req_stats[0];
			$r_approved = $req_stats[1];
			$r_pending = $req_stats[2];
	  
		?>
						
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
				["Status", "Percentage", { role: "style" } ],
			    ['Submitted', <?php echo $r_submitted;?>, "Green"],
				['Approved', <?php echo $r_approved;?>, "Red"],
				['Pending', <?php echo $r_pending;?>, "Orange"]
			]);

		var view = new google.visualization.DataView(data);

		var options = {
			title: "Request Report",
			width: 500,
			height: 200,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
		};
		
		var chart3 = new google.visualization.BarChart(document.getElementById("barchart_values3"));
		chart3.draw(view, options);
  }
  </script>
  
  
  
  
  <script type="text/javascript">
		// Request Step Report Bar Chart
		
		google.charts.load("current", {packages:["corechart"]});
		google.charts.setOnLoadCallback(drawChart);
			
			
		<?php 
		
			$rq_review = $req_steps[0];
			$rq_approval = $req_steps[1];
			$rq_inspection = $req_steps[2];
		
		?>
						
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
				["Status", "Percentage", { role: "style" } ],
				['Review Step', <?php echo $rq_review;?>, "Purple"],
				['Approval Step', <?php echo $rq_approval;?>, "Red"],
				['Inspection Step', <?php echo $rq_inspection;?>, "Slateblue"]
			]);

		var view = new google.visualization.DataView(data);

		var options = {
			title: "Request Step Report",
			width: 500,
			height: 200,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
		};
		
		var chart4 = new google.visualization.BarChart(document.getElementById("barchart_values4"));
		chart4.draw(view, options);
  }
  </script>
<table>
	<tr>  
		<td><div id="barchart_values" style="width: 500px; height: 200px;"></div></td>
		<td><div id="barchart_values2" style="width: 500px; height: 200px;"></div></td>
	</tr>
	
	<tr>  
		<td><div id="barchart_values3" style="width: 500px; height: 200px;"></div></td>
		<td><div id="barchart_values4" style="width: 500px; height: 200px;"></div></td>
	</tr>
</table>
	  
	</div>
</div>