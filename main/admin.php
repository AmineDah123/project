<?php
session_start();
if ($_SESSION['username'] != 'ADMIN' || $_SESSION['email'] != 'ADMIN@ADMIN.COM' || $_SESSION['password'] != 'ADMIN')
{
    header("Location: login.php");
    exit;
}
include("connection.php");


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>PC PARTS</title>
</head>
<body>
<ul class="nav p-2" style="background-color: grey;">
  <li class="nav-item">
    <a class="nav-link active text-white" aria-current="page" href="#">PC PARTS</a>
  </li>
  <?php if (isset($_SESSION['username']) && isset($_SESSION['email']) && isset($_SESSION['password'])) {?>         
    <li class="nav-item ms-auto">
        <a class="nav-link active text-white" aria-current="page" href="#"><?php echo $_SESSION['email'];?></a>
    </li>
    <li class="nav-item">
        <a class="btn btn-outline-light" href="./logout.php" >Logout</a>
    </li>    
  <?php } ?>
</ul>


    <form method="POST">
        <button type="submit" name="insert_product">Insert</button>
    </form>
    <form method="POST">
        <button type="submit" name="update_product">Update</button>
    </form>


</body>
</html>