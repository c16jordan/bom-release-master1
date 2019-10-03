<?php
  $nav_selected = "SETUP";
  $left_buttons = "NO";
  $left_selected = "";

  include("./nav.php");
  global $db;

  /*http://form.guide/php-form/php-form-action-self.html
  <?php echo htmlentities($_SERVER['PHP_SELF']);?>*/
 ?>
 
<html>
<body onload="update_form()">

 <div class="right-content">
    <div class="container">

      <h3 style = "color: #01B0F1;">Configuration Options</h3>

    </div>
</div>

<div class = "form-submission">

	<form action = "<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method = "POST">
	
	<h4>Select Date Option</h4>
	<select name = "start_option">
	<option id="start_opt" value = "open_date" selected>Default</option>
	<option value = "open_date"> Open Date</option>
	<option value = "dependency_date"> Dependency Date</option>
	</select><br>
	
	<h4>Start Date</h4>
	<input id="s_date" type = "date" name = "start_date" value = "1970-01-01"><br>
	
	<h4>End Date</h4>
	<input id="e_date" type = "date" name = "end_date" value = "2070-01-01"><br>
	
	<h4>Select Release Type</h4>
	<select name = "type_option">
	<option id="type_def" value = "All" selected>Default</option>
	<option value = "All">All</option>
	<option value = "Async">Async</option>
	<option value = "Major">Major</option>
	<option value = "Minor">Minor</option>
	<option value = "Patch">Patch</option>
	</select><br>
	
	<h4>Select Release Status</h4>
	<select name = "status_option">
	<option id="stat_def" value = "All" selected>Default</option>
	<option value = "All">All</option>
	<option value = "Active">Active</option>
	<option value = "Completed">Completed</option>
	<option value = "Draft">Draft</option>
	<option value = "Released">Released</option>
	</select><br><br>
	
	<input type = "Submit" name="submit" value = "Submit">
	</form>
	
	
</div> 

</body>
</html>

<?php
	// Store form values in $_SESSION variables on submission

			if(isset($_POST['submit'])){
				$_SESSION['submit']		   = $_POST['submit'];
				$_SESSION['start_option']  = $_POST['start_option'];
				$_SESSION['start_date']    = $_POST['start_date'];
				$_SESSION['end_date']  	   = $_POST['end_date'];
				$_SESSION['type_option']   = $_POST['type_option'];
				$_SESSION['status_option'] = $_POST['status_option'];
			}

?>

<script>
	
	function update_form(){		
		
		var flag = '<?php echo isset($_SESSION['submit'])? "true" : "false"; ?>' ;
		
		if(flag=="true"){
			document.getElementById("start_opt").value  	= '<?php echo $_SESSION['start_option']; ?>'; 
			document.getElementById("start_opt").innerHTML  = switch_param('<?php echo $_SESSION['start_option']; ?>');
			
			document.getElementById("s_date").value 	 = '<?php echo $_SESSION['start_date']; ?>'; 	
			document.getElementById("s_date").innerHTML  = '<?php echo $_SESSION['start_date']; ?>';
			
			document.getElementById("e_date").value 	 = '<?php echo $_SESSION['end_date']; ?>'; 	  	   
			document.getElementById("e_date").innerHTML  = '<?php echo $_SESSION['end_date']; ?>';
			
			document.getElementById("type_def").value 	 = '<?php echo $_SESSION['type_option']; ?>'; 	   
			document.getElementById("type_def").innerHTML  = '<?php echo $_SESSION['type_option']; ?>';
			
			document.getElementById("stat_def").value 	=  '<?php echo $_SESSION['status_option']; ?>';	 
			document.getElementById("stat_def").innerHTML  = '<?php echo $_SESSION['status_option']; ?>';
		}
	
}

// Function to convert open_date/dependency_date into better looking strings for the form input id="start_opt"

		function switch_param(input){
	
			var vahr = input;
	
			if(vahr == "dependency_date"){
				vahr = "Dependency Date";
			}
			else if(vahr == "open_date"){
				vahr = "Open Date";
			}

			return vahr;
		}
	
</script>

<?php include("./footer.php"); ?>
