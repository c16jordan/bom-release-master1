<?php
  $nav_selected = "SCANNER";
  $left_buttons = "YES";
  $left_selected = "SBOMTREE";

  include("./nav.php");
  
 ?>
 
 <script>
 
/*
if (typeof jQuery !== 'undefined') {  
    // jQuery is loaded => print the version
    alert(jQuery.fn.jquery);
}
*/

 </script>
 	<link rel="stylesheet" href="css/screen.css" media="screen" />
	<link rel="stylesheet" href="css/jquery.treetable.css" />
	<link rel="stylesheet" href="css/jquery.treetable.theme.default.css" />
	<script src="jquery-3.4.1.js"></script>
		
		<div class="right-content">
			<div class="container">
	
				<h3 style = "color: #01B0F1;">Scanner --> BOM Tree</h3>
				
				
				
	<div id="trees">
	
	<button id="expando" style="font-size: 10px">Expand</button>
	<button id="reducto" style="font-size: 10px">Collapse</button>
	
	<script>
		$(document).ready(function(){
				$("#expando").click(function(){
					$("#sbom_tree").treetable('expandAll');
				});
		});
		
		$(document).ready(function(){
				$("#reducto").click(function(){
					$("#sbom_tree").treetable('collapseAll');
				});
		});
	</script>
	
	<?php 
	
	
	$sql = "SELECT app_name, app_version, app_status, cmp_name, cmp_version, cmp_type, cmp_status, notes 
			FROM sbom";
	$result = $db->query($sql);
	$bom_ary;	// Array to store Application info and that of its components
	$key;
                if ($result->num_rows > 0) {
                   
                    while($row = $result->fetch_assoc()) {
						
						$key = $row["app_name"]." ".$row["app_version"];
						$value = $row["app_status"]."-".$row["cmp_name"]." ".$row["cmp_version"]
					        ."-".$row["cmp_type"]."-".$row["cmp_status"]."-".$row["notes"];
								 
						$bom_ary[$key][] = explode("-", $value);
                    }
                }
                else {
                    echo "0 results";
                }
     $result->close();
     ?>

 
 <!-- Fill table rows -->
 <div>
 
 <table id="sbom_tree">
	<thead>
	<tr>
	<?php 
		// Set up the columns by name
		
		echo "<th>Application</th>";
		echo "<th>Application Status</th>";
		echo "<th>Component Type</th>";
		echo "<th>Component Status</th>";
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
				
				
				if($index == 1){
					echo '<tr data-tt-id="'.$parent_id.".".($child_index+1).'" tr data-tt-parent-id="'.$parent_id.'">';
					echo '<td>'.$child_data[$index].'</td>';
					echo '<td></td>';
					continue;
				}
				
				//WORKS
				echo '<td>'.$child_data[$index].'</td>';
			}
			
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
			$("#sbom_tree").treetable({ expandable: true });
		</script>	
			
		</div>
				

	
		
 </div>
			

			
	</div>
</div>
<p id="test">
<?php
?>
</p>


<?php include("./footer.php"); ?>
