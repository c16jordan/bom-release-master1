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
			 //mysqli_close($db);
?>

<script src="jquery-3.4.1.js"></script>
  
  
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
			
			$total = $released + $in_prog + $cancelled;
			
			$released  = ($released / $total) * 100;
			$in_prog   = ($in_prog / $total) * 100;
			$cancelled = ($cancelled / $total) * 100;
		 ?>
						
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
				["Status", "Percentage", { role: "style" } ],
				['Released', <?php echo $released;?>, "opacity: 0.8; color: blue"],
				['In_Progress', <?php echo $in_prog;?>, "opacity: 0.8; color: green"],	  
				['Cancelled', <?php echo $cancelled;?>, "opacity: 0.8; color: grey"]
			]);

		var view = new google.visualization.DataView(data);
	
		var options = {
			title: "Application Report",
			width: 500,
			height: 175,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
			
			hAxis: {
				minValue: 0,
				title: 'Percent',
				ticks: [0, 25, 50, 75, 100]
			}
			
		};
		
		var chart1 = new google.visualization.BarChart(document.getElementById("barchart_values"));
		
		function selectHandler() {
			var selectedItem = chart1.getSelection()[0];
			
			if (selectedItem) {
				var value = data.getValue(selectedItem.row, 0);
				var table = $('#info').DataTable();
				
				resetFilters();
				
				table.column(7).search(value);
				table.draw();
			
				//resetFilters();
			}
	
		}
		
		// Listen for the 'select' event, and call my function selectHandler() when
		// the user selects something on the chart.
		google.visualization.events.addListener(chart1, 'select', selectHandler);
		
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
			
			$total = $c_released + $c_approved + $c_pending + $c_submitted + $c_in_review;
			
			$c_released   = ($c_released / $total) * 100;
			$c_approved   = ($c_approved / $total) * 100;
			$c_pending    = ($c_pending / $total) * 100;
			$c_submitted  = ($c_submitted / $total) * 100;
			$c_in_review  = ($c_in_review / $total) * 100;
	   ?>  
						
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
				["Status", "Percentage", { role: "style" } ],
			    ['Released', <?php echo $c_released;?>, "opacity: 0.8; color: blue"],
				['Approved', <?php echo $c_approved;?>, "opacity: 0.8; color: red"],
				['Pending', <?php echo $c_pending;?> , "opacity: 0.8; color: Orange"],
				['Submitted', <?php echo $c_submitted;?>, "opacity: 0.8; color: Green"],
				['In Review', <?php echo $c_in_review;?>, "opacity: 0.8; color: Purple"]
			]);

		var view = new google.visualization.DataView(data);
			
		var options = {
			title: "Component Report",
			width: 500,
			height: 175,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
			
			hAxis: {
				minValue: 0,
				title: 'Percent',
				ticks: [0, 25, 50, 75, 100]
			}
		};
		
		var chart2 = new google.visualization.BarChart(document.getElementById("barchart_values2"));
		
			function selectHandler() {
			var selectedItem = chart2.getSelection()[0];
			
			if (selectedItem) {
				var value = data.getValue(selectedItem.row, 0);
				var table4 = $('#info').DataTable();
				
				resetFilters();
				
				table4.column(8).search(value);
				table4.draw();
				
				//resetFilters();
			}
	
		}
		
		// Listen for the 'select' event, and call my function selectHandler() when
		// the user selects something on the chart.
		google.visualization.events.addListener(chart2, 'select', selectHandler);

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
			
			$total = $r_submitted + $r_approved + $r_pending;
				
			$r_submitted = ($r_submitted / $total) *100;
			$r_approved = ($r_approved / $total) * 100;
			$r_pending = ($r_pending / $total) * 100;
		?>
						
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
				["Status", "Percentage", { role: "style" } ],
			    ['Submitted', <?php echo $r_submitted;?>, "opacity: 0.8; color: Green"],
				['Approved', <?php echo $r_approved;?>, "opacity: 0.8; color: Red"],
				['Pending', <?php echo $r_pending;?>, "opacity: 0.8; color: Orange"]
			]);

		var view = new google.visualization.DataView(data);

		var options = {
			title: "Request Report",
			width: 500,
			height: 175,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
			
			hAxis: {
				minValue: 0,
				title: 'Percent',
				ticks: [0, 25, 50, 75, 100]
			}
		};
		
		var chart3 = new google.visualization.BarChart(document.getElementById("barchart_values3"));
		
			function selectHandler() {
			var selectedItem = chart3.getSelection()[0];
			
			if (selectedItem) {
				var value = data.getValue(selectedItem.row, 0);
				var table3 = $('#info').DataTable();
			
				resetFilters();
			
				table3.column(11).search(value);
				table3.draw();
				
				//resetFilters();

			}
	
		}

		// Listen for the 'select' event, and call my function selectHandler() when
		// the user selects something on the chart.
		google.visualization.events.addListener(chart3, 'select', selectHandler);
		
		
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
			
			$total = $rq_review + $rq_approval + $rq_inspection;
			$rq_review = ($rq_review / $total) * 100;
			$rq_approval = ($rq_approval / $total) * 100;
			$rq_inspection = ($rq_inspection / $total) * 100;
		
		?>
						
			function drawChart() {
				var data = google.visualization.arrayToDataTable([
				["Status", "Percentage", { role: "style" } ],
				['Review Step', <?php echo $rq_review;?>, "opacity: 0.8; color: Purple"],
				['Approval Step', <?php echo $rq_approval;?>, "opacity: 0.8; color: Red"],
				['Inspection Step', <?php echo $rq_inspection;?>, "opacity: 0.8; color: Slateblue"]
			]);

		var view = new google.visualization.DataView(data);

		var options = {
			title: "Request Step Report",
			width: 500,
			height: 175,
			bar: {groupWidth: "95%"},
			legend: { position: "none" },
		    
			hAxis: {
				minValue: 0,
				title: 'Percent',
				ticks: [0, 25, 50, 75, 100]
			}

		};
		
		var chart4 = new google.visualization.BarChart(document.getElementById("barchart_values4"));
		
		function selectHandler() {
			var selectedItem = chart4.getSelection()[0];
			
			if (selectedItem) {
				var value = data.getValue(selectedItem.row, 0);
				var table4 = $('#info').DataTable();
				
				resetFilters();
				
				table4.column(12).search(value);
				table4.draw();
				
				//resetFilters();
			}
	
		}

		// Listen for the 'select' event, and call my function selectHandler() when
		// the user selects something on the chart.
		google.visualization.events.addListener(chart4, 'select', selectHandler);
		
		chart4.draw(view, options);
  }
  </script>
    
  
 <script type="text/javascript" language="javascript">
    $(document).ready( function () {
        
        $('#info').DataTable( {
            dom: 'lfrtBip',
            buttons: [
                'copy', 'excel', 'csv', 'pdf'
            ],
			ajax: { 
				url: 'db_chart.php',
				dataSrc: ''
			}
			}
        );

        $('#info thead tr').clone(true).appendTo( '#info thead' );
        $('#info thead tr:eq(1) th').each( function (i) {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    
            $( 'input', this ).on( 'keyup change', function () {
                if ( table.column(i).search() !== this.value ) {
                    table
                        .column(i)
                        .search( this.value )
                        .draw();
                }
            } );
        } );
    
        var table = $('#info').DataTable( {
            orderCellsTop: true,
            fixedHeader: true,
            retrieve: true
        } );
        
    } );

