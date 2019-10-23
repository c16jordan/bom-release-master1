<?php
  $nav_selected = "SCANNER";
  $left_buttons = "YES";
  $left_selected = "SBOMTREE";

  include("./nav.php");   
  global $db;
 ?>

 
 <link rel="stylesheet" href="css/screen.css" media="screen" />
 <link rel="stylesheet" href="css/jquery.treetable.css" />
 <link rel="stylesheet" href="css/jquery.treetable.theme.default.css" />
 <link rel="stylesheet" type="text/css" href="mycss.css">
 <script src="jquery-3.4.1.js"></script>
 
<div class="right-content">
	<div class="container">
	
		<h3 style = "color: #01B0F1;">Scanner --> BOM Tree</h3>
 
<!-- https://www.w3schools.com/howto/howto_js_autocomplete.asp -->
 
 <table id="sbom_tree">
 
	<div>
	
		<caption>
			<button id="expand" style="font-size: 10px">Expand All</button>
			<button id="collapse" style="font-size: 10px">Collapse All</button>
			<button id="colorize" style="font-size: 10px"> Toggle Color </button>
			<button id="reds" style="font-size: 10px">Reds</button>
			<button id="red_yellow" style="font-size: 10px"> Reds and Yellows </button>
			<button id="where_button" style="font-size: 10px; margin-left:25px">Where used</button>
	
			<!--Probably abandon this autocomplete drop down list
				<div class="autocomplete">
				<div id="autocomplete-list" class="autocomplete-items"><input></input></div> 
				</div>
			-->
		
			<input id="where_used" type="text" placeholder="name;[version id] option"></input>
			<span id="error"></span>
		
		</caption>
		
	</div>
	
	
	<thead>
	
	<tr>
		<?php setupTheaders(); ?>
	</tr>
	
	</thead>
	
	<tbody id="treeSpace">
	
	</tbody>
	<p>
		<?php //phpMakeTree($db); ?>
	</p>
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
				$("#where_button").click(function(){
					
					var value = document.getElementById("where_used").value;
				
						value = formatInput(value);
						selectElement(value);
					
					
				});
		});
	
		$(document).ready(function(){
				$("#where_used").keydown(function(event){
					$key_pressed = event.which;
					if($key_pressed == 13){
						var value = document.getElementById("where_used").value;
						
						value = formatInput(value);
						selectElement(value);
						
					}
				});
		});
		
		var color_flag = 0;
		var rootOrigColor;
		var childOrigColor;
		var leafOrigColor;
		
		
		var highlighted;
		var reds = false; // Flag to verify reds class active
		var reds_yellows = false;	// Flag to verify reds_yellow class active
		var colors = false;
		
		var reds_toggle = 0;
		
		$(document).ready(function(){
				$("#reds").click(function(){
		
					if(!reds){
						destroyTree();
						<?php phpMakeTree($db); ?>
						$('#sbom_tree').treetable("expandAll");
						$('#sbom_tree').treetable("collapseAll");
						
						color_flag = 0;
						reds = true;
						reds_yellows = false;
					}
					
				});
		});
		
		$(document).ready(function(){
			<?php phpMakeTree($db); ?>
			$('#sbom_tree').treetable("expandAll");
			$('#sbom_tree').treetable("collapseAll");
		});
		
		
		var reds_yellows_toggle = 0;
