<?php 
session_start();
include('connection.php');
if (empty($_SESSION['username']) || empty($_SESSION['email']) || empty($_SESSION['password'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['username'] == 'ADMIN' || $_SESSION['email'] == 'ADMIN@ADMIN.COM'){     
    header("Location: logout.php");     
    exit; 
} 


$idcom = connexpdo('pc');
if ($idcom)
{
    $req = "SELECT id_user from users WHERE (email = :email);";
    $stmt = $idcom->prepare($req);
    $stmt->execute([
        ':email' => $_SESSION['email']
    ]);

    if ($stmt->rowCount() > 0)
    {
        $id = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_user = $id['id_user'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart']) && !empty($_POST['id_part']))
{
    $id_part = (int)$_POST['id_part'];
    if ($idcom)
    {
        $stmt = $idcom->prepare("SELECT id_part FROM parts WHERE id_part = ?");
        $stmt->execute([$id_part]);

        if ($stmt->rowCount() > 0)
        {
            $req = 'SELECT * FROM cart WHERE (id_part = :id_part AND id_user = :id_user)';
            $stmt = $idcom->prepare($req);
            
            $stmt->execute([
                ':id_part' => $id_part,
                ':id_user' => $id_user
            ]);
            if ($stmt->rowCount() > 0)
            {

                //Add a quantity checker so the user can't add more than the stock
                $req = 'SELECT quantity FROM parts WHERE (id_part = :id_part)';
                $stmt_quant = $idcom->prepare($req);

                $stmt_quant->execute([
                    ':id_part' => $id_part
                ]);
                if ($stmt_quant->rowCount() > 0)
                {
                    $q = $stmt_quant->fetch(PDO::FETCH_ASSOC);
                }

                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $quant = $row['quantity'] + 1;

                if ($quant <= $q['quantity'])
                {
                    $req = 'UPDATE cart SET quantity = :quantity WHERE (id_user = :id_user AND id_part = :id_part)';
                    $stmt = $idcom->prepare($req);

                    $stmt->execute([
                        ':quantity' => $quant,
                        ':id_user' => $id_user,
                        ':id_part' => $id_part
                    ]);
                }
                else
                {
                    echo "<br>You have selected the maximum amount available!";
                }

                
            }
            else
            {
                $req = 'SELECT * FROM parts WHERE( id_part = :id_part)';
                $stmt = $idcom->prepare($req);

                $stmt->execute([
                    ':id_part' => $id_part
                ]);

                if ($stmt->rowCount() > 0)
                {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                }

                $req = 'INSERT INTO cart(id_user,id_part,quantity) VALUES(:id_user,:id_part,:quantity)';
                $stmt = $idcom->prepare($req);

                $quantity = 1;

                $stmt->execute([
                    ':id_user' => $id_user,
                    ':id_part' => $id_part,
                    ':quantity' => $quantity,
                ]);

            }

        }        
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($idcom)
{
    $req = "SELECT * FROM parts";
    $stmt = $idcom->prepare($req);
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


//ADD LOGIC FOR PURCHASE BUTTON (ON CLICK -> UPDATE PART TABLE FOR EACH ELEMENT -> THEN DELETE CART FOR THAT USER )
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['purchase_cart']))
{
    $req = 'SELECT * FROM cart WHERE (id_user = :id_user)';
    $cart_find = $idcom->prepare($req);
    $cart_find->execute([
        ':id_user' => $id_user
    ]);

    if ($cart_find->rowCount() > 0)
    {
        $cart_row = $cart_find->fetchAll(PDO::FETCH_ASSOC);


        foreach($cart_row as $c)
        {
            $req = 'SELECT quantity FROM parts WHERE (id_part = :id_part)';
            $for_quantity = $idcom->prepare($req);
            $for_quantity->execute([
                ':id_part' => $c['id_part']
            ]);

            if ($for_quantity->rowCount() > 0)
            {
                $q = $for_quantity->fetch(PDO::FETCH_ASSOC);
                $quantity = $q['quantity'];
            }

            $total = $quantity - $c['quantity'];

            $req = 'UPDATE parts SET quantity = :quantity WHERE (id_part = :id_part)';
            $part_to_update = $idcom->prepare($req);
            $part_to_update->execute([
                ':quantity' => $total,
                ':id_part' => $c['id_part'] 
            ]);


            $req = 'DELETE FROM cart WHERE (id_part = :id_part)';
            $cart_to_delete = $idcom->prepare($req);
            $cart_to_delete->execute([
                ':id_part' => $c['id_part']
            ]);

            $req = 'INSERT INTO purchase(id_user,id_part,quantity) VALUES (:id_user,:id_part,:quantity)';
            $add_purchase = $idcom->prepare($req);
            $add_purchase->execute([
                ':id_user' => $id_user,
                ':id_part' => $c['id_part'],
                ':quantity' => $c['quantity']
            ]);


        }

    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;

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

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Cart Box on Left -->
        <div class="col-3">
            <div class="border p-3" style="height: 80vh; min-height: 400px;">
                <h4 class="text-center">My Cart</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $req = 'SELECT p.product AS product, c.quantity AS quantity, p.price AS price 
                        FROM cart c
                        JOIN parts p ON p.id_part = c.id_part
                        WHERE c.id_user = :id_user';
    
                        $stmt = $idcom->prepare($req);
                        $stmt->execute([':id_user' => $id_user]);
                        if ($stmt->rowCount() > 0)
                        {
                            $cart_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach($cart_rows as $row)
                            {
                                $price = $row['price'] * $row['quantity'];
                                ?><tr>
                                    <td><?php echo $row['product'];?></td>
                                    <td><?php echo $row['quantity'];?></td>
                                    <td><?php echo $price ?></td>
                                </tr><?php
                            }
                        }
                        ?>

                    </tbody>
                </table>
                <form method="POST">
                    <button type="submit" name="purchase_cart">Purchase</button>
                </form>
            </div>
        </div>

        <!-- Right content -->
        <div class="col-9">
    <div class="d-flex justify-content-center mt-3">
        <form class="border p-3 rounded text-center" method="POST">
            <!-- Radio buttons on the same line -->
            <label class="me-2">All:</label>
            <input type="radio" name="category" value="All" class="me-3" >
            
            <label class="me-2">Desktop:</label>
            <input type="radio" name="category" value="Desktop" class="me-3">
            
            <label class="me-2">Laptop:</label>
            <input type="radio" name="category" value="Laptop" class="me-3">
            
            <label class="me-2">Peripherals:</label>
            <input type="radio" name="category" value="Peripherals">

            <div class="mt-3">
                <button type="submit" class="btn btn-secondary">Confirm</button>
            </div>
        </form>
    </div>

    <?php  
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if (isset($_POST['category']) && $_POST['category'] != 'All' )
        {
            $category = $_POST['category'];
            if ($idcom)
            {
                $req = "SELECT * FROM parts WHERE (category = :category)";
                $stmt = $idcom->prepare($req);
                $stmt->execute([
                    ':category' => $category
                ]);

                if ($stmt->rowCount() > 0)
                {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                else
                {
                    $rows = [];
                }
            } 
        }
    }
    ?>
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
                    <?php if ($row['quantity'] > 0)
                    {
                    ?><form method="POST">
                        <input type="hidden" name="id_part" value="<?php echo ($row['id_part']); ?>">
                        <button type="submit" name="add_to_cart">Add To Cart</button>
                    </form><?php
                    }   
                    else   
                    {
                        ?>  
                        <button disabled>Out of stock</button>
                        <?php
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
    </div>
</div>
</body>
</html>