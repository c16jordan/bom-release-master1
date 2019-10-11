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
						
						//$key = $row["app_name"]." ".$row["app_version"];
						$key = $row["app_id"];
						$value = $row["app_name"].",".$row["app_version"].",".$row["cmp_id"].",".$row["cmp_name"].",".$row["cmp_version"].",".$row["cmp_type"].","
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
		
		// Set up the columns by name
		foreach($columns as $key=>$value){
			if($key < 1 || $key ==2 || $key == 3){
				continue;
			}
			
			if($key == 1){
				echo "<th>BOM</th>";
				continue;
			}
			
			echo "<th>".$value."</th>";
		}
	?>
	</tr>
	</thead>
	
	<tbody>
	
	<?php	
	
$check = "1st group of children: <br />";
$check1 = "2nd group of children <br />";
	//Place the data in the table tree per parent-child relationships 
		$parent = 1;
		$temp_parent;
		
		ksort($bom_ary);
		
		foreach($bom_ary as $key=>$value){
			
			// Parent node - BOM id
			echo '<tr data-tt-id="'.$parent.'">
					<td>'.$key.'</td> 
				  </tr>';

			
			//Array of array of child nodes
			$rows = $value;
			
			// Access array storing children node data arrays
			for($child_index = 1, $ary_length = count($rows); $child_index < $ary_length;$child_index++){

			//Print immediate bom children
				$child_data = $rows[$child_index];
				
				echo '<tr data-tt-id="'.$parent.'.'.$child_index.'" data-tt-parent-id="'.$parent.'">';
				
				if($child_index == 1){
					
					$check .= "data-tt-id=".$parent.".".$child_index." ".$child_data[0].".".$child_data[1]." "
					."data-tt-parent-id=".$parent."<br />";
					
					echo '<td>'.$child_data[0]." ".$child_data[1].'</td></tr>';
					$temp_parent = $parent.".".$child_index;
				}
				
				
				for($index=2; $index < $ary_length; $index++){
						//echo '<tr data-tt-parent-id="'.$temp_parent.'></tr>'; 
							
						 
				}
				
				
			}
				/*
				//reset child index
				
				
				for($index = 2, $sub_node=1; $index < count($child_data) ;$sub_node++, $index++){
					
					if($index == 2){
						$check1 .=  'data-tt-id='.$parent.'.'.$child_index.'.'.$sub_node.' data-tt-parent-id='.$parent.'.'.$child_index."<br />".
						'data---'.$child_data[$index].' ---data <br /><br />';
						continue;
					}
					
					echo '<td>'.$child_data[$index].'</td>';
					
				}
			}
				//echo '</tr>';
//Print immediate bom children
				
				//echo '<td>'.$child_data[$child_index].'</td>';
				
				//$child_node = $rows[$child_index];
				
				
				//Access child node data
				for($child = 0, $in_ary_length = count($rows[0]); $child < $in_ary_length; $child++){
					
					echo '<td>'.$child_node[$child].'</td>';

				}
				
				
			}
	
	
*/			$parent++;
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


<p>
<?php
/*
echo "<pre>";
echo $check;
echo "</pre>";

echo "<pre>";
echo $check1;
echo "</pre>";
*/

echo "<pre>";
print_r($rows);
echo "</pre>";

?>
</p>
<?php include("./footer.php"); ?>
