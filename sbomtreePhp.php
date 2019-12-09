<?php

  require_once('initialize.php');
  global $db;

  $child_ary = []; // Store the children nodes
  
  
	// Insert child nodes into child array, remove from leaf($cmp_ary) array
	function fillChildAry(&$bom_ary, &$cmp_ary, &$child_ary){
			
			foreach($bom_ary as $rc=>$chlf_ary){
			
				if(array_key_exists($rc, $cmp_ary)){
					$child_ary[$rc]["specs"] = $cmp_ary[$rc]["specs"]; 
					$child_ary[$rc]["chldrn"] = $bom_ary[$rc];
					unset($cmp_ary[$rc]);
				
				}
			}

	}
  
    
	// Remove child nodes from RED tree array
	function removeChldrn(&$bom_ary, $child_ary){
				
		if($child_ary != null){
			
			foreach($bom_ary as $rc=>$chlf_ary){
			
				if(array_key_exists($rc, $child_ary)){
					unset($bom_ary[$rc]);		
				}
			
			}
		}
		
	}
  
  
	function finishTree(&$bom_ary, $child_ary){
		
		foreach($bom_ary as $rc=>$chlf_ary){

			foreach($chlf_ary as $cl=>$data){
				
				$bom_ary[$rc][$cl] = updateChild($cl, $data, $child_ary);
				
			}
			
			
		}
		
		
	}
  
	function updateChild($node_name, &$node, $child_ary){

	if($child_ary !=null){
		if(array_key_exists($node_name, $child_ary)){

			$node = $child_ary[$node_name];		

			//not sure if correct
			foreach($node as $key=>$data){
				if($key === "chldrn"){
					$node[$key] = updateChild($key, $data, $child_ary);
				}
			}
		}
		
		return $node;	
	}
		
	}
  
	// Gather data for tree in four dimensions [root info][child info][leaf info][leaf data]
	function callDB($db, $sql="", $child_flag=false){
		
		// Start DB1 call
		// Store array of components - compare to application name later to confirm if application is a root or a child
		
		$cmp_ary; // Stores component nodes
		//$child_ary = []; // Stores child nodes
		
		$query = "SELECT DISTINCT concat(cmp_name, ' ', cmp_version, '@', cmp_status) AS name, cmp_type,
				  request_status, request_step, notes 
				  FROM sbom;";
				  
		$result = $db->query($query);
		
		if ($result->num_rows > 0) {
                   
			while($row = $result->fetch_assoc()) {
				
				$nm = $row['name'];
				$cmp_ary[$nm]["specs"][] = $row['cmp_type'];
				$cmp_ary[$nm]["specs"][] = $row['request_status'];
				$cmp_ary[$nm]["specs"][] = $row['request_step'];
				$cmp_ary[$nm]["specs"][] = $row['notes'];
			
            }
         }
	
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
	
		$cp_ary;
		$root_ary;	// Last minute patch to get code to function correctly - not the ideal solution
		$bom_ary;	// Array that stores BOM table data. Enter the associative keys (app/cmp names) to access the data.
		$rc_key; 	// Stores the root or child key 
		//$child_ary; // Store the children nodes
		
		if ($result->num_rows > 0) {
                   
			while($row = $result->fetch_assoc()) {

			// Root, child and leaf associative keys
			$rc_key = $row["app_name"]." ".$row["app_version"]."@".$row["app_status"];
			$leaf_key = $row["cmp_name"]." ".$row["cmp_version"]."@".$row["cmp_status"];
			
			// Component/leaf data
			$value = $row["cmp_status"]."@".$row["cmp_type"]
				."@".$row["request_status"]."@".$row["request_step"]
				."@".$row["notes"];
								 
				// Convert leaf data string into an numerically indexed array
				$bom_ary[$rc_key][$leaf_key]["specs"] = explode("@", $value);
				$root_ary[$rc_key]["name"] = $row["app_name"]; 
				$root_ary[$rc_key]["version"] = $row["app_version"]; 
				$cp_ary[$leaf_key]["name"] = $row["cmp_name"]; 
				$cp_ary[$leaf_key]["version"] = $row["cmp_version"]; 
            }
         }
         else {
            return false;
         }
		 
		 // END DB2 call 
		 
		$result->close();
	 
		
		// Build array with children nodes to be subbed for children nodes in main RED bom tree array
		fillChildAry($bom_ary, $cmp_ary, $child_ary);
		removeChldrn($bom_ary, $child_ary);
		finishTree($bom_ary, $child_ary);
		
		if($child_flag){
			return $child_ary;
		}
		
		$return_ary = [];
		
		$return_ary["bom"] = $bom_ary;
		$return_ary["root"] = $root_ary;
		$return_ary["cp"] = $cp_ary;
		
		return $return_ary;
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
			
			$returned  = callDB($db, $sql);

			$bom_ary = $returned["bom"];
			$rt_ary = $returned["root"];
			$cp_ary = $returned["cp"];			
			
			if($bom_ary !== false){
			
				ksort($bom_ary);
		
			if(!$y || $ry){
		
				// Set up base - App names only
				foreach($bom_ary as $root=>$root_ary){
			
					$leaf_array;
				
					// Echo out root node data
					root($rt_ary, $root, $tree_switch, $root_id);
					
					
					// Set up root - App names + Versions only
					foreach($root_ary as $cmp=>$cmp_array){

						$leaf_ary = $cmp_array;
					
						if(array_key_exists("chldrn", $cmp_array)){
							child($rt_ary, $cp_ary, $cmp, $tree_switch, $root_id, $child_id, $cmp_array, $leaf_id);
							$child_id++;
						}
						else{
							leaf($cp_ary, $cmp, $cmp_array["specs"], $root_id, $child_id);
						}
		
				
						$leaf_id = 1;
						$child_id++;
						
					
					}

				
					$child_id = 1;
					$root_id++;
				}
			}
			
			if($y || $ry){
				$child_ary = callDB($db, $sql, true);
				

				if($ry){
					$child_id = $root_id;
				}
				
				foreach($child_ary as $cmp=>$cmp_array){

						$leaf_ary = $cmp_array;
					
						if(array_key_exists("chldrn", $cmp_array)){
							child_y($rt_ary, $cp_ary, $cmp, $tree_switch, $child_id, $cmp_array, $leaf_id, $ry);
							$child_id++;
						}
						else{
							leaf_y($cp_ary, $cmp, $cmp_array["specs"], $child_id, $leaf_id);
						}
						
				}
				$leaf_id = 1;	
			}
			
			}
			else{ 
				return false;
			}
		}

	// Print out the root nodes - <tr data-tt-id="x">
	function root($rt_ary, $root, $tree_switch, $id){

		//getNodes($id, $tree_switch, true);
		$root_data = explode("@", $root);
		$name_ver = explode(" ",$root_data[0]);
		
		echo '<tr class="'.strToLower($root_data[0]).'" data-tt-id="'.$id.'">';
		echo '<td class="root">'.$rt_ary[$root]["name"].'</td>';
		echo '<td >'.$rt_ary[$root]["version"].'</td>';
		echo '<td >'.$root_data[1].'</td>';
		
		for($index=0; $index < 4; $index++){
				echo '<td></td>';
		
		}
		echo "</tr>";
		
	}

	// Print out the child nodes - <tr data-tt-id="x.x">
	function child($rt_ary, $cp_ary, $child, $tree_switch, $parent_id, $child_id, $cmp_ary, $leaf_id){
		
		$chld_id = $parent_id.'.'.$child_id;

		if(array_key_exists("chldrn", $cmp_ary)){			
			
			$child_data = explode("@",$child);
			$name_ver = explode(" ", $child_data[0]);
			

			echo '<tr class="child '.strToLower($child_data[0]).'" data-tt-id="'.$chld_id.'" data-tt-parent-id="'.$parent_id.'">';
					
			echo '<td class="child">'.$rt_ary[$child]["name"].'</td>';
			echo '<td >'.$rt_ary[$child]["version"].'</td>';
		
			for($index=0 ;$index < 4; $index++){
				echo '<td>'.$cmp_ary["specs"][$index].'</td>';
			}
			echo '</tr>';
		
			foreach($cmp_ary["chldrn"] as $chlf=>$data){
				child($rt_ary, $cp_ary, $chlf, $tree_switch, $parent_id, $child_id, $data, $leaf_id);
				$leaf_id++;
			}

		}
		else{
				leaf($cp_ary, $child,$cmp_ary["specs"],$chld_id,$leaf_id, 1);
		}

			
		
		$leaf_id=1;
		
		//print_r($cmp_ary);
	}

	
	function child_y($rt_ary, $cp_ary, $child, $tree_switch, $child_id, $cmp_ary, $leaf_id, $ry=false){
		
		
		$child_data = explode("@",$child);
		$name_ver = explode(" ", $child_data[0]);
			
		if($ry){
			if(array_key_exists("chldrn", $cmp_ary)){			
			
			$child_data = explode("@",$child);
			$name_ver = explode(" ", $child_data[0]);
			

			echo '<tr class="root '.strToLower($child_data[0]).'" data-tt-id="'.$child_id.'">';
					
			echo '<td class="root">'.$rt_ary[$child]["name"].'</td>';
			echo '<td >'.$rt_ary[$child]["version"].'</td>';
			echo '<td >'.$child_data[1].'</td>';
		
		
			for($index=0 ;$index < 4; $index++){
				echo '<td></td>';
			}
			echo '</tr>';
	
			foreach($cmp_ary["chldrn"] as $chlf=>$data){
				child_y($rt_ary, $cp_ary, $chlf, $tree_switch, $child_id, $data, $leaf_id);
				$leaf_id++;
			}

		}
			else{
				//echo $child_id."<br/>";
				leaf_y($cp_ary, $child,$cmp_ary["specs"],$child_id,$leaf_id, 1);
			}

			
		
			$leaf_id=1;
		}
		else{
			if(array_key_exists("chldrn", $cmp_ary)){			
			
				$child_data = explode("@",$child);
				$name_ver = explode(" ", $child_data[0]);
			

				echo '<tr class="child '.strToLower($child_data[0]).'" data-tt-id="'.$child_id.'">';
					
				echo '<td class="child">'.$name_ver[0].'</td>';
				echo '<td >'.$name_ver[1].'</td>';
		
				for($index=0 ;$index < 4; $index++){
					echo '<td>'.$cmp_ary["specs"][$index].'</td>';
				}
				echo '</tr>';
	
				foreach($cmp_ary["chldrn"] as $chlf=>$data){
					child_y($rt_ary, $cp_ary, $chlf, $tree_switch, $child_id, $data, $leaf_id);
					$leaf_id++;
				}

			}
			else{
				//echo $child_id."<br/>";
				leaf_y($cp_ary, $child,$cmp_ary["specs"],$child_id,$leaf_id, 1);
			}

			
		
			$leaf_id=1;
		}
	}
	
		// Print out the leaf nodes - <tr data-tt-id="x.x.x">
	function leaf_y($cp_ary, $leaf, $leaf_ary, $parent_id, $leaf_id,$child_flag=0){

			$leaf_data = explode("@", $leaf);
			$name_ver = explode(" ", $leaf_data[0]);
		
			echo '<tr class="'.strToLower($leaf_data[0]).'" data-tt-id="'.$parent_id.".".$leaf_id.'" data-tt-parent-id="'.$parent_id.'">';
			
			echo '<td class="leaves ">'.$cp_ary[$leaf]["name"].'</td>';	
			echo '<td ">'.$cp_ary[$leaf]["version"].'</td>';	
			
			leafData($leaf_ary);
			
			echo '</tr>';		
		
	}
	
	
	// Print out the leaf nodes - <tr data-tt-id="x.x.x">
	function leaf($cp_ary, $leaf, $leaf_ary, $parent_id, $leaf_id,$child_flag=0){

			$leaf_data = explode("@", $leaf);
			$name_ver = explode(" ", $leaf_data[0]);
		
			echo '<tr class="'.strToLower($leaf_data[0]).'" data-tt-id="'.$parent_id.'.'.$leaf_id.'" data-tt-parent-id="'.$parent_id.'">';
			
			echo '<td class="leaves ">'.$cp_ary[$leaf]["name"].'</td>';	
			echo '<td ">'.$cp_ary[$leaf]["version"].'</td>';	
			
			leafData($leaf_ary);
			
			echo '</tr>';		
		
	}
		
	// Prints out leaf node data under children of child() function
	function leafData($leaf_ary){

		foreach($leaf_ary as $key=>$value){
			echo '<td>'.$value.'</td>';
		}
		
    }
	
	// Print out the children nodes as root nodes after their corresponding root node. 
	// Leaf nodes are still leaf nodes.
	function redsYellows($child_ary, $tree_switch, $root_id, $leaf_array){
		
		$leaf_id=1;
	
		foreach($child_ary as $child=>$leaf_array){

			child($rt_ary, $cp_ary, $child, $tree_switch, 0, ++$root_id);
			
			$leaf_parent = $root_id;
			
			// Set up leaf - Cmp Name + Versions only
			foreach($leaf_array as $leaf=>$leaf_values){
			
				leaf($cp_ary, $leaf, $leaf_values, $leaf_parent ,$leaf_id);	
				$leaf_id++;
			}
				
			$leaf_id = 1;
		}

		return $root_id;
	}
	
	
	
	//TEST
	
	//callDB($db);
	
?>