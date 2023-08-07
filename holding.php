<?php require "header.php" ?>
<?php require "menu.php" ?>

<?php
  if(isset($_SESSION["customer"]))
  {
    $pdo = new PDO('mysql:host=localhost:3307;dbname=finance;charset=utf8','root','');
    echo "<table>";
        echo "<tr>
        <th>股票代碼</th>
        <th>股票名稱</th>
        <th>股票名稱</th>
        <th>擁有數量</th>
        </tr>";

    #找出user目前有涉略了哪些股票
    $sql = $pdo->prepare("SELECT DISTINCT(trade.stockid), stockname, stocktype FROM trade,
        stock WHERE userid=? AND trade.stockid = stock.stockid");
    $sql->execute([$_SESSION["customer"]["id"]]);
    foreach($sql->fetchAll() as $row)
    {
      #計算user目前所擁有的股票數量
      $holding = 0;
      $sql2 = $pdo->prepare("SELECT * FROM trade WHERE stockid=? ");
      $sql2->execute([$row["stockid"]]);
      foreach($sql2->fetchAll() as $each)
      {
        if($each["tradetype"]) #buy
          $holding += $each["quantity"];
        else                   #sell
          $holding -= $each["quantity"];
      }
      if($holding > 0)
      {
        echo "<tr>";
        echo "<td>".$row["stockid"]."</td>";
        echo "<td>".$row["stockname"]."</td>";
        echo "<td>".$row["stocktype"]."</td>";
        echo "<td>".$holding."</td>";
        echo "</tr>";
      }
    }        
    echo "</table>";
  }
  else
  {
    echo '<script>alert("請先登入賬戶")</script>';
    $url = "login.php";
    echo "<script type='text/javascript'>";
    echo "window.location.href='$url'";
    echo "</script>";   
  }
?>