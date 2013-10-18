<html>
<head>
<Title>Registration Form</Title>
<meta charset="UTF-8"/>
<link rel="stylesheet" href="../css/default.css"/>
</head>
<body>
<h1>Register here!</h1>
<p>Fill in your name and email address, then click <strong>Submit</strong> to register.</p>
<form method="post" action="index.php" enctype="multipart/form-data" >
  <fieldset>
    <legend> Registration Form </legend>
      <span>Name:</span><input type="text" name="name" id="name"/></br>
      <span>Email:</span><input type="text" name="email" id="email"/></br>
      <span>Company:</span><input type="text" name="company" id="company"/></br>
      <input type="submit" name="submit" value="Submit" />
  </fieldset>
</form>
<?php
    // DB connection info
    //using the values you retrieved earlier from the portal.
    $host = "eu-cdbr-azure-west-b.cloudapp.net";
    $user = "b9e0d9bc5e8435";
    $pwd = "ef5d18ba";
    $db = "COMP2013cw2";
    // Connect to database.
    try {
        $conn = new PDO( "mysql:host=$host;dbname=$db;", $user, $pwd);
        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }
    catch(Exception $e){
        die(var_dump($e));
    }
    // Insert registration info
    if(!empty($_POST)) {
    try {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $company = $_POST['company'];
        $date = date("Y-m-d");
        // Insert data
        $sql_insert = "INSERT INTO registration_tbl (name, email, date, companyName) 
                   VALUES (?,?,?,?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $email);
        $stmt->bindValue(3, $date);
        $stmt->bindValue(4, $company);
        $stmt->execute();
    }
    catch(Exception $e) {
        die(var_dump($e));
    }
    echo "<h3>Your're registered!</h3>";
    }
    // Retrieve data
    $sql_select = "SELECT * FROM registration_tbl";
    $stmt = $conn->query($sql_select);
    $registrants = $stmt->fetchAll(); 
    if(count($registrants) > 0) {
        echo "
<h2>People who are registered
  <a class='search_button' href='search.php'> [-Search-]
  </a>:
</h2>";
        echo "<table>";
        echo "<tr><th>Name</th>";
        echo "<th>Email</th>";
        echo "<th>Date</th></tr>";
        foreach($registrants as $registrant) {
            echo "<tr><td>".$registrant['name']."</td>";
            echo "<td>".$registrant['email']."</td>";
            echo "<td>".$registrant['date']."</td>";
            echo "<td>".$registrant['companyName']."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<h3>No one is currently registered.</h3>";
    }
?>
</body>
</html>
