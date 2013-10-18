<html>
<head>
<Title>Search Database</Title>
<meta charset="UTF-8"/>
<link rel="stylesheet" href="../css/default.css"/>
</head>
<body>
<h1>Search</h1>
<p>Fill in name, email address or company name then click <strong>Search</strong> to search.</p>
<form method="post" action="search.php" enctype="multipart/form-data" >
  <fieldset>
    <legend> Search </legend>
      <span>Name:</span>
      <input type="text" name="name" id="name" <?php if(isset($_POST['name'])) echo "value='".$_POST['name']."'"; ?>/></br>
      <span>Email:</span>
      <input type="text" name="email" id="email" <?php if(isset($_POST['email'])) echo "value='".$_POST['email']."'"; ?>/></br>
      <span>Company:</span>
      <input type="text" name="company" id="company" <?php if(isset($_POST['company'])) echo "value='".$_POST['company']."'"; ?>/></br>
      <input type="submit" name="submit" value="Search" />
      
  </fieldset>
</form>
<form action="search.php" method="post" enctype="multipart/form-data">
<!-- hiddden inputs to redisplay search after deletion -->
<input type="hidden" name="name" value="
<?php echo $_POST['name']; ?>
"/>
<input type="hidden" name="email" value="
<?php echo $_POST['email']; ?>
"/>
<input type="hidden" name="company" value="
<?php echo $_POST['company']; ?>
"/>
<input type="hidden" name="date_start" value="
<?php echo $_POST['date_start']; ?>
"/>
<input type="hidden" name="date_end" value="
<?php echo $_POST['date_end']; ?>
"/>

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
       echo "|".$delete_query."|";
       $stmt = $conn->prepare($delete_query);
       $stmt->execute($delete_ids);
    }
    // Retrieve data
    if (!empty($_POST)) {
       $sql_select = "SELECT * FROM registration_tbl WHERE";
       $values = array();
       if ($_POST['name']) {
       	  $sql_select .= " name = :name AND";
	  $values[':name'] = $_POST['name'];
       }
       if ($_POST['email']) {
       	  $sql_select .= " email = :email AND";
	  $values[':email'] = $_POST['email'];
       }
       if ($_POST['company']) {
       	  $sql_select .= " companyName = :company";
	  $values[':company'] = $_POST['company'];
       }
       //remove trailing AND if there is one
       if (substr($sql_select, -4) == " AND") 
       	  $sql_select = substr($sql_select, 0, count($sql_select) - 4);
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
<td><input type="image" src="<?php echo "http://".$_SERVER['SERVER_NAME']."/icons/delete.png"; ?>" alt="Delete"/></li>
</td>
</tfoot>
</table>
</fieldset>
</form>
</body>
</html>