/*					HEREERERERERE    	*/		
		$(document).ready(function(){
				$("#red_yellow").click(function(){
										
					if(!reds_yellows){
						
						destroyTree();
						<?php phpMakeTree($db, true); ?>
						$('#sbom_tree').treetable("expandAll");
						$('#sbom_tree').treetable("collapseAll");
						
						color_flag = 0;
						reds_yellows = true;
						reds = false;
					}			
					
				});
		});
		
		$(document).ready(function(){
				$("#colorize").click(function(){			
											
				var root_nodes = document.getElementsByClassName("root");
				var child_nodes = document.getElementsByClassName("child");
				var leaf_nodes = document.getElementsByClassName("leaf");
									
					if(color_flag == 0){	
							
						color(root_nodes, "root_colored");
						color(child_nodes, "child_colored");
						color(leaf_nodes, "leaf_colored");
							
						colors = true;
						color_flag = 1;
					}
					else if(color_flag == 1){
						
						removeColor(root_nodes, "root_colored");
						removeColor(child_nodes, "child_colored");
						removeColor(leaf_nodes, "leaf_colored");
							
						colors = false;
						color_flag = 0;
					}
					
					
				});
		});
		
		</script>
		
		
	
		<script>
	
			
			// Did not use - just collapse the whole tree when highlighting new elements 
			//var node_ids = []; 
			
			function makeTree(){
				<?php //phpMakeTree($db); ?>
			}
			
			function destroyTree(){
				var root_ary = document.getElementsByClassName("root branch");
				var length = root_ary.length;
				//alert(length);
				
				for(var index = 1; index<=length ;index++){
					$('#sbom_tree').treetable("removeNode", index);
				}
			}
			
			function removeColor(node_list, class_name){				
				
				
				var length = node_list.length;
				node_list = Array.from(node_list);
				
					if(node_list.length > 0){						
						
						for(var exist_index = 0; exist_index < node_list.length; exist_index++){				
							
							//https://stackoverflow.com/questions/15843581/how-to-correctly-iterate-through-getelementsbyclassname
							node = node_list[exist_index]; // This is how to access nodelist / document.getElementsByClassName nodelists without skips
							node.classList.remove(class_name);
						}						
						
				}
				
			}
			
					
			function color(node_list, class_name){
						
				var length = node_list.length;
				node_list = Array.from(node_list);
				
				if(node_list.length > 0){						
					
					for(var exist_index = 0; exist_index < node_list.length; exist_index++){				
							
						//https://stackoverflow.com/questions/15843581/how-to-correctly-iterate-through-getelementsbyclassname
						node = node_list[exist_index]; // This is how to access nodelist / document.getElementsByClassName nodelists without skips
						node.classList.add(class_name);
					}						
						
				}
	
			}
			
			function selectElement(target){

				var results;
				var children;
				
				highlighted = document.getElementsByClassName("highlight_node");
				
				removeHighlighted(highlighted);
								
				results	= document.getElementsByClassName(target);
				
				
				for(var result_index = 0; result_index < results.length; result_index++){
					
					var node = (results[result_index].getAttribute("data-tt-id"));

					$("#sbom_tree").treetable("reveal", node);
					
					children = results[result_index].children;
					color(children, "highlight_node");
				
				}
				
			}
					


			function removeHighlighted(node_list){
					
					var length = node_list.length;				

					if(node_list.length > 0){						
						
						for(var exist_index = 0; exist_index < node_list.length; exist_index++){				
							
							removeColor(node_list,"highlight_node");

						}
						
						// Why look for an individual node? Collapse the whole tree after removing highlighting
						$("#sbom_tree").treetable("collapseAll");
						
					}
					
			}
			
			function formatInput(string){
			
				if(string.indexOf(';') !== -1){
					string = string.replace(';', ' ');
				}
				else if(string.indexOf(',') !== -1){
					string = string.replace(',', ' ');
				}
				
				return string;
			}
			
		</script>
		
		
		<script>
			$("#sbom_tree").treetable({ expandable: true });
		</script>	

			
	</div>
</div>


