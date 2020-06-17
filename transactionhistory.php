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
          //ten fragment kodu wyświetla przycisk zaloguj jeśli użytkownik nie jest zalogowany, lub wyloguj, jeśli jest zalogowany
          session_start();
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

    <div class="transactionhistory_content">

      <?php
        require_once('db_connect.php');
        $validation = false;
        //jeśli w URL przekazano jako argument numer konta, oraz użytkownik jest zalogowany
        if(isset($_GET["id"]) && isset($_SESSION["id"])){
          $accountindex = array();
          $id = $_SESSION['id'];
          //pobieranie informacji o kliencie
          $sql_getcustomerinfo = "SELECT * FROM Customer INNER JOIN Login ON Customer.customer_id = Login.customer_id WHERE login = '$id'";
          $result = pg_query($conn, $sql_getcustomerinfo);
          $customerdata = pg_fetch_all($result);

          //pobieranie danych z tabeli Customer_Account_Xref
          $customer_id = $customerdata[0]['customer_id'];
          $sql_getaccountids = "SELECT * FROM Customer_Account_Xref WHERE customer_id = '$customer_id'";
          $result = pg_query($conn, $sql_getaccountids);
          $xreftable = pg_fetch_all($result);
          foreach($xreftable as $pozycja=>$identyfikator)
          {
            array_push($accountindex, $identyfikator["account_id"]);
          }
          //sprawdzanie czy podane w URL konto należy do zalogowanego użytkownika
          if(in_array($_GET["id"],$accountindex)){

            $validation = true;

          }else echo "<h1>Nie masz uprawnień do wyświetlenia historii transakcji tego konta.</h1>";
        } else echo "<h1>Nie jesteś zalogowany lub id jest błędne.</h1>";

      ?>
      <!-- Ta zawartość zostanie wyświetlona jedynie gdy zalogowany użytkownik jest upowazniony do wyswietlenia historii transakcji danego konta -->
      <?php if($validation) : ?>


        <h1>Historia transakcji dla konta <?php echo $_GET["id"] ?></h1>
        <br />
        <h2 class="accent-text"><?php
        //wypisanie stanu konta
        $id = $_GET['id'];
        $sql_getaccountdata = "SELECT * FROM Account WHERE account_id = $id";
        $result = pg_query($conn, $sql_getaccountdata);
        $currentaccounttable = pg_fetch_all($result);
        $balance = $currentaccounttable[0]['balance'];
        echo $balance;
        ?></h2>
        <br />
        <?php
        //pobranie z bazy danych wszystkich transakcji w których brało udział to konto
          $sql_gettransactions = "SELECT * FROM Transactions WHERE sender_id = '$id' OR recipient_id = '$id'";
          $result = pg_query($conn,$sql_gettransactions);
          $transactiontable = pg_fetch_all($result);
          //jeśli brak transakcji, wyświetl stosowny komunikat, w innym wypadku przystąp do ich wyświetlania
          if(empty($transactiontable))echo "<h2>Brak transakcji dla tego konta.</h2>";
          else{
                      echo '<table class="transaction-table">';
                      echo "<tr>";
                      echo "<th>Numer konta nadawcy</th>";
                      echo "<th>Numer konta odbiorcy</th>";
                      echo "<th>Ilość</th>";
                      echo "<th>Opis</th>";
                      echo "<th>Data</th>";
                      echo "</tr>";
                      //wyświetl w tabeli dane o wszystkich wykonanych transakcjach
                      $keys = array_keys($transactiontable);
                      for($i = 0; $i < count($transactiontable); $i++){
                        echo "<tr>";
                            foreach($transactiontable[$keys[$i]] as $key => $value) {
                              if($key=="sender_id"){
                                $senderid = $value;
                                echo "<td>$senderid</td>";
                              }
                              if($key=="recipient_id")echo "<td>$value</td>";
                              if($key=="amount"){
                                $amount = $value;
                                //ten fragment kodu koloruje kwotę dla uznań lub obciążeń na zielono lub czerwono
                                if($senderid==$id)echo '<td class="red-text">- '.$amount.'</td>';
                                else echo '<td class="accent-text">'.$amount.'</td>';
                              }
                              if($key=="description")echo "<td>$value</td>";
                              if($key=="transaction_date")echo "<td>$value</td>";
                            }
                        echo "</tr>";
                      }
                      echo "</table>";
          }



        ?>
        <br />
        <a href="userpanel.php"><h2>Powrót do panelu klienta</h2></a>
      <?php endif; ?>

    </div>
  </div>
</body>
</html>
