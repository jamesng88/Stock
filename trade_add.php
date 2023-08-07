<?php require 'header.php'?>
<?php require 'menu.php'?>

<form method="post" action="trade_output.php">
  <?php
    $pdo = new PDO('mysql:host=localhost:3307;dbname=finance;charset=utf8','root','');
    $sql = $pdo->prepare("SELECT * FROM stock");
    $sql->execute();
    echo "<select name='stockid'";
    foreach($sql->fetchAll() as $row)
    {
      echo "<option value='".$row['stockid']."'>".$row['stockid']." ".$row['stockname']."</option>";
    }
  echo "</select><br>";
  //<div>公司全名：</div><input type=text readonly value=""><br>
  ?>
  
  <div>交易：</div>
  <input type="radio" name="tradetype" id="radio2" value="1" checked>買入
  <input type="radio" name="tradetype" id="radio1" value="0">賣出
  <div width="20px">Amount：</div><input type=text name="amount"><br>
  <div width="20px">Price：</div><input type=text name="price"><br>
  <div width="20px">Date：</div><input type=date name="tradedate"><br>
  <input type="submit" value="新增交易">
  <a href="new_stock.php" class="">新增股票</a>

</form>

<?php require 'footer.php'?>