<?php 
	
	// Gather data for tree in four dimensions [root info][child info][leaf info][leaf data]
	function callDB($db){
	
		$sql = "SELECT app_name, app_version, app_status, 
					cmp_name, cmp_version, cmp_type, cmp_status,
					request_id, request_date, request_status, request_step,
					notes 
				FROM sbom";
			
		$result = $db->query($sql);
	
	
		$bom_ary;	// Not so nice 4-dimensional array that stores BOM table data. Enter the associative keys (app/cmp names) to access the data.
		$root_key;	// Stores root node data
		$child_key;	// Stores child node data
		//$autocomplete = []; // Stores all app and cmp names 
		//$autocomplete_num = []; // Numerically indexed keys from $autocomplete
 		
		if ($result->num_rows > 0) {
                   
			while($row = $result->fetch_assoc()) {
			
			// Store data for autocomplete
			//$autocomplete[$row["app_name"]] = 1;
			//$autocomplete[$row["cmp_name"]] = 1;
			
			// Store relevant components by Application (name+id)
			
			$root_key = $row["app_name"];
			$child_key = $row["app_name"]." ".$row["app_version"]."@".$row["app_status"];
			$leaf_key = $row["cmp_name"]." ".$row["cmp_version"];
			
			//$row["app_status"]
			$value = $row["cmp_type"]."@".$row["cmp_status"]
				."@".$row["request_id"]."@".$row["request_date"]."@".$row["request_status"]."@".$row["request_step"]
				."@".$row["notes"];
								 
				// Convert leaf data string into an numerically indexed array
				$bom_ary[$root_key][$child_key][$leaf_key][] = explode("@", $value);
            }
         }
         else {
            echo "0 results";
         }
		 
		// Convert $autocomplete associative array into a numerically index array for easier access
		
		/*
		foreach($autocomplete as $name=>$value){
		 $autocomplete_num[] = $name;
		}
		
		sort($autocomplete_num);
		*/
		$result->close();
	 
		return $bom_ary;
	}
		
	function setupTHeaders(){
			
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
	}

	
	// Set up the root nodes
		function phpMakeTree($db, $reds_yellows=false){
			
			$root_id=1;
			$child_id = 1;
			$leaf_id = 1;
			$output_string = "";
			
			$bom_ary = callDB($db);
		
			ksort($bom_ary);
		
			// Set up root - App names only
			foreach($bom_ary as $root=>$child_ary){
				
				root($root, $root_id);
			
				// Set up child - App names + Versions only
				foreach($child_ary as $child=>$leaf_array){
					
					child($child, $root_id, $child_id);
				
					$leaf_parent = $root_id.'.'.$child_id;
				
					// Set up leaf - Cmp Name + Versions only
					foreach($leaf_array as $leaf=>$leaf_values){
				
						leaf($leaf, $leaf_values, $leaf_parent ,$leaf_id);	
						$leaf_id++;
					}
				
					$leaf_id = 1;
					$child_id++;
				}
			
				// Draw child nodes as separate root nodes
				if($reds_yellows){
					$root_id = reds_yellows($child_ary, $root_id);
				}
				
				$child_id = 1;
				$root_id++;
			}

		}
	
	//<tr data-tt-id="x">
	function root($root, $id){
		
		$root_node = "";
		$root_node .= '\'<tr data-tt-branch=\"true\" class="root '.$root.'" data-tt-id="'.$id.'"><td class="root">'.$root.'</td&>';

		for($index=0; $index < 8; $index++){
				$root_node.= '<td class="root"></td>';
		}
		//&lt <  &gt >  &#47 /
		$root_node .= '</tr>\'';

		
		$output = '$(\'#sbom_tree\').treetable("loadBranch",null,'.$root_node.');';
		echo $output;
	}

	//<tr data-tt-id="x.x" or data-tt-id="x">
	function child($child, $parent_id, $child_id){
		
		$class = "child";
		$child_output = "";
				
		if($parent_id === 0){
			$child = explode("@",$child);
			$class = "root";
			
			$child_output .= '<tr data-tt-branch=\"true\" class="'.$class.' '.$child[0].'" data-tt-id="'.$child_id.'">';
			$child_output .= '<td class="'.$class.'">'.$child[0].'</td>';
			$child_output .= '<td class="'.$class.'">'.$child[1].'</td>';
		
			for($index=0; $index < 7; $index++){
				$child_output .= '<td class="'.$class.'"></td>';
			}
		
			$child_output .= '</tr>\'';
		
			$jquery_call = '$(\'#sbom_tree\').treetable("loadBranch",null,\''.$child_output.');';

			echo $jquery_call;
		}
		else{
			//Normal way
			$child = explode("@",$child);
		
			$child_output .= '<tr data-tt-branch=\"true\" class="'.$class.' '.$child[0].'" data-tt-id="'.$parent_id.'.'.$child_id.'" data-tt-parent-id="'.$parent_id.'">';
			$child_output .= '<td class="'.$class.'">'.$child[0].'</td>';
			$child_output .= '<td class="'.$class.'">'.$child[1].'</td>';
		
			for($index=0; $index < 7; $index++){
				$child_output .= '<td class="'.$class.'"></td>';
			}
		
			$child_output .= '</tr>\'';
		
			$jquery_call = '$(\'#sbom_tree\').treetable("loadBranch",null,\''.$child_output.');';

			echo $jquery_call;
		}
	}

	//<tr data-tt-id="x.x.x">
	function leaf($leaf, $leaf_ary, $parent_id, $leaf_id){
		
		$leaf_output = "";
		
		$leaf_output .= '\'<tr class="leaf '.$leaf.'" data-tt-id="'.$parent_id.'.'.$leaf_id.'" data-tt-parent-id="'.$parent_id.'">';
			
			$leaf_output .= '<td class="leaf">'.$leaf.'</td>';	
			
			foreach($leaf_ary as $leaf=>$data)
				$leaf_output .= leaf_data($data);
				
		$leaf_output .= '</tr>\'';
		$jquery_call = '$(\'#sbom_tree\').treetable("loadBranch",null,'.$leaf_output.');';
		echo $jquery_call;
	}
		
	// Prints out leaf node data under children of child() function
	function leaf_data($leaf_ary){
		$leaf_data = "";
		
		$leaf_data .= '<td class="leaf"></td>';
		
		foreach($leaf_ary as $key=>$value){
			$leaf_data .= '<td class="leaf">'.$value.'</td>';
		}
		
		return $leaf_data;
    }
	
	
	function reds_yellows($child_ary, $root_id){
		
		$leaf_id=1;
	
		foreach($child_ary as $child=>$leaf_array){
			//child($child,0,$root_id)
			child($child, 0, ++$root_id);
			
			$leaf_parent = $root_id;
			
			// Set up leaf - Cmp Name + Versions only
			foreach($leaf_array as $leaf=>$leaf_values){
			
				leaf($leaf, $leaf_values, $leaf_parent ,$leaf_id);	
				$leaf_id++;
			}
				
			$leaf_id = 1;
		}

		return $root_id;
	}
	
?>


<?php //include("./footer.php"); ?>
