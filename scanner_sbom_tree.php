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
				
				
				
			<div id="trees">

				<button id="expand">Exp</button>
		
				<script>
			
					$("#expand").click( function(){
						$("#test").treetable("expandAll");
						//alert("expand");
					});
			
				</script>
		
				<table id="test">

				<caption>Practice Tree</caption>
				<thead>
				<tr>
					<th>Tree column</th>
					<th>Additional data</th>
					<th>more info</th>
				</tr>
				</thead>
				<tbody>
				<tr data-tt-id="1">
					<td>Node 1: Click on the icon in front of me to expand this branch.</td>
					<td>I live in the second column.</td>
					<td>I live in the third column.</td>
				</tr>
				<tr data-tt-id="1.1" data-tt-parent-id="1">
					<td>Node 1.1: Look, I am a table row <em>and</em> I am part of a tree!</td>
					<td>Interesting.</td>
				<td>I live in the third column.</td>
				</tr>
				<tr data-tt-id="1.1.1" data-tt-parent-id="1.1">
					<td>Node 1.1.1: I am part of the tree too!</td>
					<td>That's it!</td>
					<td>I live in the third column.</td>
				</tr>
				<tr data-tt-id="2">
					<td>Node 2: I am another root node, but without children</td>
					<td>Hurray!</td>
					<td>I live in the third column.</td>
				</tr>
				</tbody>
	  
			</table>
		
		<script src="jquery.treetable.js"></script>
			
		<script>
			$("#test").treetable({ expandable: true });
		</script>		
					
	
			
		</div>
				
	
				
	</div>
</div>



<?php include("./footer.php"); ?>
