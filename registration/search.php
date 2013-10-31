<html>
<head>
<Title>Search Database</Title>
<meta charset="UTF-8"/>
<link rel="stylesheet" href="../css/default.css"/>
</head>
<body>
<h1>Search</h1>
<a class="search_button" href="index.php">[-Back-]</a>
<p>Fill in name, email address or company name then click <strong>Search</strong> to search.</p>
<form method="post" action="search.php" enctype="multipart/form-data" >
  <fieldset>
    <legend> Search </legend>
      <span class='label'>Name:</span>
      <input type="text" name="name" id="name" <?php if(isset($_POST['name'])) echo "value='".$_POST['name']."'"; ?>/></br>
      <span class='label'>Email:</span>
      <input type="text" name="email" id="email" <?php if(isset($_POST['email'])) echo "value='".$_POST['email']."'"; ?>/></br>
      <span class='label'>Company:</span>
      <input type="text" name="company" id="company" <?php if(isset($_POST['company'])) echo "value='".$_POST['company']."'"; ?>/></br>
      <fieldset>
	<legend>Date Filter:</legend>
	<span class='label'>Date:</span>
      	<input type="text" name="date" <?php 
	       if (isset($_POST['date'])) 
	       	  echo "value='".$_POST['date']."'"; 
	       else 
	       	    echo "value='DD/MM/YYYY'";
	?> /></br>
      	<span class='label'>Before</span>
	<input type="radio" name="date_mode" value="before" <?php if($_POST['date_mode'] == "before") echo "checked"; ?>/>
	</br>
      	<span class='label'>After</span>
	<input type="radio" name="date_mode" value="after" <?php if($_POST['date_mode'] == "after") echo "checked"; ?>/>
      	</br>
      </fieldset>
      <input type="submit" name="submit" value="Search" />
  </fieldset>
</form>
<form action="search.php" method="post" enctype="multipart/form-data">
<!-- hiddden inputs to redisplay search after deletion -->
<input type="hidden" name="name" value="<?php echo $_POST['name']; ?>"/>
<input type="hidden" name="email" value="<?php echo $_POST['email']; ?>"/>
<input type="hidden" name="company" value="<?php echo $_POST['company']; ?>"/>
<input type="hidden" name="date" value="<?php echo $_POST['date']; ?>"/>
<input type="hidden" name="date_mode" value="<?php echo $_POST['date_mode']; ?>"/>

<fieldset>
<legend>Results</legend>
<table>
<thead>
<tr>
	<th>Select</th>
	<th>Name</th>
	<th>Email</th>
	<th>Date</th>
	<th>Company</th>
</tr>
</thead>
<tbody>
<?php
    // DB connection info
    //using the values you retrieved earlier from the portal.
    $host = "eu-cdbr-azure-west-b.cloudapp.net";
    $user = "b9e0d9bc5e8435";
    $pwd = "ef5d18ba";
    $db = "COMP2013cw2";
    //Connect to database.
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db;", $user, $pwd);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(Exception $e){
        die(var_dump($e));
    }
    //perform deletions
    
    $delete_ids = array();
    $delete_query = "DELETE FROM registration_tbl WHERE";
    foreach($_POST as $name => $value) {
    	if (is_numeric($name) && $value == "y") {
	   $delete_ids[] =  $name;
	   $delete_query .= " id = ? OR";
	}
    }
    if (count($delete_ids) > 0) {
       if (substr($delete_query, -3) == " OR") 
       	  $delete_query = substr($delete_query, 0, 
	  		  	count($delete_query) - 3);
       $stmt = $conn->prepare($delete_query);
       $stmt->execute($delete_ids);
    }
    // Retrieve data
    if (!empty($_POST)) {
       $sql_select = "SELECT * FROM registration_tbl";
       $sql_select_condition = "";
       $values = array();
       if ($_POST['name']) {
       	  $sql_select_condition .= " name LIKE '%:name%' AND";
	  $values[':name'] = $_POST['name'];
       }
       if ($_POST['email']) {
       	  $sql_select_condition .= " email = :email AND";
	  $values[':email'] = $_POST['email'];
       }
       if ($_POST['company']) {
       	  $sql_select_condition .= " companyName = :company AND";
	  $values[':company'] = $_POST['company'];
       }
       if ($_POST['date'] != "DD/MM/YYYY") {
       	  if ($_POST['date_mode'] = "before") {
	     $sql_select_condition .= " date <= :date";
	  }
	  else {
	       $sql_select_condition .= " date >= :date";
	  }
       	  $y = substr($_POST['date'], 6);
	  $m = substr($_POST['date'], 3, 4);
	  $d = substr($_POST['date'], 0, 1);
       	  $values[':date'] = $y.$m.$d;
	  
       }
       if(count($values) > 0) {
         //remove trailing AND if there is one
         if (substr($sql_select_condition, -4) == " AND") 
       	    $sql_select_condition = substr($sql_select_condition, 0, 
	    			  strlen($sql_select_condition) - 4);
	 $sql_select .= " WHERE".$sql_select_condition;
       }
       //exec query
       $stmt = $conn->prepare($sql_select); 
       $stmt->execute($values);
       //parse output
       $people = $stmt->fetchAll(); 
       if(count($people) > 0) { 
         foreach($people as $person) {
            echo "<tr><td>";
	    echo "<input type='checkbox' name='".$person['id']."' value='y'/>";
	    echo "</td>";
	    echo "<td>".$person['name']."</td>";
            echo "<td>".$person['email']."</td>";
            echo "<td>".$person['date']."</td>";
            echo "<td>".$person['companyName']."</td></tr>";
         }
      }
    }
?>
</tbody>
<tfoot>
<!-- actions that can be performed on the list -->
<tr class="menu">
<td><input type="submit" name="submit" value="Delete"/>
</td>
</tfoot>
</table>
</fieldset>
</form>
</body>
</html>
