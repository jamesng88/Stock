<?php require "header.php" ?>
<?php require "menu.php" ?>

<?php 
  unset($_SESSION["customer"]);
  $pdo=new PDO('mysql:host=localhost:3307;dbname=finance;charset=utf8','root','');
  $sql = $pdo->prepare("SELECT * FROM customer WHERE username = ? AND password = ?");
  $sql->execute([$_REQUEST["username"], $_REQUEST["password"]]);
  foreach($sql->fetchAll() as $row)
  {    
    $_SESSION["customer"] = ['id'=>$row["userid"], 'username'=>$row["username"]];
  }
  if(isset($_SESSION["customer"]))
  {
    $message = "歡迎". $_SESSION["customer"]["username"] ."登入";
    echo "<script>alert('$message');</script>"; 
  }
  else
  {
    echo '<script>alert("賬號或密碼錯誤，請重新嘗試");</script>';
  }
?>

<?php require "footer.php" ?>