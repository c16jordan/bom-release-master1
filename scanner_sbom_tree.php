<?php
  $nav_selected = "SCANNER";
  $left_buttons = "YES";
  $left_selected = "SBOMTREE";

  include("./nav.php");   
 ?>
 
    <link rel="stylesheet" href="css/screen.css" media="screen" />
    <link rel="stylesheet" href="css/jquery.treetable.css" />
    <link rel="stylesheet" href="css/jquery.treetable.theme.default.css" />
	<script src="jquery-3.4.1.js"></script>
 
		
		<div class="right-content">
			<div class="container">
	
				<h3 style = "color: #01B0F1;">Scanner --> BOM Tree</h3>
				
				
				

	<?php 
	
	
	$sql = "SELECT app_name, app_version, app_status, 
				   cmp_name, cmp_version, cmp_type, cmp_status,
				   request_id, request_date, request_status, request_step,
				   notes 
			FROM sbom";
			
	$result = $db->query($sql);
	
	
	$bom_ary;	// Associative array to store Application info and that of its components
	$key;
    

		if ($result->num_rows > 0) {
                   
			while($row = $result->fetch_assoc()) {
				
			// Store relevant components by Application (name+id)
			$key = $row["app_name"]." ".$row["app_version"];
			$value = $row["app_status"]
				."@".$row["cmp_name"]." ".$row["cmp_version"]."@".$row["cmp_type"]."@".$row["cmp_status"]
				."@".$row["request_id"]."@".$row["request_date"]."@".$row["request_status"]."@".$row["request_step"]
				."@".$row["notes"];
								 
				$bom_ary[$key][] = explode("@", $value);
            }
         }
         else {
            echo "0 results";
         }
		 
     $result->close();
     ?>

	 
 <!-- Fill table rows -->

 <table id="sbom_tree">
	<caption>
		<button id="expand" style="font-size: 10px">Expand</button>
		<button id="collapse" style="font-size: 10px">Collapse</button>
	</caption>
	
	<thead>
	<tr>
	<?php 
		// Set up the columns by name
		
		echo "<th>Application</th>";
		echo "<th>Application Status</th>";
		echo "<th>Component Type</th>";
		echo "<th>Component Status</th>";
		echo "<th>Request Id</th>";
		echo "<th>Request Date</th>";
		echo "<th>Request Status</th>";
		echo "<th>Request Step</th>";
		echo "<th>Notes</th>";
	?>
	</tr>
	</thead>
	
	<tbody>
	<?php
		// Set up the root nodes
		$count=0;
		$parent_id=1;
		
		ksort($bom_ary);
		
		foreach($bom_ary as $key=>$value){
			echo '<tr data-tt-id="'.$parent_id.'">';
			echo '<td>'.$key.'</td>';
			
			
		// Set up the child nodes
		
		$child_index=0;
		$add_status = 1;
		
		foreach($value as $key=>$child){
			
			// Gets individual child record
			$child_data = $value[$child_index];
			
			
			for($index=0; $index < count($child_data); $index++){
				
				if($index == 0){
				
					// Complete root node
					if($add_status === 1){
						echo '<td>'.$child_data[0].'</td>';
						echo '</tr>';	// Closing tr tag for root note data
						$add_status = 0;
											}
					else{
						echo '</tr>';
					}
					
					continue;

				}
				
				
				// Begin component node row
				if($index == 1){
					echo '<tr data-tt-id="'.$parent_id.".".($child_index+1).'" tr data-tt-parent-id="'.$parent_id.'">';
					echo '<td>'.$child_data[$index].'</td>';
					echo '<td></td>';
					continue;
				}
				
				// Fill in component data
				echo '<td>'.$child_data[$index].'</td>';
			}
			
			// Close component node row
			echo '</tr>';
			$child_index++;
		}
		
		
		
		$parent_id++;
		}
	?>	
	</tbody>
	
</table>	
		
		


		<script src="jquery.treetable.js"></script>
		
		
		<script>
		$(document).ready(function(){
				$("#expand").click(function(){
					$('#sbom_tree').treetable('expandAll');
					//alert("Expand");
				});
		});
		
		$(document).ready(function(){
				$("#collapse").click(function(){
					$('#sbom_tree').treetable('collapseAll');
					//alert("Collapse");
				});
		});
		</script>
		
		
		<script>
			$("#sbom_tree").treetable({ expandable: true });
		</script>	

			
	</div>
</div>


<?php //include("./footer.php"); ?>
