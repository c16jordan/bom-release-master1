<?php
  //Left the same as scanner_releases, looks like it is still on the same page this way
  $nav_selected = "SCANNER"; 
  $left_buttons = "YES"; 
  $left_selected = "RELEASESLIST"; 

  include("./nav.php");
  global $db;

  ?>


<div class="right-content">
    <div class="container">

      <h3 style = "color: #01B0F1;">Scanner -> Releases Bom</h3>

		<?php echo $_SESSION['name']; ?>

	</div>
</div>

<style>
   tfoot {
     display: table-header-group;
   }
</style>

  <?php include("./footer.php"); ?>