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
	
		<!-- Collect database information to load tables-->
	<?php 
	
	
	// Collect database column names
	
	$sql = "SELECT DISTINCT COLUMN_NAME 
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_NAME = 'sbom'";
	
	$result_col = $db->query($sql);
	$columns = [];
	
	if ($result_col->num_rows > 0) {
		while($row = $result_col->fetch_assoc()) {
			$columns[] = $row['COLUMN_NAME'];
		}
	}
	
	
	// Collect database node information
	
	$sql = "SELECT * from sbom ORDER BY 'app_name';";
	$result = $db->query($sql);
	$bom_ary;	// To be an associative array
	$iterator = []; // Inner numerically indexed array for autoindexing
	$key;
                if ($result->num_rows > 0) {
                   
                    while($row = $result->fetch_assoc()) {
						
						$key = $row["app_name"]." ".$row["app_version"];
						$value = $row["cmp_id"].",".$row["cmp_name"].",".$row["cmp_version"].",".$row["cmp_type"].","
								 .$row["app_status"].",".$row["cmp_status"].",".$row["notes"];
								 
						$bom_ary[$key][] = explode(",", $value);
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
		echo "<th>Software</th>";
		// Set up the columns by name
		foreach($columns as $key=>$value){
			if($key < 4){
				continue;
			}
				echo "<th>".$value."</th>";
		}
	?>
	</tr>
	</thead>
	
	<tbody>
	
	<?php	
		//Place the data in the table tree per parent-child relationships 
		$parent = 1;

		foreach($bom_ary as $key=>$value){
			
			// Parent node - Software(Name + Version)
			echo '<tr data-tt-id="'.$parent.'">
					<td>'.$key.'</td> 
				  </tr>';
			
			
			//Array of child nodes
			$rows = $value;
			
			// Access array storing children node data arrays
			for($child_index = 0, $out_ary_length = count($rows); $child_index < $out_ary_length;$child_index++){
				
				echo '<tr data-tt-id="'.$parent.'.'.($child_index+1).'" data-tt-parent-id="'.$parent.'">';
				echo '<td></td>';
				$child_node = $rows[$child_index];
				
				//Access child node data
				for($child = 0, $in_ary_length = count($rows[0]); $child < $in_ary_length; $child++){
					
					echo '<td>'.$child_node[$child].'</td>';

				}
				
				echo '</tr>';	
			}
			$parent++;
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



<?php include("./footer.php"); ?>
