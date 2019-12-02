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
			$apps = [];
			$cmps = [];
			$app_stats1 = [];
			$cmp_stats1 = [];
			$req_stats1 = [];
			$req_steps1 = [];
			
			// Get status names from db column themselves
			$mysql = "SELECT GROUP_CONCAT(DISTINCT CONCAT(app_name, ' ', app_version, '@' , app_status)) FROM sbom;";
			$mysql .= "SELECT GROUP_CONCAT(DISTINCT CONCAT(cmp_name, ' ', cmp_version, '@' , cmp_status)) FROM sbom;";
			$mysql .= "SELECT GROUP_CONCAT(DISTINCT app_status) FROM sbom;";
			$mysql .= "SELECT GROUP_CONCAT(DISTINCT cmp_status) FROM sbom;";
			$mysql .= "SELECT GROUP_CONCAT(DISTINCT request_status) FROM sbom;";
			$mysql .= "SELECT GROUP_CONCAT(DISTINCT request_step) FROM sbom;";

			$meta_d = [];
			$result = $db->query($mysql);
			
			 if (mysqli_multi_query($db,$mysql)) {

			    do{
					if($result=mysqli_store_result($db)){
						while($row=mysqli_fetch_row($result)){
							//echo "ROW: ".$row[0]."<br/>";
							$meta_d[] = $row[0];
						}
						
					 mysqli_free_result($result);
					}
					
				}while(mysqli_next_result($db));
				
             }
			
	
			
			for($iter=0; $iter < 6; $iter++){
				
				$temp_ary = explode(",", $meta_d[$iter]);
				
				//echo "<pre>";
				//print_r($temp_ary);
				//echo "</pre>";
				
				for($in_iter=0; $in_iter < count($temp_ary); $in_iter++){
					
					switch($iter){
						case 0:
							$apps[$temp_ary[$in_iter]]=0;
							break;
						
						case 1:
							$cmps[$temp_ary[$in_iter]]=0;
							break;
						
						case 2:
							$app_stats1[$temp_ary[$in_iter]]=0;
							break;
						
						case 3:
							$cmp_stats1[$temp_ary[$in_iter]]=0;
							break;
						
						case 4:
							$req_stats1[$temp_ary[$in_iter]]=0;
							break;
						
						case 5:
							$req_steps1[$temp_ary[$in_iter]]=0;
							break;  
							
					}
				}
			
			}
		

		
	
			// Number of elements in each array
			$app_cnt = count($app_stats1);
			$cmp_cnt = count($cmp_stats1);
			$reqs_cnt = count($req_stats1);
			$reqst_cnt = count($req_steps1);
			
			$app_stats = [];	
			$cmp_stats = [];
			$req_stats = [];
			$req_steps = [];
			
			$app_cats = [];
			$cmp_cats = [];
			$req_cats = [];
			$reqst_cats = [];
			
			$sql = "";

			
			foreach($req_stats1 as $key=>$value){
				$sql .= "SELECT COUNT(request_status) FROM sbom WHERE request_status='".$key."';";
			}
			
			foreach($req_steps1 as $key=>$value){
				$sql .= "SELECT COUNT(request_step) FROM sbom WHERE request_step='".$key."';";
			}
			
			$result = $db->query($sql);
			
			// Keep track of which results are being stored
			$tracker = 0;
	
			 if (mysqli_multi_query($db,$sql)) {
               
			    do{
					if($result=mysqli_store_result($db)){
						while($row=mysqli_fetch_row($result)){
							
							if($tracker < $reqs_cnt){
								$req_stats[] = $row[0];
							}
							else if($tracker < $reqs_cnt + $reqst_cnt){
								$req_steps[] = $row[0];
							}
							
							$tracker++;
						}
						
					 mysqli_free_result($result);
					}
				}while(mysqli_next_result($db));
				
             }
			 
			$iterator=0;
			foreach($req_steps1 as $key=>$value){
				$req_steps1[$key] = $req_steps[$iterator];
				$iterator++;
			}
			
			$iterator=0;
			foreach($req_stats1 as $key=>$value){
				$req_stats1[$key] = $req_steps[$iterator];
				$iterator++;
			}
			 		

			//Store type and quantity of app statuses
			 foreach($apps as $key=>$value){
				
				$delim_pos= stripos($key,"@")+1;
				$str_len = strlen($key);
				$category = substr($key, $delim_pos, $str_len);
				
				if(array_key_exists($category, $app_cats)){
					$app_cats[$category] = $app_cats[$category] + 1;
				}else{
					$app_cats[$category] = 1;
				}

			}
			
			//Store type and quantity of cmp statuses
			foreach($cmps as $key=>$value){
				
				$delim_pos= stripos($key,"@")+1;
				$str_len = strlen($key);
				$category = substr($key, $delim_pos, $str_len);
				
				if(array_key_exists($category, $cmp_cats)){
					$cmp_cats[$category] = $cmp_cats[$category] + 1;
				}else{
					$cmp_cats[$category] = 1;
				}

			}
			 
			//Store type of request status statuses
			foreach($req_stats1 as $key=>$value){
				
				$delim_pos= stripos($key,"@");
				$str_len = strlen($key);
				$category = substr($key, $delim_pos, $str_len);
				
				if(array_key_exists($category, $req_cats)){
					$req_cats[$category] = $req_cats[$category] + 1;
				}else{
					$req_cats[$category] = 1;
				}

			}

			//Store type of request_step statuses
			foreach($req_steps1 as $key=>$value){
				
				$delim_pos= stripos($key,"@");
				$str_len = strlen($key);
				$category = substr($key, $delim_pos, $str_len);
				
				if(array_key_exists($category, $reqst_cats)){
					$reqst_cats[$category] = $reqst_cats[$category] + 1;
				}else{
					$reqst_cats[$category] = 1;
				}

			}			

			$iterator = 0;
			foreach($reqst_cats as $key=>$value){
				$reqst_cats[$key] = $req_steps[$iterator++]; 
			}
			
			$iterator = 0;
			foreach($req_cats as $key=>$value){
				$req_cats[$key] = $req_stats[$iterator++]; 
			}
			 
			 /*
			 echo "<pre>";
			 print_r($app_cats);
			 echo "</pre>";
			 
			 
			 echo "<pre>";
			 print_r($cmp_cats);
			 echo "</pre>";
			 
			  echo "<pre>";
			 print_r($req_cats);
			 echo "</pre>";
			 
			 echo "<pre>";
			 print_r($reqst_cats);
			 echo "</pre>";
			 */
			 
			 
			 
				foreach($app_cats as $key=>$value){
				//echo "['".$key."',".$value."],";
			}
						
			mysqli_close($db);
			
			$total = count($apps) + count($cmps);
			
			//echo $total;
			//echo "<pre>";
			//print_r($apps);
			//echo "</pre>";
			 
			
