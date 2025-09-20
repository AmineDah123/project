<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC PARTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
<ul class="nav p-2" style="background-color: grey;">
  <li class="nav-item">
    <a class="nav-link active text-white" aria-current="page" href="#">PC PARTS</a>
  </li>
  <li class="nav-item ms-auto">
    <a class="btn btn-primary me-2" href="#">Sign Up</a>
  </li>
  <li class="nav-item">
    <a class="btn btn-outline-light" href="/project/main/login.php">Login</a>
  </li>
</ul>

<!-- Sign Up Form -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
  <div class="card shadow p-4" style="width: 350px;">
    <h3 class="text-center mb-4">Sign Up</h3>
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]  ?>">
      <div class="mb-3">
        <label class="form-label">Username :</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email :</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password :</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Sign Up</button>
    </form>

    <!-- Login helper text -->
    <p class="text-center mt-3">
      Already signed up? 
      <a href="#" class="text-decoration-none">Login here</a>
    </p>
  </div>
</div>
</body>
</html>



<?php
include("connection.php");
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password']))
    {
        $user = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $idcom = connexpdo('pc');
        if ($idcom)
        { 
            try{
                $req = "INSERT INTO users(username,email,password) 
                VALUES ('$user','$email','$password')";

                $response = $idcom->exec($req);

                if ($response)
                {
                
                    $_SESSION["username"] = $user;
                    $_SESSION["email"] = $email;
                    $_SESSION["password"] = $password;
                    $user = '';
                    $password = '';
                    $email = '';
                    header("Location: index.php");
                    exit;
                }
            }  
            catch( PDOException $e)
            {
                if ($e->getCode() == 23000)
                {
                     echo "<p style='color:red; text-align:center;'>Username or email already exists. Try another one!</p>";
                } else {
                     echo "<p style='color:red; text-align:center;'>Database error: " . $e->getMessage() . "</p>";
                }
            }
        }
        else{
            echo "<p style='color:red; text-align:center;'>Fatal Error In Database! Contact Support Immediatly! </p>";
        }
    }
    else
    {
        echo "<p style='color:red; text-align:center;'>Empty Fields! Be Sure To Fill All Required Fields!</p>";
    }
}
?>