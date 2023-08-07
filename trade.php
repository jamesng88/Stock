<?php require 'header.php'?>
<?php require 'menu.php'?>

<?php
  if(isset($_SESSION["customer"]))
  {
    $pdo = new PDO('mysql:host=localhost:3307;dbname=finance;charset=utf8','root','');
    $sql = $pdo->prepare("SELECT * FROM trade, stock WHERE trade.userid = ? AND stock.stockid = trade.stockid");
    $sql->execute([$_SESSION['customer']['id']]);
    $dataFetch = $sql->fetchAll();

    if(empty($dataFetch))
      echo "<p>無交易記錄</p>";
    else
    {
      echo "<table>";
      echo "<tr>
        <th>交易日期</th>        
        <th>股票名稱</th>
        <th>股票代碼</th>
        <th>交易形態</th>
        <th>數量</th>
        <th>金額</th>
      </tr>";
      foreach($dataFetch as $row)
      {
        echo "<tr>";
        echo "<td>".$row['trade_date']."</td>";
        echo "<td>".$row['stockname']."</td>";
        echo "<td>".$row['stockid']."</td>";
        if($row['tradetype'])
          echo "<td>買入</td>";
        else
          echo "<td>賣出</td>";
        echo "<td>".$row['quantity']."</td>";
        echo "<td>".$row['price']."</td>";
        echo "</tr>";
        
      }
  }
    echo "</table>";

  }
  else
  {
    echo '<alert>"請先登入賬戶"';
    $url = "login.php";
    echo "<script type='text/javascript'>";
    echo "window.location.href='$url'";
    echo "</script>"; 
  }
?>
<?php require 'footer.php'?>