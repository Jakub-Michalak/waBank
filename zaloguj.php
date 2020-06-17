<html>
<head>
  <title>Logowanie - waBank - bank dla ludzi™</title>
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
        //sprawdź czy użytkownik jest już zalogowany, jeśli tak, przenieś go do panelu użytkownika
        if(isset($_SESSION["id"])){
          header('Location: userpanel.php');
        }
        require_once('db_connect.php');
        //utwórz tablicę zawierającą błędy
        $errors = array('id'=>'','pass'=>'');
        $id = "";
        $pass = "";

        if($_SERVER["REQUEST_METHOD"] == "POST"){
          //walidacja czy pole zostało uzupełnione
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
          //sprawdzenie czy podany login istnieje w bazie
          $sql_doesloginexist = "SELECT login FROM Login";
          $result = pg_query($conn, $sql_doesloginexist);
          $existinglogins = pg_fetch_all($result);
          //jeśli istnieje, sprawdź poprawność hasła
          if(in_array($id, array_column($existinglogins, 'login')))
          {
            $sql_checkpass = "SELECT hashed_pass FROM Login WHERE login = '$id'";
            $result = pg_query($conn, $sql_checkpass);
            $hashedPwdInDb = pg_fetch_result($result,0,0);
            if(password_verify($pass, $hashedPwdInDb)){
                $_SESSION["id"] = $id;
            } else{
              $errors["pass"] = "Niepoprawne hasło.";
            }
          } else{
              $errors["id"] = "Nie ma konta o takim identyfikatorze.";
          }
          //jeśli brak błędów, przejdź do panelu klienta
          if(!array_filter($errors)){
            header('Location: userpanel.php');
          }
        }


      ?>

      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        Identyfikator klienta:
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
