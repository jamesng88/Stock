<?php require "header.php" ?>
<?php require "menu.php" ?>

<?php 
  $pdo=new PDO('mysql:host=localhost:3307;dbname=finance;charset=utf8','root','');
  $sql=$pdo->prepare('SELECT * FROM customer WHERE username=?');
  $sql->execute([$_REQUEST["username"]]);

  if(empty($sql->fetchAll()))
  {
    $sql=$pdo->prepare('INSERT INTO customer (username, password) VALUES( ?,?)');
    $sql->execute([$_REQUEST["username"], $_REQUEST["password"]]);
    echo '<script>alert("注冊完成");</script>';
  }
  else
  {
    echo '<script>alert("已被注冊");</script>';
  }

?>
<?php require "footer.php" ?>