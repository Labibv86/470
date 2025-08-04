<?php
session_start();
include("database.blade.php");

$userid = $_SESSION['userid'];
$dateNow = date("Y-m-d");
$returnDate = date("Y-m-d", strtotime("+7 days"));


$userQuery = "SELECT points FROM users WHERE userid = $userid";
$userResult = mysqli_query($connection, $userQuery);
$userData = mysqli_fetch_assoc($userResult);
$currentPoints = $userData['points'];

$cartQuery = "SELECT * FROM cart WHERE userID = $userid AND PaymentStatus=''";
$cartResult = mysqli_query($connection, $cartQuery);


$totalAmount = 0;
while ($cartRow = mysqli_fetch_assoc($cartResult)) {
    $totalAmount += $cartRow['TotalAmount'];
}

mysqli_data_seek($cartResult, 0);
$errorMessage = '';

if (isset($_POST['pay'])) {
    if ($currentPoints < $totalAmount || $currentPoints <= 0) {
        $errorMessage = "You don't have enough points to make the payment!";
    } else {
        while ($row = mysqli_fetch_assoc($cartResult)) {
            $itemID = $row['ItemID'];
            $shopID = $row['ShopID'];
            $amount = $row['TotalAmount'];

            mysqli_query($connection, "UPDATE users SET points = points - $amount WHERE userid = $userid");
            mysqli_query($connection, "UPDATE shops SET points = points + $amount WHERE ShopID = $shopID");
            mysqli_query($connection, "UPDATE rentalitems SET RenterID=$userid, RentDate='$dateNow', ReturnDate='$returnDate' WHERE ItemID = $itemID");
            mysqli_query($connection, "UPDATE items SET ItemUse='Rented' WHERE ItemSerial = $itemID");
            mysqli_query($connection, "UPDATE cart SET PaymentStatus='PaymentCleared' WHERE userID=$userid AND ItemID = $itemID");
        }

        $cartResult = mysqli_query($connection, $cartQuery);
        $totalAmount = 0;
    }
}

if (isset($_POST['clear'])) {
    mysqli_query($connection, "DELETE FROM cart WHERE userID = $userid AND PaymentStatus = ''");
    $cartResult = mysqli_query($connection, $cartQuery); // Refresh after clearing
    $totalAmount = 0;
}

if (isset($_POST['backtorental'])) {
    header('Location: rental.blade.php');
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Gidole&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <style>
        body { font-family: Quicksand; padding: 20px; }
        .cart-container { max-width: 800px; margin: auto; border: 4px solid ; padding: 20px; border-radius: 8px; }
        .item-box { border-bottom: 1px solid #ddd; padding: 10px 0; }
        .actions { margin-top: 20px; display: flex; gap: 20px; }
        button { width: 200px;
            height: 50px;
            border: solid;
            border-radius: 25px;
            cursor: pointer;
            border-color: grey;
            font-size: large; }
        .header{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 50px;
        }

        .backtorental{
            width: 200px;
            height: 50px;
            border: solid;
            border-radius: 25px;
            cursor: pointer;
            border-color: grey;
            font-size: large;
        }
    </style>
</head>
<body>

    <form action="cart.php" method="post">
        <div class="header">
                <button class="backtorental" name="backtorental">Back To Rental</button>
        </div>
        <div class="cart-container">

            <?php if (!empty($errorMessage)): ?>
                <h3 style="color: red;"><?= $errorMessage ?></h3>
            <?php endif; ?>



            <h2>Your Cart</h2>

            <?php while ($cart = mysqli_fetch_assoc($cartResult)): ?>
                <?php
                $itemID = $cart['ItemID'];
                $itemQuery = "SELECT * FROM items WHERE ItemSerial = $itemID";
                $itemResult = mysqli_query($connection, $itemQuery);
                $item = mysqli_fetch_assoc($itemResult);

                //*if ($cart['PaymentStatus'] !== 'PaymentCleared') {
                    //$totalAmount += $cart['TotalAmount'];
                //}


                        $query="SELECT * FROM users WHERE userid = $userid";
                        $result=mysqli_query($connection, $query);
                        $req=mysqli_fetch_assoc($result);
                        $points=$req['points']

                ?>
                <div class="item-box">
                    <p><strong>Item Name:</strong> <?= htmlspecialchars($item['ItemName']) ?></p>
                    <p><strong>Total Amount:</strong> <?= htmlspecialchars($cart['TotalAmount']) ?> BDT</p>

                    <p><strong>Payment Status:</strong> <?= $cart['PaymentStatus'] ?: 'Pending' ?></p>
                </div>
            <?php endwhile; ?>
            <h3>Total Amount to Pay: <?= $totalAmount ?> BDT</h3>
            <h3>Your Account Balance: <?= $currentPoints ?> BDT</h3>

            <form method="post" class="actions">
                <button name="pay" type="submit">Pay Using Points</button>
                <button name="clear" type="submit">Clear Cart</button>
            </form>
        </div>
    </form>
</body>
</html>
