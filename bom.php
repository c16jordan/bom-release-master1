<?php
  //Left the same as scanner_releases, looks like it is still on the same page this way
  $nav_selected = "SCANNER"; 
  $left_buttons = "YES"; 
  $left_selected = "RELEASESLIST"; 

  include("./nav.php");
  include("sbomtreePhp.php");
  global $db;

  ?>

 <link rel="stylesheet" href="css/screen.css" media="screen" />
 <link rel="stylesheet" href="css/jquery.treetable.css" />
 <link rel="stylesheet" href="css/jquery.treetable.theme.default.css" />
 <link rel="stylesheet" type="text/css" href="mycss.css">
 <script src="jquery-3.4.1.js"></script>
 <script src="sbomtreeJs.js"></script>
  
<div class="right-content">
    <div class="container">

      <h3 style = "color: #01B0F1;">Scanner -> Releases Bom</h3>
		
		<table id="sbom_tree">
 
	<div>
	
		<caption>
			<button id="expand" style="font-size: 10px">Expand All</button>
			<button id="collapse" style="font-size: 10px">Collapse All</button>
			<button id="colorize_r" style="font-size: 10px"> Toggle Color </button>
			<button id="reds_r" style="font-size: 10px">Reds</button>
			<button id="yellows_r" style="font-size: 10px">Yellows</button>
			<button id="red_yellow_r" style="font-size: 10px"> Reds and Yellows </button>
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
		<?php 	
			
			// Check for root node name - app name only first
			$sql = "SELECT * FROM sbom WHERE app_name ='".$_SESSION['name']."'"; 
			//$sql = "SELECT * FROM sbom WHERE app_name ='Quizmaster'"; 
			$returned = phpMakeTree($db, $ry=false, $y=false, $sql);
			
			if($returned === false){
				
				//Check for child node - app_name + app_version
				$sql = "SELECT * FROM sbom WHERE CONCAT(app_name,' ',app_version) ='".$_SESSION['name']."'"; 
				$returned = phpMakeTree($db, $ry=false, $y=true, $sql);
					if($returned === false){
						echo "No results found.";
					}
			}
			
		?>
	</tbody>

</table>	
		
	</div>
</div>

<script src="jquery.treetable.js"></script>	

<script>
			$("#sbom_tree").treetable({ expandable: true, initialState: "expanded" });

</script>	

<style>
   tfoot {
     display: table-header-group;
   }
</style>

  <?php include("./footer.php"); ?>