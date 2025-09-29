<?php
session_start(); 
include("connection.php");
if ($_SESSION['username'] != 'ADMIN' || $_SESSION['email'] != 'ADMIN@ADMIN.COM' || $_SESSION['password'] != 'ADMIN') {     
    header("Location: login.php");     
    exit; 
}

$idcom = connexpdo("pc");
if ($idcom)
{
    $res = "SELECT * FROM parts";
    $stmt = $idcom->prepare($res);
    $stmt->execute();

    if ($stmt->rowCount() > 0)
    {
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else
    {
        $rows = [];
    }

}
else
{
    $rows = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modify_product']) && !empty($_POST['id_part']))
{
    $id_part = (int) $_POST['id_part'];

    $_SESSION['id_part'] = $id_part;
    header('location: modify.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product']) && !empty($_POST['id_part']))
{
    $id_part = (int) $_POST['id_part'];

    $req = 'DELETE FROM parts WHERE (id_part = :id_part)';
    $stmt = $idcom->prepare($req);
    $stmt->execute([
        ':id_part' => $id_part
    ]);
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
            <button type="submit" class="btn btn-warning" disabled>Update</button>
        </form>

        <form method="GET" action="admin.php" class="me-2">
            <button type="submit" class="btn btn-primary">Add</button>
        </form>

        <form method="GET" action="view.php">
            <button type="submit" class="btn btn-success">View</button>
        </form>
    </div>
<div id="parts-container" class="mt-4 d-flex flex-wrap" style="gap: 15px;">
    <?php if (!empty($rows)): ?>
        <?php foreach($rows as $row): ?>
            <div class="card" style="width: calc(33.33% - 10px); min-height: 450px;">
                    <div style="height: 200px; overflow: hidden; background-color: #f8f9fa;">
                        <img src="./uploads/<?php echo $row['image'] ?>" 
                             alt="<?php echo htmlspecialchars($row['product']); ?>" 
                             style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                <div class="card-body">
                    Product: <?php echo $row['product']; ?><br>
                    Quantity: <?php echo $row['quantity']; ?><br>
                    Price: <?php echo $row['price']; ?>$<br>
                
                    <form method="POST">
                        <input type="hidden" name="id_part" value="<?php echo ($row['id_part']); ?>">
                        <button type="submit" name="modify_product">Modify</button>
                    </form>
                    <br>
                    <form method="POST">
                        <input type="hidden" name="id_part" value="<?php echo $row['id_part']; ?>">
                        <button type="submit" name="delete_product">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
    </div>
</body>
</html>