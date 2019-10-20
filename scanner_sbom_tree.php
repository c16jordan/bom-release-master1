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
				
	<script>
		var flag = 0;
		var rootOrigColor;
		var childOrigColor;
		var leafOrigColor;
	</script>	

	<?php 
	
	
	$sql = "SELECT app_name, app_version, app_status, 
				   cmp_name, cmp_version, cmp_type, cmp_status,
				   request_id, request_date, request_status, request_step,
				   notes 
			FROM sbom";
			
	$result = $db->query($sql);
	
	
	$bom_ary;	// Not so nice 3-dimensional array that stores BOM table data. But, because no searching is involved you just enter the associative keys to access the data.
	$base_key;	// Stores base node data
	$root_key;	// Stores root node data
    $autocomplete = []; // Stores all app and cmp names 
	$autocomplete_num = []; // Numerically indexed keys from $autocomplete
 		
		if ($result->num_rows > 0) {
                   
			while($row = $result->fetch_assoc()) {
			
			// Store data for autocomplete
			$autocomplete[$row["app_name"]] = 1;
			$autocomplete[$row["cmp_name"]] = 1;
			
			// Store relevant components by Application (name+id)
			
			$base_key = $row["app_name"];
			$root_key = $row["app_name"]." ".$row["app_version"]."@".$row["app_status"];
			$child_key = $row["cmp_name"]." ".$row["cmp_version"];
			
			//$row["app_status"]
			$value = $row["cmp_type"]."@".$row["cmp_status"]
				."@".$row["request_id"]."@".$row["request_date"]."@".$row["request_status"]."@".$row["request_step"]
				."@".$row["notes"];
								 
				$bom_ary[$base_key][$root_key][$child_key][] = explode("@", $value);
            }
         }
         else {
            echo "0 results";
         }
		 
	 // Convert $autocomplete associative array into a numerically index array for easier access
	
	 foreach($autocomplete as $name=>$value){
		 $autocomplete_num[] = $name;
	 }
	 sort($autocomplete_num);
	 
     $result->close();
     ?>
<!-- https://www.w3schools.com/howto/howto_js_autocomplete.asp -->
<style>

#where{
	margin-right: 5px;
}


.autocomplete{
	width:150px; 
	display:inline-block;
	position:absolute;
}

.autocomplete-list{
	width:200px;
	position: absolute;
	top:100%;
	left:0;
	right:0;
	z-index:99;
	background-color: #fff;
}

.autocomplete-items{
	border-top: none;
	border-left: 2px solid #f9f9f9;
	border-bottom: none;
	background-color: #f9f9f9;


	
}

.root{
	background-color: #f9f9f9;
}

.root_colored{
	background-color: #e60000;
}


.child{
	background-color: #f9f9f9;
}


.child_colored{
	background-color: #ffff4d;
}

.leaf{
	background-color: white;
}

.leaf_colored{
	background-color: #009900;
}

.highlight_node{
	background-color: #1E90FF;
}

