<?php

  require_once('initialize.php');
  global $db;

	function fillChildAry($bom_ary, $cmp_ary, $child_ary){
			
			foreach($bom_ary as $rc=>$chlf_ary){
			
				if(array_key_exists($rc, $cmp_ary)){
					$child_ary[$rc]["specs"] = $cmp_ary[$rc]["specs"]; 
					$child_ary[$rc]["chldrn"] = $bom_ary[$rc];
					unset($cmp_ary[$rc]);
				
				}
			}
		return $child_ary;
	}
  
  
	// Gather data for tree in four dimensions [root info][child info][leaf info][leaf data]
	function callDB($db, $sql=""){
		
		// Start DB1 call
		// Store array of components - compare to application name later to confirm if application is a root or a child
		
		$cmp_ary; // Stores component nodes
		$child_ary = []; // Stores child nodes
		
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
	
		//echo "<pre>";
		//print_r($cmp_ary);
		//echo "<pre>";
	
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
			$value = $row["cmp_status"]."@".$row["cmp_type"]
				."@".$row["request_status"]."@".$row["request_step"]
				."@".$row["notes"];
								 
				// Convert leaf data string into an numerically indexed array
				$bom_ary[$rc_key][$leaf_key]["specs"] = explode("@", $value);
            }
         }
         else {
            return false;
         }
		 
		 // END DB2 call 
		 
		$result->close();
	 
	 
		// Build array with children nodes to be subbed for children nodes in main bom tree array
		$child_ary = fillChildAry($bom_ary, $cmp_ary, $child_ary);
		
		
		echo "<pre>";
		echo "Cmp ";
		//print_r($cmp_ary);
		print_r($child_ary);
		echo "</pre>";
		
		
		// Remove child nodes from RED tree array
		foreach($bom_ary as $rc=>$chlf_ary){
			
			if(array_key_exists($rc, $cmp_ary)){
				unset($bom_ary[$rc]);
				//print_r($cmp_ary);				
			}
			
		}
		
		
		// Copy root-child data from cmp array into bom array to complete RED tree
		foreach($bom_ary as $rc=>$chlf_ary){
			
			foreach($chlf_ary as $root=>$cmp){
							
				if(array_key_exists($root, $cmp_ary)){
						
						//echo "<pre>";
						//echo $root;
						//print_r($cmp);
						//echo "</pre>";
						$bom_ary[$rc][$root] = $cmp_ary[$root];
				}
	
			}
			
				
				//echo "<pre>";
				//echo $rc;
				//print_r($cmp_ary[$rc]);
				//echo "</pre>";
			
		}
		
				//echo "<pre>";
				//print_r($cmp_ary);
				//echo "</pre>";
		//echo "<pre>";
		
		/*
		foreach($bom_ary as $key=>$chlf){
						
			foreach($chlf as $clkey=>$data){
			
				if(array_key_exists($clkey, $child_ary)){

					$bom_ary[$key][$clkey]["child_flag"] = true;
					//if ["child_flag"] array_slice(<$array_name>, "child_flag");
					$bom_ary[$key][$clkey] = $child_ary[$clkey];
				}
				else{
					// Do we check for duplicates or should they all be unique and not need to be checked?
					// The database needs to be updated to reflect newer version on DB_Layer 2.4 for QM2.2
				
					//echo $key."<br>";
				}
			}			
		
		}
		

	
		
	*/
		//return $bom_ary;
		//echo "<pre>";
		//print_r($bom_ary);
		//print_r($cmp_ary);
		//echo "</pre>";
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
			
			$bom_ary = callDB($db, $sql);
			
			if($bom_ary !== false){
			
				ksort($bom_ary);
		
				// Set up base - App names only
				foreach($bom_ary as $base=>$root_ary){
			
					$leaf_array;
				
					root($base, $tree_switch, $root_id);
					
				/*	
					// Set up root - App names + Versions only
					foreach($root_ary as $root=>$cmp_array){
						
						$leaf_ary = $cmp_array;
					
						if(array_key_exists("child_flag", $cmp_array)){
							unset($cmp_array["child_flag"]);
							child($root, $tree_switch, $root_id, $child_id, $cmp_array);
							unset($cmp_array[0]);
							unset($cmp_array[1]);
							$child_id++;
						}
				
						$child_parent = $root_id.'.'.$child_id;
						
						leaf($root, $cmp_array, $root_id, $child_id);
				
						$leaf_id = 1;
						$child_id++;
					
					}
				*/
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
		$name_ver = explode(" ",$root_data[0]);
		
		echo '<tr class="'.strToLower($root_data[0]).'" data-tt-id="'.$id.'">';
		echo '<td class="root">'.$name_ver[0].'</td>';
		echo '<td >'.$name_ver[1].'</td>';
		echo '<td >'.$root_data[1].'</td>';
		
		for($index=0; $index < 4; $index++){
				echo '<td></td>';
		
		}
		echo "</tr>";
		
	}

	// Print out the child nodes - <tr data-tt-id="x.x">
	function child($child, $tree_switch, $parent_id, $child_id, $cmp_ary){

		$child_data = explode("@",$child);
		$name_ver = explode(" ", $child_data[0]);
		$chld_id = $parent_id.'.'.$child_id;
		
			//getNodes($parent_id.'.'.$child_id, $tree_switch, true);
		echo '<tr class="child '.strToLower($child_data[0]).'" data-tt-id="'.$chld_id.'" data-tt-parent-id="'.$parent_id.'">';
					
		echo '<td class="child">'.$name_ver[0].'</td>';
		echo '<td >'.$name_ver[1].'</td>';
		//echo '<td >'.$child_data[1].'</td>';
		
		for($index=0 ;$index < 4; $index++){
			echo '<td>'.$cmp_ary[0][$index].'</td>';
		}
		echo '</tr>';
		
		
		$leaf_id=1;

		foreach($cmp_ary[1] as $leaf=>$l_data){
			leaf($leaf,$l_data,$chld_id,$leaf_id, 1);
			
			$leaf_id++;
		}
		
		$leaf_id=1;
		
		//print_r($cmp_ary);
	}

	// Print out the leaf nodes - <tr data-tt-id="x.x.x">
	function leaf($leaf, $leaf_ary, $parent_id, $leaf_id,$child_flag=0){
		
		if($child_flag === 1){
			$leaf_data = explode("@", $leaf);
			$name_ver = explode(" ", $leaf_data[0]);
		
			echo '<tr class="'.strToLower($leaf_data[0]).'" data-tt-id="'.$parent_id.'.'.$leaf_id.'" data-tt-parent-id="'.$parent_id.'">';
			
			echo '<td class="leaves ">'.$name_ver[0].'</td>';	
			echo '<td ">'.$name_ver[1].'</td>';	
			
			foreach($leaf_ary as $leaf=>$data){
				leafData($data);
			}
			echo '</tr>';		
		}
		else{
			$leaf_data = explode("@", $leaf);
			$name_ver = explode(" ", $leaf_data[0]);
		
			echo '<tr class="'.strToLower($leaf_data[0]).'" data-tt-id="'.$parent_id.'.'.$leaf_id.'" data-tt-parent-id="'.$parent_id.'">';
			
			echo '<td class="leaves ">'.$name_ver[0].'</td>';	
			echo '<td ">'.$name_ver[1].'</td>';	
			
			foreach($leaf_ary as $leaf=>$data){
				leafData($data);
			}
			echo '</tr>';		
		}
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