?>
  
  
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	

	<script type="text/javascript">
      
	  google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Percent'],
		  
		  <?php
			foreach($app_cats as $key=>$value){
				echo "['".$key."',".$value."],";
			}
		  ?>
			
		]);

        var options = {
          title: 'Application Report'
        };

        var chart1 = new google.visualization.PieChart(document.getElementById('piechart1'));
		
		
			function selectHandler() {
			var selectedItem = chart1.getSelection()[0];
			
			if (selectedItem) {
				var value = data.getValue(selectedItem.row, 0);
				var table = $('#info').DataTable();
				
				resetFilters();
				
				table.column(7).search(value);
				table.draw();
			
				resetFilters();
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
	   
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Percent'],
			
		  <?php
			foreach($cmp_cats as $key=>$value){
				echo "['".$key."',".$value."],";
			}
		  ?>
		  
        ]);

        var options = {
          title: 'Component Report'
        };

        var chart2 = new google.visualization.PieChart(document.getElementById('piechart2'));

		function selectHandler() {
			var selectedItem = chart2.getSelection()[0];
			
			if (selectedItem) {
				var value = data.getValue(selectedItem.row, 0);
				var table = $('#info').DataTable();
				
				resetFilters();
				
				table.column(8).search(value);
				table.draw();
				
				resetFilters();
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
				var table = $('#info').DataTable();
			
				resetFilters();
			
				table.column(11).search(value);
				table.draw();
				
				resetFilters();
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
				var table = $('#info').DataTable();
				
				resetFilters();
				
				table.column(12).search(value);
				table.draw();
				
				resetFilters();
			}
	
		}

		// Listen for the 'select' event, and call my function selectHandler() when
		// the user selects something on the chart.
		google.visualization.events.addListener(chart4, 'select', selectHandler);

        chart4.draw(data, options);
      }
    </script>

	
  </head>
  
  <body>
    
	<table class="pies" width="100%">
	
	<tr>
		<td width="50%" id="piechart1"></td>
		<td width="50%" id="piechart2"></td>
	</tr>
	
	<tr>
		<td width="50%" id="piechart3"></td>
		<td width="50%" id="piechart4"></td>
	</tr>
	
	</table>

	<div id="slice_table">
	
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

	
  </body>

 


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

 

 <style>
   tfoot {
     display: table-header-group;
   }
 </style>
 
  
 </html>

	</div>
</div>

  <?php include("./footer.php"); ?>