</style>
 
 <!-- Fill table rows -->

 <table id="sbom_tree">
 
	
	<div>
	
	<caption>
		<button id="expand" style="font-size: 10px">Expand All</button>
		<button id="collapse" style="font-size: 10px">Collapse All</button>
		<button id="colorize" style="font-size: 10px"> Toggle Color </button>
	
	<span id="where">Where used: </span>
		<div class="autocomplete">
			
			<input id="where_used" type="text" placeholder="name;version id"></input>
		<!--	<div id="autocomplete-list" class="autocomplete-items"><input></input></div> -->

		</div>
	
	</caption>
	
	
	
	</div>
	
	
	
	
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

		$parent_id=1;
		$root_id = 1;
		$child_id = 1;
		
		ksort($bom_ary);
		
		// Set up base - App names only
		foreach($bom_ary as $base=>$root_ary){
			
			base($base, $parent_id);
			
			// Set up root - App names + Versions only
			foreach($root_ary as $root=>$cmp_array){
				root($root, $parent_id, $root_id);
				
				$child_parent = $parent_id.'.'.$root_id;
				
				// Set up component - Cmp Name + Versions only
				foreach($cmp_array as $child=>$cmp_values){
				
					child($child, $cmp_values, $child_parent ,$child_id);	
					$child_id++;
				}
				
				$child_id = 1;
				$root_id++;
			}
			$root_id = 1;
			$parent_id++;
		}
		
		//<tr data-tt-id="x">
		function base($base, $parent_id){
			echo '<tr class="root '.$base.'" data-tt-id="'.$parent_id.'">';
			echo '<td class="root">'.$base.'</td>';
			
			for($index=0; $index < 8; $index++){
					echo '<td class="root"></td>';
			}
			
			echo "</tr>";
		}

		//<tr data-tt-id="x.x">
		function root($root, $parent_id, $root_id){
		    $root = explode("@",$root);
			echo '<tr class="child '.$root[0].'" data-tt-id="'.$parent_id.'.'.$root_id.'" data-tt-parent-id="'.$parent_id.'">';
				
				echo '<td class="child">'.$root[0].'</td>';
				echo '<td class="child">'.$root[1].'</td>';
				
				for($index=0; $index < 7; $index++){
					echo '<td class="child"></td>';
				}
			echo '</tr>';
		}

		//<tr data-tt-id="x.x.x">
		function child($child, $child_ary, $parent_id, $child_id){
			echo '<tr class="leaf '.$child.'" data-tt-id="'.$parent_id.'.'.$child_id.'" data-tt-parent-id="'.$parent_id.'">';
				echo '<td class="leaf">'.$child.'</td>';	
				
				foreach($child_ary as $leaf=>$data)
					leaf($data);
					
			echo '</tr>';
		}

		// Prints out leaf node data under children of child() function
		function leaf($leaf_ary){
			echo '<td class="leaf"></td>';
			
			foreach($leaf_ary as $key=>$value){
				echo '<td class="leaf">'.$value.'</td>';
			}
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
	
		$(document).ready(function(){
				$("#where_used").keydown(function(event){
					$key_pressed = event.which;
					if($key_pressed === 13){
						selectElement(document.getElementById("where_used").value);
					}
				});
		});
		
		$(document).ready(function(){
				$("#colorize").click(function(){			
									
				var root_nodes = document.getElementsByClassName("root");
				var child_nodes = document.getElementsByClassName("child");
				var leaf_nodes = document.getElementsByClassName("leaf");
									
					if(flag == 0){	
							
						color(root_nodes, "root_colored");
						color(child_nodes, "child_colored");
						color(leaf_nodes, "leaf_colored");
						
						flag = 1;
					}
					else if(flag == 1){
						
						removeColor(root_nodes, "root_colored");
						removeColor(child_nodes, "child_colored");
						removeColor(leaf_nodes, "leaf_colored");
							
						flag = 0;
					}
					
					
				});
		});
		
		</script>
		
		
		<script>
	var result_container;
/*
	$(document).ready(function(){
			$('#where_used').keydown(function(){
	
				
			});
	});
*/
	
</script>
		
		
		<script>
					
			function removeColor(node, class_name){				
				
				for(var i = 0; i < node.length ;i++){
					node[i].classList.remove(class_name);
				}
				
			}
			
					
			function color(node, class_name){
		
				for(var i = 0; i < node.length ;i++){
					node[i].classList.add(class_name);
				}
				
			}
			
			//function selectElement(object){
			function selectElement(target){
				
				var results;
				var exist_highlight;
				var children;
				
				results	= document.getElementsByClassName(target);
				children = results[0];
				
				alert("COLORING");
				
				color(children, "highlight_node");
				/*
				for(var parent_index = 0; parent_index < results.length ; parent_index++){

					children = results[parent_index].children;
					var node = (results[parent_index].getAttribute("data-tt-id"));
				
					for(var child_index = 0; child_index < children.length ;child_index++){
						children[child_index].style.backgroundColor = "#1E90FF";

					}
					
					$("#sbom_tree").treetable("reveal", node);
				}
					*/		
			}
						
		</script>
		
		
		<script>
			$("#sbom_tree").treetable({ expandable: true });
		</script>	

			
	</div>
</div>

<p>
<?php
/*
echo "<pre>";
print_r($autocomplete_num);
echo "</pre>"; 
*/
?>
</p>
<?php //include("./footer.php"); ?>
