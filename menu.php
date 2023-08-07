<a class="menu" href="trade.php">股票記錄</a>
<a class="menu" href="trade_add.php">新增交易</a>
<a class="menu" href="dividend.php">股息記錄</a>
<a class="menu" href="holding.php">現有股票</a>
<a class="menu" href="stock_detail.php">股息大全</a>

<?php
if(isset($_SESSION['customer'])) {?>
<a class="menu" href="change.php">修改密碼</a>
<a class="menu" href="logout.php">登出</a>
<hr>
<?php } else  { ?>
<a class="menu" href="register.php">注冊</a>
<a class="menu" href="login.php">登入</a>
<hr>
<?php } ?>