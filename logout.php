<?php require "header.php" ?>
<?php require "menu.php" ?>

<?php 
unset($_SESSION["customer"]);
echo '<script>alert("成功登出");</script>';

$url = "home.php";
echo "<script type='text/javascript'>";
echo "window.location.href='$url'";
echo "</script>"; 

?>
<?php require "footer.php" ?>