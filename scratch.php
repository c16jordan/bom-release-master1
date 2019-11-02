<?php
		function testPhpMakeTree($db){
			
			$parent_id=1;
			$root_id = 1;
			$child_id = 1;
		
			$bom_ary = callDB($db);
		
			ksort($bom_ary);
		
			// Set up base - App names only
			foreach($bom_ary as $base=>$root_ary){
			
				rootRoot($base, $parent_id);
			
				// Set up root - App names + Versions only
				foreach($root_ary as $root=>$cmp_array){
					
					rootChild($root, $parent_id, $root_id);
				
					$child_parent = $parent_id.'.'.$root_id;
				
					// Set up component - Cmp Name + Versions only
					foreach($cmp_array as $child=>$cmp_values){
				
						//rootLeaf($child, $cmp_values, $child_parent ,$child_id);	
						//$child_id++;
					}
				
					$child_id = 1;
					$root_id++;
				}
			
				$root_id = 1;
				$parent_id++;
			}
		
		}
	
		//<tr data-tt-id="x">
	function rootRoot($root, $id){
			
		echo '<tr class="root '.$root.'" data-tt-branch=\"true\" data-tt-id="'.$id.'">';
		echo '<td class="root">'.$root.'</td>';
		
		for($index=0; $index < 8; $index++){
				echo '<td class="root"></td>';
		}
		
		echo "</tr>";
		
	}

	//<tr data-tt-id="x.x">
	function rootChild($child, $parent_id, $child_id){
		
	    $child = explode("@",$child);
		echo '<tr class="child '.$child[0].'" data-tt-branch=\"true\" data-tt-id="'.$parent_id.'.'.$child_id.'" data-tt-parent-id="'.$parent_id.'">';
				
			echo '<td class="child">'.$child[0].'</td>';
			echo '<td class="child">'.$child[1].'</td>';
				
			for($index=0; $index < 7; $index++){
				echo '<td class="child"></td>';
			}
		echo '</tr>';
		
	}

	//<tr data-tt-id="x.x.x">
	function rootLeaf($leaf, $leaf_ary, $parent_id, $leaf_id){
		echo '<tr class="leaf '.$leaf.'" data-tt-id="'.$parent_id.'.'.$leaf_id.'" data-tt-parent-id="'.$parent_id.'">';
			
			echo '<td class="leaf">'.$leaf.'</td>';	
			
			foreach($leaf_ary as $leaf=>$data)
				rootLeafData($data);
				
		echo '</tr>';
	}
		
	// Prints out leaf node data under children of child() function
	function rootLeafData($leaf_ary){
		echo '<td class="leaf"></td>';
		
		foreach($leaf_ary as $key=>$value){
			echo '<td class="leaf">'.$value.'</td>';
		}
    }
	
?>