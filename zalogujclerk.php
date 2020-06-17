<html>
<head>
  <title>Logowanie pracownicze - waBank - bank dla ludzi™</title>
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

    <div class="login_content">

      <?php
        session_start();
        //sprawdzenie czy pracownik jest już zalogowany, jeśli tak, przejdź do panelu pracowniczego
        if(isset($_SESSION["clerk_id"])){
          header('Location: clerkpanel.php');
        }
        require_once('db_connect.php');
        //utworzenie tabeli błędów
        $errors = array('id'=>'','pass'=>'');
        $id = "";
        $pass = "";
        //walidacja czy pole zostało uzupełnione
        if($_SERVER["REQUEST_METHOD"] == "POST"){
          if(empty($_POST["id"])){
            $errors["id"] = "Pole nie może być puste.";
          } else{
            $id = trim($_POST["id"]);
          }
          //walidacja czy pole zostało uzupełnione
          if(empty($_POST["pass"])){
            $errors["pass"] = "Pole nie może być puste.";
          } else{
            $pass = trim($_POST["pass"]);
          }
          //sprawdzenie czy podany pracownik istnieje w bazie
          $sql_doesclerkexist = "SELECT login FROM Clerk_login";
          $result = pg_query($conn, $sql_doesclerkexist);
          $existingclerks = pg_fetch_all($result);
          //jeśli istnieje, sprawdź hasło
          if(in_array($id, array_column($existingclerks, 'login')))
          {
            $sql_checkpass = "SELECT hashed_pass FROM Clerk_login WHERE login = '$id'";
            $result = pg_query($conn, $sql_checkpass);
            $hashedPwdInDb = pg_fetch_result($result,0,0);
            if(password_verify($pass, $hashedPwdInDb)){
              $_SESSION["clerk_id"] = $id;
            } else{
              $errors["pass"] = "Niepoprawne hasło.";
            }
          } else{
            $errors["id"] = "Nie ma konta o takim identyfikatorze.";
          }
          //jeśli nienapotkano błędów, przenieś go do panelu pracownika
          if(!array_filter($errors)){
            header('Location: clerkpanel.php');
          }
        }
      ?>

      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        Identyfikator pracownika:
        <br />
        <input type="text" name="id" value="<?php echo $id ?>"/>
        <br/>
        <span class="error"><?php echo $errors["id"];?></span>
        <br/>
        Hasło:
        <br />
        <input type="password" name="pass" value="<?php echo $pass ?>" />
        <br/>
        <span class="error"><?php echo $errors["pass"];?></span>
        <br />
        <input type="submit" value="Zaloguj" />
      </form>

    </div>
  </div>
</body>
</html>
