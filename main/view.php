<?php
session_start(); 
include("connection.php");
if ($_SESSION['username'] != 'ADMIN' || $_SESSION['email'] != 'ADMIN@ADMIN.COM' || $_SESSION['password'] != 'ADMIN') {     
    header("Location: login.php");     
    exit; 
}

$idcom = connexpdo('pc');
if ($idcom)
{
    $req = 'SELECT * FROM users';
    $users_total = $idcom->prepare($req);
    $users_total->execute();

    if ($users_total->rowCount() > 0)
    {
        $users = $users_total->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        $users = [];
    }

    $req = 'SELECT * FROM purchase';
    $products_total = $idcom->prepare($req);
    $products_total->execute();

    if ($products_total->rowCount() > 0)
    {
        $products = $products_total->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        $products = [];
    }

    $req = 'SELECT AVG(quantity) FROM purchase';
    $products_average = $idcom->prepare($req);
    $products_average->execute();

    if ($products_average->rowCount() > 0)
    {
        $average = $products_average->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        $average = [];
    }
}
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
    <!-- Navbar -->
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

    <!-- Buttons Bar -->
    <div class="d-flex justify-content-center mt-4">
        <form method="GET" action="update.php" class="me-2">
            <button type="submit" class="btn btn-warning">Update</button>
        </form>

        <form method="GET" action="admin.php" class="me-2">
            <button type="submit" class="btn btn-primary">Add</button>
        </form>

        <form method="GET" action="view.php">
            <button type="submit" class="btn btn-success" disabled>View</button>
        </form>
    </div>
        <!-- Analytics Section -->
    <div class="container mt-5">
        <div class="row text-center">
            
            <!-- Total Users -->
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="display-6 fw-bold text-primary">
                            <?php echo count($users); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Total Purchases -->
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Total Purchases</h5>
                        <p class="display-6 fw-bold text-success">
                            <?php echo count($products); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Average Quantity per Purchase -->
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Average Quantity</h5>
                        <p class="display-6 fw-bold text-warning">
                            <?php 
                                if (!empty($average)) {
                                    echo number_format($average[0]["AVG(quantity)"], 2);
                                } else {
                                    echo "0";
                                }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
