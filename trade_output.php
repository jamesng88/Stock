<?php require 'header.php'?>
<?php require 'menu.php'?>

  <?php
    $pdo = new PDO('mysql:host=localhost:3307;dbname=finance;charset=utf8','root','');
    $sql = $pdo->prepare("INSERT INTO trade (stockid, userid ,trade_date,	quantity,	price, tradetype) VALUES (?, ?, ?, ?, ?, ?)");
    $sql->execute([$_REQUEST["stockid"], $_SESSION["customer"]["id"], 
      $_REQUEST["tradedate"], $_REQUEST["amount"], $_REQUEST["price"], $_REQUEST["tradetype"]]);
    $url = "trade.php";
    echo '<script>alert("新增成功");</script>';
    echo "<script type='text/javascript'>";
    echo "window.location.href='$url'";
    echo "</script>"; 
  ?>

<?php require 'footer.php'?>