<html>
<head>
  <title>Panel klienta - waBank - bank dla ludzi™</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
  <meta charset="UTF-8" />
  <meta name="description" content="waBank SA – bank z hiszpańskiej grupy Banco Motomoto działający w Polsce pod szyldem waBank SA od 2006 roku." />
  <meta name="keywords" content="konta, kredyty, karty, płatności, przelewy, oszczędności, inwestycje, ubezpieczenia, bankowość" />
  <meta name="author" content="waBank SA" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
  <div class="container">
    <div class="header">
        <a href="index.html">
          <div class="logo">
          </div>
        </a>
        <div class="navbar">
          <a href="konta.html"><div class="navbar_item">
            Konta
          </div></a>
          <a href="kredyty.html"><div class="navbar_item">
            Kredyty
          </div></a>
          <a href="karty.html"><div class="navbar_item">
            Karty
          </div></a>
          <a href="inwestycje.html"><div class="navbar_item">
            Inwestycje
          </div></a>
          <?php
          session_start();
          //ten fragment kodu wyświetla przycisk zaloguj jeśli użytkownik nie jest zalogowany, lub wyloguj, jeśli jest zalogowany
          if(isset($_SESSION["id"])){
              echo '<a id="a_navbar_login" href="wyloguj.php">';
              echo '<div class="navbar_item" id="navbar_login">';
              echo 'Wyloguj';
              echo '</div>';
              echo '</a>';
          } else {
            echo '<a id="a_navbar_login" href="zaloguj.php">';
            echo '<div class="navbar_item" id="navbar_login">';
            echo 'Zaloguj';
            echo '</div>';
            echo '</a>';

          }
          ?>
        </div>
    </div>

    <div class="panel_content">
      <h1>Panel klienta</h1>
      <br />
      <?php
        require_once('db_connect.php');
        //jeśli klient jest zalogowany
        if(isset($_SESSION["id"])){
            $id = $_SESSION["id"];
            //pobierz dane klienta
            $sql_getcustomerinfo = "SELECT * FROM Customer INNER JOIN Login ON Customer.customer_id = Login.customer_id WHERE login = '$id'";
            $result = pg_query($conn, $sql_getcustomerinfo);
            $customerdata = pg_fetch_all($result);
            //pobierz dane z tabeli Customer_Account_Xref
            $customer_id = $customerdata[0]['customer_id'];
            $sql_getaccountids = "SELECT * FROM Customer_Account_Xref WHERE customer_id = '$customer_id'";
            $result = pg_query($conn, $sql_getaccountids);
            $xreftable = pg_fetch_all($result);

          } else echo "<h1>Nie jesteś zalogowany.</h1>";

      ?>
      <!-- Ta zawartość zostanie wyświetlona jedynie gdy klient jest zalogowany -->
      <?php if(isset($_SESSION["id"])) : ?>


        <h2>Witaj <?php echo $customerdata[0]['first_name']; ?></h2>
        <br />
        <br />

        <h2>Twoje konta:</h2>
        <br />
        <?php
            //wyświetl listę kont, informacje o nim, oraz link do historii transakcji
            $accountindex = array();
            foreach($xreftable as $pozycja=>$identyfikator)
            {
              array_push($accountindex, $identyfikator["account_id"]);
            }

            foreach($accountindex as $currentaccount)
            {
              $sql_getaccountdata = "SELECT * FROM Account WHERE account_id = '$currentaccount'";
              $result = pg_query($conn, $sql_getaccountdata);
              $currentaccounttable = pg_fetch_all($result);

              if($currentaccounttable[0]['ac_type'] == 'personal')$name = "Konto Osobiste";
              if($currentaccounttable[0]['ac_type'] == 'savings')$name = "Konto Oszczędnościowe";
              if($currentaccounttable[0]['ac_type'] == 'vip')$name = "Konto VIP";
              $desc = $currentaccounttable[0]['description'];
              $balance = $currentaccounttable[0]['balance'];
              $account_id = $currentaccounttable[0]['account_id'];
              echo '<h3 style="display: inline;">'. "$name - </h3>" . '<h3 style="display: inline;" class="accent-text">' . "$balance</h3>" . "<br/>";
              echo "Numer konta - $account_id <br />";
              echo '<a href="transactionhistory.php?id='.$account_id.'">Historia transakcji</a><br /><br />';

            }
        ?>

        <a href="transfer.php"><h2>Wykonaj przelew</h2></a>

        <br />
        <br />
        <h3>Dane klienta:</h3>
        <p><?php echo $customerdata[0]['first_name']; echo " "; echo $customerdata[0]['last_name']; ?></p>
        <p>PESEL: <?php echo $customerdata[0]['pesel']; ?></p>
        <p><?php echo $customerdata[0]['street']; ?></p>
        <p><?php echo $customerdata[0]['post_code']; echo " "; echo $customerdata[0]['city']; ?></p>
        <p>tel. <?php echo $customerdata[0]['phone']; ?></p>
        <p><?php echo $customerdata[0]['email']; ?></p>


      <?php endif; ?>

    </div>
  </div>
</body>
</html>
