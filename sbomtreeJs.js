		var active = "#sbom_tree";
		/* var roots = document.getElementsByClassName('root');
		var chldrn = document.getElementsByClassName('child');
		var expandNodes = [];
		var e_index = 0;
		
		$(document).ready(function(){
						
					var root_list = Array.from(roots);
					var child_list = Array.from(chldrn);
					
					if(length > 0){						
						
						for(var exist_index = 0; exist_index < node_list.length; exist_index++){				
							expandNodes[] = root_list[exist_index].getAttribute("data-tt-id");
						}						
						
					}
		});
		 */
		
		$(document).ready(function(){
				$("#expand, #expand2").click(function(){				
					$(active).treetable('expandAll');
					//alert("Expand");
					
				});
		});
		

		$(document).ready(function(){
				$("#collapse, #collapse2").click(function(){
					$(active).treetable('collapseAll');
					//alert("Collapse");
				});
		});
	
		$(document).ready(function(){
				$("#where_button, #where_button2").click(function(){
					
					var which;
					
					if(active==="#sbom_tree"){
							which = "where_used";
					}
					else if(active==="#sbom_tree2"){
							which = "where_used2";
					}
					
					var value = document.getElementById(which).value;
				
						value = formatInput(value);
						selectElement(value, active);
					
					
				});
		});
	
		$(document).ready(function(){
				$("#where_used, #where_used2").keydown(function(event){
					$key_pressed = event.which;
					if($key_pressed == 13){
						
						var which;
						
						if(active==="#sbom_tree"){
							which = "where_used";
						}else if(active==="#sbom_tree2"){
							which = "where_used2";
						}
						
						var value = document.getElementById(which).value;
						
						value = formatInput(value);
						selectElement(value, active);
						
					}
				});
		});
		
		var color_flag = 0;
		var rootOrigColor;
		var childOrigColor;
		var leafOrigColor;
		
		
		var highlighted;
		var reds = true; // Flag to verify reds class active
		var reds_yellows = false;	// Flag to verify reds_yellow class active
		var colors = false;

		
		var reds_toggle = 0;
		
		$(document).ready(function(){
				$("#reds, #reds2").click(function(){
					
					if(!reds){
						reds_yellows = false;
						reds = true;
						
						active = "#sbom_tree";
					
						var trees = document.getElementById("tables");
						var redTree = document.getElementById("sbom_tree");
						var redYellowTree = document.getElementById("sbom_tree2");
						
						//<DOM object>.replaceChild() https://www.w3schools.com/jsref/met_node_replacechild.asp
						redYellowTree.setAttribute("style", "visibility: hidden");
						trees.insertBefore(redTree,redYellowTree);
						redTree.setAttribute("style", "visibility:");
					}
				});
				
		
		});
		

		var reds_yellows_toggle = 0;

		$(document).ready(function(){
				$("#red_yellow, #red_yellow2").click(function(){
					
					if(!reds_yellows){
						
						reds_yellows = true;
						reds = false;
						
						active = "#sbom_tree2";
						
						var trees = document.getElementById("tables");
						var redTree = document.getElementById("sbom_tree");
						var redYellowTree = document.getElementById("sbom_tree2");
	
						//<DOM object>.replaceChild() https://www.w3schools.com/jsref/met_node_replacechild.asp
						redTree.setAttribute("style", "visibility: hidden");
						trees.insertBefore(redYellowTree,redTree);
						redYellowTree.setAttribute("style", "visibility: visible");
					}
				});
		});
		
		$(document).ready(function(){
				$("#colorize, #colorize2").click(function(){			

											
				var root_nodes = document.getElementsByClassName("root");
				var child_nodes = document.getElementsByClassName("child");
				var leaf_nodes = document.getElementsByClassName("leaves");
									
					if(color_flag == 0){	
							
						color(root_nodes, "root_colored");
						color(child_nodes, "child_colored");
						color(leaf_nodes, "leaf_colored");

						color_flag = 1;
					}
					else if(color_flag == 1){
						
						removeColor(root_nodes, "root_colored");
						removeColor(child_nodes, "child_colored");
						removeColor(leaf_nodes, "leaf_colored");

						color_flag = 0;
					}
					
				});
		});
			
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
			
			function selectElement(target, active){

				var results;
				var children;
				
				var which;
				
				if(active === "#sbom_tree"){
					which = "sbom_tree";
				}
				else if(active === "#sbom_tree2"){
					which = "sbom_tree2";
				}
				
				highlighted = document.getElementById(which).getElementsByClassName("highlight_node");
				
				removeHighlighted(highlighted);
										
				results	= document.getElementById(which).getElementsByClassName(target);
	
				for(var result_index = 0; result_index < results.length; result_index++){
					
					var node = (results[result_index].getAttribute("data-tt-id"));
				
					$(active).treetable("reveal", node);
					
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
						$(active).treetable("collapseAll");
						
					}
					
			}
			
			function formatInput(string){
			
				if(string.indexOf(';') !== -1){
					string = string.replace(';', ' ');
				}
				else if(string.indexOf(',') !== -1){
					string = string.replace(',', ' ');
				}
				
				return string.toLowerCase();
			}
			