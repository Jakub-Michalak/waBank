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
        } else{
          //jeśli przesłano zawartość formularza, przypisz wszystkie zebrane dane do zmiennych
          if($_SERVER["REQUEST_METHOD"] == "POST"){
            $account_type = $_POST['account_type'];
            $login = $_POST['id'];
            $hashedPwd = password_hash($_POST["pass"], PASSWORD_DEFAULT);
            $last_name = $_POST['last_name'];
            $first_name = $_POST['first_name'];
            $pesel = $_POST['pesel'];
            $street = $_POST['street'];
            $city = $_POST['city'];
            $post_code = $_POST['post_code'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            //utwórz nowego klienta w bazie danych
            $sql_newcustomer = "INSERT INTO Customer(last_name,first_name,PESEL,street,city,post_code,phone,email) VALUES('$last_name','$first_name','$pesel','$street','$city','$post_code','$phone','$email')";
            //pobierz id nowego klienta
            $sql_customercount = "SELECT currval(pg_get_serial_sequence('Customer','customer_id'))";

            pg_query($conn, $sql_newcustomer);
            $result = pg_query($conn, $sql_customercount);
            $idcount = pg_fetch_result($result, 0, 0);
            //utwórz logowanie dla nowego klienta
            $sql_newlogin = "INSERT INTO Login (customer_id,login,hashed_pass) VALUES ('$idcount', '$login' , '$hashedPwd')";
            pg_query($conn, $sql_newlogin);
            //utwórz konto wybranego typu
            $sql_newaccount = "INSERT INTO Account (ac_type, description, balance) VALUES ('$account_type','konto',0)";
            pg_query($conn, $sql_newaccount);
            //przypisz utworzone konto do klienta
            $sql_newxref = "INSERT INTO Customer_Account_Xref(customer_id,account_id) VALUES ('$idcount', currval(pg_get_serial_sequence('Account','account_id')))";
            pg_query($conn, $sql_newxref);
          }
        }


      ?>
      <!-- Ta zawartość zostanie wyświetlona jedynie gdy pracownik jest zalogowany -->
      <?php if(isset($_SESSION["clerk_id"])) : ?>
        <a href="wylogujclerk.php" class="accent-text"><h1>Wyloguj</h1></a>
        <br />
        <h1>Załóż konto klienta</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
          Rodzaj konta:
          <br />
          <select name="account_type">
            <option value="personal">Konto osobiste</option>
            <option value="savings">Konto oszczędnościowe</option>
            <option value="vip">Konto VIP</option>
          </select>
          <br/>

          Logowanie klienta:
          <br />
          <input type="text" name="id"/>
          <br/>

          Hasło:
          <br />
          <input type="password" name="pass"/>
          <br />

          Nazwisko:
          <br />
          <input type="text" name="last_name"/>
          <br />

          Imię:
          <br />
          <input type="text" name="first_name"/>
          <br />

          PESEL:
          <br />
          <input type="text" name="pesel"/>
          <br />

          Adres:
          <br />
          <input type="text" name="street"/>
          <br />

          Miasto:
          <br />
          <input type="text" name="city"/>
          <br />

          Kod pocztowy:
          <br />
          <input type="text" name="post_code"/>
          <br />

          Telefon:
          <br />
          <input type="text" name="phone"/>
          <br />

          E-mail:
          <br />
          <input type="text" name="email"/>
          <br />

          <input type="submit" value="Utwórz konto" />
        </form>
        <br />
        <a href="clerkpanel.php"><h1>Powrót do panelu pracownika</h1></a>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
