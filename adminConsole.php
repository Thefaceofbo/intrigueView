<html>
<head>
  <?php 
    include('dbConnect.php');
    include('objectConstruction.php');
    session_start();
   ?>
</head>
<body>
  <?php 
  
  echo "hey there, nice login";
  var_dump($_SESSION['user']);
  
  
  ?>
</body>
</html>
