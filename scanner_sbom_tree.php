<?php
  $nav_selected = "SCANNER";
  $left_buttons = "YES";
  $left_selected = "SBOMTREE";

  include("./nav.php");   
  include('sbomtreePhp.php');
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
 <div id="tables">
 
 <table id="sbom_tree">
 
	<div>
	
		<caption>
			<button id="expand" style="font-size: 10px">Expand All</button>
			<button id="collapse" style="font-size: 10px">Collapse All</button>
			<button id="colorize" style="font-size: 10px"> Toggle Color </button>
			<button id="reds" style="font-size: 10px">Reds</button>
			<button id="red_yellow" style="font-size: 10px"> Reds and Yellows </button>
			<button id="where_button" style="font-size: 10px; margin-left:25px">Where used</button>
		
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
		<?php phpMakeTree($db);?>
	</tbody>

</table>	
		

<table id="sbom_tree2" style="visibility: hidden">
 
	<div>
	
		<caption>
			<button id="expand2" style="font-size: 10px">Expand All</button>
			<button id="collapse2" style="font-size: 10px">Collapse All</button>
			<button id="colorize2" style="font-size: 10px"> Toggle Color </button>
			<button id="reds2" style="font-size: 10px">Reds</button>
			<button id="red_yellow2" style="font-size: 10px"> Reds and Yellows </button>
			<button id="where_button2" style="font-size: 10px; margin-left:25px">Where used</button>
	
			<input id="where_used2" type="text" placeholder="name;[version id] option"></input>
			<span id="error"></span>
		
		</caption>
		
	</div>
	

	<thead>
	
	<tr>
		<?php setupTheaders(); ?>
	</tr>
	
	</thead>
	
	<tbody id="treeSpace2">
		<?php  phpMakeTree($db, true); ?>
	</tbody>


</div>
		<script src="sbomtreeJs.js"> </script>
		<script src="jquery.treetable.js"></script>	
				
		<script>
			$("#sbom_tree").treetable({ expandable: true });
			$("#sbom_tree2").treetable({ expandable: true });
		</script>	

			
	</div>
</div>


<?php //include("./footer.php"); ?>
