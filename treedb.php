<?php

  require_once('initialize.php');
  global $db;

	// Gather data for tree in four dimensions [root info][child info][leaf info][leaf data]
	function callDB($db, $sql=""){
		
		// Start DB1 call
		// Store array of components - compare to application name later to confirm if application is a root or a child
		
		$cmp_ary;
		$child_ary;
		$query = "SELECT DISTINCT concat(cmp_name, ' ', cmp_version, '@', cmp_status) AS name FROM sbom;";
		$result = $db->query($query);
		
		if ($result->num_rows > 0) {
                   
			while($row = $result->fetch_assoc()) {
				
				$nm = $row['name'];
				$cmp_ary[$nm] = true;
			
            }
         }
	
		echo "<pre>";
		//print_r($cmp_ary);
		echo "<pre>";
	
		//$key = "Jquery 4.3";
		//echo array_key_exists($key, $cmp_ary);
	
		// End DB1 call
	
		// DB2 call - fill RED tree array
		if($sql === ""){
			$sql = "SELECT app_name, app_version, app_status, 
					cmp_name, cmp_version, cmp_type, cmp_status,
					request_id, request_date, request_status, request_step,
					notes 
					FROM sbom";
		}
		
		$result = $db->query($sql);
	
		$bom_ary;	// Not so nice 4-dimensional array that stores BOM table data. Enter the associative keys (app/cmp names) to access the data.
		$rc_key; 	// Stores the root or child key 
		$child_ary; // Store the children nodes
		
		if ($result->num_rows > 0) {
                   
			while($row = $result->fetch_assoc()) {

			// Root, child and leaf associative keys
			$rc_key = $row["app_name"]." ".$row["app_version"]."@".$row["app_status"];
			$leaf_key = $row["cmp_name"]." ".$row["cmp_version"]."@".$row["cmp_status"];
			
			// Component/leaf data
			$value = $row["cmp_type"]."@".$row["cmp_status"]
				."@".$row["request_status"]."@".$row["request_step"]
				."@".$row["notes"];
								 
				// Convert leaf data string into an numerically indexed array
				$bom_ary[$rc_key][$leaf_key][] = explode("@", $value);
            }
         }
         else {
            return false;
         }
		 
		 // END DB2 call 
		 
		$result->close();
	 
	 
		// Copy root-child data from main db copy into child array to complete RED tree
		foreach($bom_ary as $rc=>$chlf_ary){
			
			if(array_key_exists($rc, $cmp_ary)){
				$child_ary[$rc] = $chlf_ary;
				//print_r($cmp_ary);				
			}
		}
		
		
		echo "<pre>";
		
		
		foreach($bom_ary as $key=>$chlf){
						
			foreach($chlf as $clkey=>$data){
			
				if(array_key_exists($clkey, $child_ary)){

					$bom_ary[$key][$clkey]["child_flag"] = true;
					//if ["child_flag"] array_slice(<$array_name>, "child_flag");
					$bom_ary[$key][$clkey][] = $child_ary[$clkey];
				}
				else{
					// Do we check for duplicates or should they all be unique and not need to be checked?
					// The database needs to be updated to reflect newer version on DB_Layer 2.4 for QM2.2
				
					//echo $key."<br>";
				}
			}			
		
		}
		

		// Remove child nodes from RED tree array
		foreach($bom_ary as $rc=>$chlf_ary){
			
			if(array_key_exists($rc, $cmp_ary)){
				unset($bom_ary[$rc]);
				//print_r($cmp_ary);				
			}
			
		}
		
	
		
		
		
		print_r($bom_ary);
		//echo "<pre>";
		return $bom_ary;
		//print_r($child_ary);
		
	}
		
	// Set up the columns by name
	function setupTHeaders(){
		
			echo "<th>Application</th>";			
			echo "<th>Version</th>";			
			echo "<th>Status</th>";
			echo "<th>Component Type</th>";
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
	function phpMakeTree($db, $ry=false, $y=false, $sql=""){
			
			$root_id=1;
			$child_id = 1;
			$leaf_id = 1;
			$tree_switch = 1;
			
			if($ry){
				$tree_switch = 2;
			}
			
			$bom_ary = callDB($db, $sql);
			
			if($bom_ary !== false){
			
				ksort($bom_ary);
		
				// Set up base - App names only
				foreach($bom_ary as $base=>$root_ary){
			
			
					$leaf_array;
				
					if(!$y){
						root($base, $tree_switch, $root_id);
					}
					
				/*
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
					*/
					}
				
				/*
					if($ry){
						$root_id = redsYellows($root_ary, $tree_switch, $root_id, $leaf_array);
					}
				*/
				
					$child_id = 1;
					$root_id++;
				}
			}
			else{ 
				return false;
			}
		}

	// Print out the root nodes - <tr data-tt-id="x">
	function root($root, $tree_switch, $id){

		//getNodes($id, $tree_switch, true);
		$root_data = explode("@", $root);
		echo '<tr class="'.strToLower($root_data).'" data-tt-id="'.$id.'">';
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
		echo '<tr class="'.strToLower($leaf).'" data-tt-id="'.$parent_id.'.'.$leaf_id.'" data-tt-parent-id="'.$parent_id.'">';
			
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
	
	
	
	//TEST
	
	callDB($db);
	
?>