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
      <h1>Wykonaj przelew</h1>
      <?php
        require_once('db_connect.php');
        //jeśli użytkownik jest zalogowany
        if(isset($_SESSION["id"])){
          $accountindex = array();
          $id = $_SESSION['id'];
          //pobierz dane o kliencie
          $sql_getcustomerinfo = "SELECT * FROM Customer INNER JOIN Login ON Customer.customer_id = Login.customer_id WHERE login = '$id'";
          $result = pg_query($conn, $sql_getcustomerinfo);
          $customerdata = pg_fetch_all($result);
          //pobierz dane z tabeli Customer_Account_Xref
          $customer_id = $customerdata[0]['customer_id'];
          $sql_getaccountids = "SELECT * FROM Customer_Account_Xref WHERE customer_id = '$customer_id'";
          $result = pg_query($conn, $sql_getaccountids);
          $xreftable = pg_fetch_all($result);
          foreach($xreftable as $pozycja=>$identyfikator)
          {
            array_push($accountindex, $identyfikator["account_id"]);
          }
          //jeśli wybrano konto z którego należy wykonać przelew, wskazano odbiorcę, oraz wprowadzono kwotę
          if(isset($_POST['account_choice']) && isset($_POST['recipient_id']) && isset($_POST['change']))
          {
            //deklaracja zmiennych przesłanych z formularza oraz bieżącej daty
            $senderacc = $_POST['account_choice'];
            $recipientacc = $_POST['recipient_id'];
            $amount = $_POST['change'];
            $title = $_POST['title'];
            $time = date('Y-m-d H:i:s');
            //pobierz dane o koncie w celu sprawdzenia czy stan konta pozwala wykonać przelew
            $sql_getaccountdata = "SELECT * FROM Account WHERE account_id = '$senderacc'";
            $result = pg_query($conn, $sql_getaccountdata);
            $currentaccounttable = pg_fetch_all($result);
            $balance = $currentaccounttable[0]['balance'];
            $balance = substr($balance, 1);
            $balance = (float)$balance;
            //porównanie kwoty przelewu do bieżącego stanu konta
            if($balance < $amount)echo "brak środków na koncie";
            else{
              //wprowadź transakcje do bazy danych
              $sql_newtransaction = "INSERT INTO Transactions(sender_id,recipient_id,amount,description,transaction_date) VALUES('$senderacc','$recipientacc','$amount','$title','$time')";
              pg_query($conn, $sql_newtransaction);
              //zmień saldo w koncie nadawcy
              $sql_changebalance_sender = "UPDATE account set balance = (balance - '$amount'::money) WHERE account_id = $senderacc";
              pg_query($conn, $sql_changebalance_sender);
              //zmień saldo w koncie odbiorcy
              $sql_changebalance_recipient = "UPDATE account set balance = (balance + '$amount'::money) WHERE account_id = $recipientacc";
              pg_query($conn, $sql_changebalance_recipient);
              $message = "Pomyślnie zrealizowano transakcje";
              echo "<script type='text/javascript'>alert('$message');</script>";
            }
          }

        } else echo "<h1>Nie jesteś zalogowany.</h1>";


      ?>
      <!-- Ta zawartość zostanie wyświetlona jedynie gdy klient jest zalogowany -->
      <?php if(isset($_SESSION["id"])) : ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            Wybierz konto:
            <?php
              echo '<select name="account_choice">';
              //wypełnij listę kontami klienta
              foreach($accountindex as $currentaccount){
              $sql_getaccountdata = "SELECT * FROM Account WHERE account_id = '$currentaccount'";
              $result = pg_query($conn, $sql_getaccountdata);
              $currentaccounttable = pg_fetch_all($result);

              if($currentaccounttable[0]['ac_type'] == 'personal')$name = "Konto Osobiste";
              if($currentaccounttable[0]['ac_type'] == 'savings')$name = "Konto Oszczędnościowe";
              if($currentaccounttable[0]['ac_type'] == 'vip')$name = "Konto VIP";
              $desc = $currentaccounttable[0]['description'];
              $balance = $currentaccounttable[0]['balance'];

              echo "<option value='".$currentaccount."'>".$desc. " - " . $name. " - ". $balance."</option>";
              }
            echo '</select>';
          ?>
          <br />
          Wprowadź numer konta odbiorcy:
          <br />
          <input type="number" name="recipient_id"/>
          <br/>
          Wprowadź tytuł przelewu:
          <br />
          <input type="text" name="title"/>
          <br/>
          Wprowadź kwotę przelewu:
          <br />
          <input type="number" step="0.01" min="0.01" name="change"/>
          <br/>
          <input type="submit" value="Zatwierdź" />
        </form>
        <br />
        <a href="userpanel.php"><h2>Powrót do panelu klienta</h2></a>
      <?php endif; ?>

    </div>
  </div>
</body>
</html>
