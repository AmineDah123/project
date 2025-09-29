<?php 
session_start(); 
if ($_SESSION['username'] != 'ADMIN' || $_SESSION['email'] != 'ADMIN@ADMIN.COM' || $_SESSION['password'] != 'ADMIN') {     
    header("Location: login.php");     
    exit; 
} 

include("connection.php"); 
$idcom = connexpdo('pc'); 
if (!$idcom) {     
    exit(); 
}  

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {     
    if (!empty($_POST['product']) && !empty($_POST['price']) && !empty($_POST['category'])         
        && !empty($_POST['quantity']) && isset($_FILES['image'])         
        && $_FILES['image']['error'] == UPLOAD_ERR_OK )     
    {         
        $product = $_POST['product'];         
        $quantity = $_POST['quantity'];         
        $price = $_POST['price'];         
        $category = $_POST['category'];          

        $imageName = $_FILES['image']['name'];         
        $tmpName = $_FILES['image']['tmp_name'];          

        move_uploaded_file($tmpName,"uploads/" . basename($imageName));          

        $req = 'INSERT INTO parts(product,quantity,category,price,image) VALUES (:product,:quantity,:category,:price,:img)';         
        $stmt = $idcom->prepare($req);         
        $stmt->execute([             
            ':product' => $product,             
            ':quantity' => $quantity,             
            ':category' => $category,             
            ':price' =>  $price,             
            ':img' => $imageName         
        ]);      
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

        <form method="GET" action="add.php" class="me-2">
            <button type="submit" class="btn btn-primary" disabled>Add</button>
        </form>

        <form method="GET" action="view.php">
            <button type="submit" class="btn btn-success">View</button>
        </form>
    </div>

    <!-- Add Product Form -->
    <form method="POST" enctype="multipart/form-data" class="container mt-5 p-4 border rounded bg-light shadow-sm">   
        <h3 class="mb-4 text-center">Add New Product</h3>    

        <div class="mb-3">     
            <label for="product" class="form-label">Product</label>     
            <input type="text" class="form-control" id="product" name="product" placeholder="Enter product name" required>   
        </div>    

        <div class="mb-3">     
            <label for="quantity" class="form-label">Quantity</label>     
            <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity" required>   
        </div>    

        <div class="mb-3">   
            <label for="category" class="form-label">Category</label>     
            <select class="form-select" id="category" name="category" required>         
                <option value="" disabled selected>Select category</option>         
                <option value="Desktop">Desktop</option>         
                <option value="Laptop">Laptop</option>         
                <option value="Peripherals">Peripherals</option>     
            </select>     
        </div>     

        <div class="mb-3">     
            <label for="price" class="form-label">Price</label>     
            <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Enter price" required>   
        </div>    

        <div class="mb-3">     
            <label for="image" class="form-label">Image</label>     
            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>   
        </div>    

        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button> 
    </form>  

</body> 
</html>
