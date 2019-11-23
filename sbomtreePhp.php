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
		
		
		if ($result->num_rows > 0) {
                   
			while($row = $result->fetch_assoc()) {

			// Root, child and leaf associative keys
			$root_key = $row["app_name"];
			$child_key = $row["app_name"]." ".$row["app_version"]."@".$row["app_status"];
			$leaf_key = $row["cmp_name"]." ".$row["cmp_version"];
			
			// Component/leaf data
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
		 
		$result->close();
	 
		return $bom_ary;
	}
		
	// Set up the columns by name
	function setupTHeaders(){
		
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
	
	/* 
	
	    getNodes()
			$echo flag determines what type of get is applied: 
				True - "get" the nodes during the table construction and add them to the nodes list, 
				False - "get" the nodes from the $nodes list
			
			Returns: an array of node id values
	*/
	
	function getNodes($node="", $tree=1 ,$echo=false){
		
		static $nodes;
		static $nodes2;
		
		if($echo){
			
			if($tree === 1){
				$nodes .= $node."@";
			}
		
			else if($tree === 2){
				$nodes2 .= $node."@";
			}
		}		
		else{
			// Trim off extra "@" delimeter and return the array of values
			if($tree === 1){
				$pos = strripos($nodes,"@");
				$nodes = substr($nodes, 0, $pos);
				echo json_encode(explode('@', $nodes));
			}
			else if($tree === 2){
				$pos = strripos($nodes2,"@");
				$nodes2 = substr($nodes2, 0, $pos);
				echo json_encode(explode('@', $nodes2));
			}
		}
	}
	
	// Echo out the tree with php either as Reds(ry=false), redsYellows(ry=true) or yellows y = true
	function phpMakeTree($db, $ry=false, $y=false){
			
			$root_id=1;
			$child_id = 1;
			$leaf_id = 1;
			$tree_switch = 1;
			
			if($ry){
				$tree_switch = 2;
			}
			
			$bom_ary = callDB($db);
		
			ksort($bom_ary);
		
			// Set up base - App names only
			foreach($bom_ary as $base=>$root_ary){
			
				$leaf_array;
				
				if(!$y){
					root($base, $tree_switch, $root_id);
				}
				
				// Set up root - App names + Versions only
				foreach($root_ary as $root=>$cmp_array){
					
					$leaf_array = $cmp_array;
					child($root, $tree_switch, $root_id, $child_id);
				
					$child_parent = $root_id.'.'.$child_id;
				
					// Set up component - Cmp Name + Versions only
					foreach($cmp_array as $child=>$cmp_values){
				
						leaf($child, $cmp_values, $child_parent ,$leaf_id);	
						$leaf_id++;
					}
					
					
					$leaf_id = 1;
					$child_id++;
				}
				
				if($ry){
					$root_id = redsYellows($root_ary, $tree_switch, $root_id, $leaf_array);
				}
				
				$child_id = 1;
				$root_id++;
			}

		}

	// Print out the root nodes - <tr data-tt-id="x">
	function root($root, $tree_switch, $id){

		//getNodes($id, $tree_switch, true);
		echo '<tr class="'.strToLower($root).'" data-tt-id="'.$id.'">';
		echo '<td class="root">'.$root.'</td>';
		
		for($index=0; $index < 8; $index++){
				echo '<td></td>';
		
		}
		echo "</tr>";
		
	}

	// Print out the child nodes - <tr data-tt-id="x.x">
	function child($child, $tree_switch, $parent_id, $child_id){
		
		$child = explode("@",$child);
		
		if($parent_id == 0){
			//getNodes($child_id, $tree_switch, true);
			echo '<tr class="child '.strToLower($child[0]).'" data-tt-id="'.$child_id.'">';
				
				echo '<td class="root">'.$child[0].'</td>';
				echo '<td >'.$child[1].'</td>';
				
				for($index=0; $index < 7; $index++){
					echo '<td></td>';
				}
			echo '</tr>';
		}
		else{
			//getNodes($parent_id.'.'.$child_id, $tree_switch, true);
			echo '<tr class="child '.strToLower($child[0]).'" data-tt-id="'.$parent_id.'.'.$child_id.'" data-tt-parent-id="'.$parent_id.'">';
					
				echo '<td class="child">'.$child[0].'</td>';
				echo '<td >'.$child[1].'</td>';
				
				for($index=0; $index < 7; $index++){
					echo '<td></td>';
				}
			echo '</tr>';
		}
	}

	// Print out the leaf nodes - <tr data-tt-id="x.x.x">
	function leaf($leaf, $leaf_ary, $parent_id, $leaf_id){
		echo '<tr class="'.$leaf.'" data-tt-id="'.$parent_id.'.'.$leaf_id.'" data-tt-parent-id="'.$parent_id.'">';
			
			echo '<td class="leaves ">'.$leaf.'</td>';	
			
			foreach($leaf_ary as $leaf=>$data)
				leafData($data);
				
		echo '</tr>';
	}
		
	// Prints out leaf node data under children of child() function
	function leafData($leaf_ary){
		echo '<td></td>';
		
		foreach($leaf_ary as $key=>$value){
			echo '<td>'.$value.'</td>';
		}
    }
	
	// Print out the children nodes as root nodes after their corresponding root node. 
	// Leaf nodes are still leaf nodes.
	function redsYellows($child_ary, $tree_switch, $root_id, $leaf_array){
		
		$leaf_id=1;
	
		foreach($child_ary as $child=>$leaf_array){

			child($child, $tree_switch, 0, ++$root_id);
			
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