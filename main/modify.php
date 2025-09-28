<?php
session_start(); 
if ($_SESSION['username'] != 'ADMIN' || $_SESSION['email'] != 'ADMIN@ADMIN.COM' 
|| $_SESSION['password'] != 'ADMIN'  || empty($_SESSION['id_part'])) {     
    header("Location: login.php");     
    exit; 
} 

include("connection.php");

$idcom = connexpdo("pc");
$row = null;

if ($idcom) {
    $req = "SELECT * FROM parts WHERE (id_part = :id_part)";
    $stmt = $idcom->prepare($req);
    $stmt->execute([
        ':id_part' => $_SESSION['id_part']
    ]);
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if ((!empty($_POST['product'])) && (!empty($_POST['quantity'])) && (!empty($_POST['price'])) 
    && (!empty($_POST['category'])))
    {
        $product = $_POST['product'];
        $quantity = $_POST['quantity'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $imageName = $row['image'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK)
        {
            $imageName = $_FILES['image']['name'];
            $tmpName = $_FILES['image']['tmp_name'];
            move_uploaded_file($tmpName, "uploads/". basename($imageName));
    }
        $req = "UPDATE parts 
                SET product = :product,
                    quantity = :quantity,
                    category = :category,
                    price = :price,
                    image = :img
                WHERE id_part = :id_part";
        $stmt = $idcom->prepare($req);
        $stmt->execute([
            ':product' => $product,
            ':quantity' => $quantity,
            ':category' => $category,
            ':price' => $price,
            ':img' => $imageName,
            ':id_part' => $_SESSION['id_part']            
        ]);

        header("Location: update.php");
        exit();
    }


}



?>


<!DOCTYPE html> 
<html lang="en"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">     
    <title>Edit Product</title> 
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

    <!-- Edit Product Form -->
    <div class="container mt-5 p-4 border rounded bg-light shadow-sm">
        <h3 class="mb-4 text-center">Edit Product</h3>

        <?php if ($row) { ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="product" class="form-label">Product</label>
                <input type="text" class="form-control" id="product" name="product" value="<?php echo htmlspecialchars($row['product']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="Desktop" <?php if($row['category']=="Desktop") echo "selected"; ?>>Desktop</option>
                    <option value="Laptop" <?php if($row['category']=="Laptop") echo "selected"; ?>>Laptop</option>
                    <option value="Peripherals" <?php if($row['category']=="Peripherals") echo "selected"; ?>>Peripherals</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Image</label><br>
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" class="img-thumbnail mb-2" style="max-width:150px;">
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>

            <button type="submit" name="save_changes" class="btn btn-success">Save Changes</button>
        </form>
        <?php } else { ?>
            <p class="text-center text-danger">No product found to edit.</p>
        <?php } ?>
    </div>
</body>
</html>
