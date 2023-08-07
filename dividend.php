<?php require "header.php" ?>
<?php require "menu.php" ?>

<?php
  if(isset($_SESSION["customer"]))
  {
    $pdo = new PDO('mysql:host=localhost:3307;dbname=finance;charset=utf8','root','');
    $sql = $pdo->prepare("SELECT DISTINCT stockid FROM trade WHERE userid=?");
    $sql->execute([$_SESSION["customer"]["id"]]);

    #找出user擁有的股票
    foreach($sql->fetchAll() as $row) 
    {
      $sql = $pdo->prepare("SELECT min(trade_date), stockname FROM trade, stock WHERE userid=? AND trade.stockid=?
      AND trade.stockid = stock.stockid ");
      $sql->execute([$_SESSION["customer"]["id"], $row["stockid"]]);
      
      #找user在個別股票第一次的購買時間點
      foreach($sql->fetchAll() as $item)
      {
        $holding = 0;
        $totalDividend = 0;
        $sql2 = $pdo->prepare("SELECT min(ex_date), stockid, amount FROM dividend WHERE dividend.stockid = ? AND ex_date > ?");
        $sql2->execute([$row["stockid"], $item["min(trade_date)"]]);

        echo "<p>股票代碼：".$row["stockid"];
        echo "<p>股票名稱：".$item["stockname"];
        echo "<a href='stock_detail.php?stockid=".$row["stockid"]."'>公司歷年股息</a>";
        echo "<table>";
          echo "<tr>
            <th>Ex 日期</th>
            <th>每股利息</th>
            <th>數量</th>        
            <th>金額</th>
          </tr>";

          
        #計算每個股票的股息
        #先計算在第一次購買后的Ex date前購買了多少股
        foreach($sql2->fetchAll() as $specific)
        {
          $sql3 = $pdo->prepare("SELECT * FROM trade WHERE trade_date < ? and stockid = ?");
          $sql3->execute([$specific["min(ex_date)"], $row["stockid"]]);
          foreach($sql3->fetchAll() as $each)
          {   
              if($each["tradetype"]) #buy
                  $holding += $each["quantity"];
              else                   #sell
                  $holding -= $each["quantity"];
          }
          $totalDividend += $specific["amount"] * $holding;
          echo "<tr>";
          echo "<td>".$specific["min(ex_date)"]."</td>";
          echo "<td>".$specific["amount"]."</td>";
          echo "<td>".$holding."</td>";
          echo "<td>".$specific["amount"] * $holding."</td>";
          echo "</tr>";

          #再計算 第n個ex date到第n+1個ex date期間買了多少
          $preDate = $specific["min(ex_date)"];
          $sql4 = $pdo->prepare("SELECT ex_date, amount FROM dividend WHERE ex_date>? AND stockid=?");
          $sql4->execute([$specific["min(ex_date)"], $row["stockid"]]);
          foreach($sql4->fetchAll() as $pay)
          {
              $sql5 = $pdo->prepare("SELECT * FROM trade WHERE stockid=? AND trade_date BETWEEN ? AND ? ");
              $sql5->execute([$row["stockid"], $preDate, $pay["ex_date"]]);
              
              foreach($sql5->fetchAll() as $each)
              {
                  if($each["tradetype"]) #buy
                      $holding += $each["quantity"];
                  else                   #sell
                      $holding -= $each["quantity"];
              }
              if($holding == 0)
                  break;

              $totalDividend += $pay["amount"] * $holding;
              echo "<tr>";
              echo "<td>".$pay["ex_date"]."</td>";
              echo "<td>".$pay["amount"]."</td>";
              echo "<td>".$holding."</td>";
              echo "<td>".$pay["amount"] * $holding."</td>";
              echo "</tr>";          
            }
        }
        echo "</table>";
        echo "<p>擁有：".$holding."股</p>";
        echo "<p>纍積股息：".$totalDividend."</p>";
        echo "<hr>";
      }
    }
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