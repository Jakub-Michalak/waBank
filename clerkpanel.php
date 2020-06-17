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
        session_start();
        require_once('db_connect.php');
        //sprawdzenie czy pracownik jest zalogowany
        if(!isset($_SESSION["clerk_id"])){
            echo "<h1>Nie jesteś zalogowany.</h1>";
            echo "<br/>";
            echo '<h2><a class="accent-text" href="zalogujclerk.php">Przejdź do panelu logowania pracownika</a></h2>';
        }

      ?>
      <!-- Ta zawartość zostanie wyświetlona jedynie gdy pracownik jest zalogowany -->
      <?php if(isset($_SESSION["clerk_id"])) : ?>
        <a href="wylogujclerk.php" class="accent-text"><h1>Wyloguj</h1></a>
        <br />
        <a href="addclient.php"><h1>Załóż konto klienta</h1></a>
        <br />
        <a href="changebalance.php"><h1>Wpłata / Wypłata</h1></a>
        <br />
        <a href="addaccount.php"><h1>Dodaj dodatkowe konto</h1></a>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
