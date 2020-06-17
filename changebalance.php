<html>
<head>
  <title>Panel pracowniczy - waBank - bank dla ludzi™</title>
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
          <a id="a_navbar_login" href="zaloguj.php"><div class="navbar_item" id="navbar_login">
            Zaloguj
          </div></a>
        </div>
    </div>

    <div class="panel_content">
      <?php
        $id = '';
        session_start();
        require_once('db_connect.php');
        //sprawdzenie czy pracownik jest zalogowany
        if(!isset($_SESSION["clerk_id"])){
            echo "<h1>Nie jesteś zalogowany.</h1>";
            echo "<br/>";
            echo '<h2><a class="accent-text" href="zalogujclerk.php">Przejdź do panelu logowania pracownika</a></h2>';
        }else{
          //sprawdzanie czy w formularzu przesłano już identyfikator klienta
          if(isset($_POST['client_id'])){
            $accountindex = array();
            $id = $_POST['client_id'];
            //pobranie wszystkich danych klienta z tabeli Customer
            $sql_getcustomerinfo = "SELECT * FROM Customer INNER JOIN Login ON Customer.customer_id = Login.customer_id WHERE login = '$id'";
            $result = pg_query($conn, $sql_getcustomerinfo);
            $customerdata = pg_fetch_all($result);

            //pobranie rekordów z tabeli Customer_Account_Xref
            $customer_id = $customerdata[0]['customer_id'];
            $sql_getaccountids = "SELECT * FROM Customer_Account_Xref WHERE customer_id = '$customer_id'";
            $result = pg_query($conn, $sql_getaccountids);
            $xreftable = pg_fetch_all($result);
            foreach($xreftable as $pozycja=>$identyfikator)
            {
              array_push($accountindex, $identyfikator["account_id"]);
            }
            //jeśli wybrano konto z rozwijanej listy i wpisano kwotę wpłaty lub wypłaty
            if(isset($_POST['account_choice']) && isset($_POST['change'])){
              $chosenaccount = $_POST['account_choice'];
              $amount = $_POST['change'];
              $time = date('Y-m-d H:i:s');
              //utwórz nowy rekord w tablicy Transactions
              $sql_newtransaction = "INSERT INTO Transactions(sender_id,recipient_id,amount,description,transaction_date) VALUES(0,'$chosenaccount','$amount','Wpłata / wypłata u bankiera','$time')";
              pg_query($conn, $sql_newtransaction);
              //zmień stan konta na wybranym koncie
              $sql_changebalance = "UPDATE account set balance = (balance + '$amount'::money) WHERE account_id = $chosenaccount";
              pg_query($conn, $sql_changebalance);
              $message = "Pomyślnie zrealizowano transakcje";
              echo "<script type='text/javascript'>alert('$message');</script>";
            }
          }
        }


      ?>
      <!-- Ta zawartość zostanie wyświetlona jedynie gdy pracownik jest zalogowany -->
      <?php if(isset($_SESSION["clerk_id"])) : ?>
        <a href="wylogujclerk.php" class="accent-text"><h1>Wyloguj</h1></a>
        <br />
        <h1>Wpłata / Wypłata</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
          Wprowadź ID klienta:
          <br />
          <input type="text" name="client_id" value="<?php echo $id ?>"/>
          <br/>

          Wybierz konto:
          <?php
            if(isset($_POST['client_id'])){
              echo '<select name="account_choice">';
              //zaciągnij do listy wszystkie konta wybranego klienta
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
            }else {
              echo '<select name="account_choice" disabled="disabled"></select>';
            }
          ?>
          <br />
          Wprowadź kwotę wpłaty (lub wypłaty, używając minusa):
          <br />
          <input type="number" step="0.01" name="change"/>
          <br/>

          <input type="submit" value="Zatwierdź" />
        </form>
        <br />
        <a href="clerkpanel.php"><h1>Powrót do panelu pracownika</h1></a>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
