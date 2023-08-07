<?php require 'header.php'?>
<?php require 'menu.php'?>

<?php
  $pdo = new PDO('mysql:host=localhost:3307;dbname=finance;charset=utf8','root','');

  #顯示個別股票歷年的股息
  if(isset($_REQUEST["stockid"]))
  {
    $sql = $pdo->prepare("SELECT * FROM stock WHERE stockid = ?");
    $sql->execute([$_REQUEST["stockid"]]);
    foreach($sql->fetchAll() as $row)
    {
      echo "<p>股票代碼：".$row["stockid"]."</p>" ;
      echo "<p>股票簡稱：".$row["stockname"]."</p>";
      echo "<p>股票名稱：".$row["fullname"]."</p>";
      echo "<p>股票市場：".$row["market"]."</p>";
      echo "<p>股票種類：".$row["stocktype"]."</p>";
    }
    echo "<table>";
    echo "<tr>
        <th>宣佈日期</th>
        <th>Ex日期</th>
        <th>財政年度</th>
        <th>標題</th>
        <th>付款日期</th>
        <th>每股金額</th>
    </tr>";
    $sql = $pdo->prepare("SELECT * FROM dividend WHERE stockid = ? ORDER BY ex_date DESC");
    $sql->execute([$_REQUEST["stockid"]]);
    foreach($sql->fetchAll() as $row)
    {
      echo "<tr>";
      echo "<td>".$row['announced_date']."</td>";
      echo "<td>".$row['ex_date']."</td>";
      echo "<td>".$row['finance_year']."</td>";
      echo "<td>".$row['dividendsubject']."</td>";
      echo "<td>".$row['payment_date']."</td>";
      echo "<td>".$row['amount']."</td>";
      echo "</tr>";
    }
}

#顯示user歷年收到的股息
else
{
  $sql = $pdo->prepare("SELECT * FROM stock");
  $sql->execute([]);
  foreach($sql->fetchAll() as $row)
  {
    $count = 0; #限制table row數量            
    echo "<p>股票代碼：".$row["stockid"]."</p>" ;
    echo "<p>股票簡稱：".$row["stockname"]."</p>";
    echo "<p>股票名稱：".$row["fullname"]."</p>";
    echo "<p>股票市場：".$row["market"]."</p>";
    echo "<p>股票種類：".$row["stocktype"]."</p>";
    echo "<a href='stock_detail.php?stockid=".$row['stockid']."'>股息詳情</a>";

    $fyDividend = 0;
    $sql2= $pdo->prepare("SELECT min(finance_year) FROM dividend WHERE stockid = ? ");
    $sql2->execute([$row["stockid"]]);
    $preYear = $sql2->fetchColumn();
    if(empty($preYear))
    {
      echo "<p style='color:blue;'>暫無股息</p>";
      echo "<hr>";
    }
    else
    {
      echo "<table class=vertical>";
      echo "<tr>
        <th>財政年度</th>
        <th>每股總金額</th>
        </tr>";

      $sql2= $pdo->prepare("SELECT * FROM dividend WHERE stockid = ? ");
      $sql2->execute([$row["stockid"]]);          
      foreach($sql2->fetchAll() as $item)
      {
        if($count == 8)
        {
          echo "</table>
            <table class=vertical>
            <tr>
            <th>財政年度</th>
            <th>每股總金額</th>
            </tr>";
          $count = 0;
        }
        if($preYear != $item['finance_year'])
        {
          echo "<tr>";
          echo "<td><div>".$preYear."</div></td>";
          echo "<td><div>".$fyDividend."</div></td>";
          echo "</tr>";
          $fyDividend = 0;
          $count++;
          $preYear = $item['finance_year'];
        }                
        $fyDividend += $item['amount'];
        }
        echo "<tr>";
        echo "<td><div>".$preYear."</div></td>";
        echo "<td><div>".$fyDividend."</div></td>";
        echo "</tr>";
        echo "</table>";
        echo "<hr>";
      }
    }
  }
?>

<?php require 'footer.php'?>