</script>



<script>

	function resetFilters() {
		
		var table = $('#info').DataTable();
		
		for (i = 0; i < 15; i++) {
			table.column(i).search("");	
		}
	}

</script>

<script src="jquery-3.4.1.js"></script>

  
<table id="charts" width="100%">
	<tr>  
		<td width="50%"><div id="barchart_values" style="width: 500px; height: 200px;"></div></td>
		<td width="50%"><div id="barchart_values2" style="width: 500px; height: 200px;"></div></td>
	</tr>
	
	<tr>  
		<td width="50%"><div id="barchart_values3" style="width: 500px; height: 200px;"></div></td>
		<td width="50%"><div id="barchart_values4" style="width: 500px; height: 200px;"></div></td>
	</tr>
</table>
	  
	  
<div id="bar_table" style="margin-top: 40px" width="25%">

  <table id="info" cellpadding="0" cellspacing="0" border="0"
            class="datatable table table-striped table-bordered datatable-style table-hover"
            width="100%" style="width: 100px;">
              <thead>
                <tr id="table-first-row">
                        <th>App Id</th>
                        <th>App Name</th>
                        <th>App Version</th>
                        <th>Cmp Id</th>
                        <th>Cmp Name</th>
                        <th>Cmp Version</th>
                        <th>Cmp Type</th>
                        <th>App Status</th>
                        <th>Cmp Status</th>
						<th>Request Id</th>
                        <th>Request Date</th>
                        <th>Request Status</th>
                        <th>Request Step</th>
                        <th>Notes</th>
                </tr>
              </thead>

              <tbody>
			  
              </tbody>
			  
			   <tfoot>
                <tr>
                        <th>App Id</th>
                        <th>App Name</th>
                        <th>App Version</th>
                        <th>Cmp Id</th>
                        <th>Cmp Name</th>
                        <th>Cmp Version</th>
                        <th>Cmp Type</th>
                        <th>App Status</th>
                        <th>Cmp Status</th>
						<th>Request Id</th>
                        <th>Request Date</th>
                        <th>Request Status</th>
                        <th>Request Step</th>
                        <th>Notes</th>
                </tr>
              </tfoot>
        </table>


</div>








</div>

</div>
</div>

 <style>
   tfoot {
     display: table-header-group;
   }
   
 </style>

<?php include("./footer.php"); ?>