<?php 

    require("db_config.php"); 
    require("recaptcha_config.php");

    $imie; $nazw; $nrtel; $email; $tresc; $captcha;
    
    //mysqli("localhost","my_user","my_password","my_db");
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME)
              or die('Błąd połączenia z bazą danych!');

    if(isset($_POST['imie']))
        $imie = (empty($_POST['imie'])) ? '' : $conn -> real_escape_string($_POST['imie']);
    if(isset($_POST['nazwisko']))
        $nazw  = (empty($_POST['nazwisko']))  ? '' : $conn -> real_escape_string($_POST['nazwisko']);
    if(isset($_POST['numertelefonu']))
        $nrtel   = (empty($_POST['numertelefonu']))   ? null : $conn -> real_escape_string($_POST['numertelefonu']);
    if(isset($_POST['email']))
        $email   = (empty($_POST['email']))   ? null : $conn -> real_escape_string($_POST['email']);
    if(isset($_POST['tresc']))
        $tresc   = (empty($_POST['tresc']))   ? '' : $conn -> real_escape_string($_POST['tresc']);

    if(isset($_POST['g-recaptcha-response']))
        $captcha=$_POST['g-recaptcha-response'];

    if(isset($captcha) && !$captcha){
        $return_message = "Please check the the captcha form send";
        goto END;
    }
        
        $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$SECRET_KEY&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);

    if($response['success'] && (isset($nrtel) && isset($email)) && ($nrtel!="" || $email!=""))
{
    $sql = "INSERT INTO kontaktskrzynka VALUES ('$imie', '$nazw', '$nrtel', '$email', '$tresc')";

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
  
    if (mysqli_query($conn, $sql)) {
        $return_message = "Twoja wiadomość została wysłana pomyślnie!";
    }
    else 
    {
        $return_message = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
else
{
        $return_message = "Wpisz email lub tel.";
}

END:
$conn -> close();
  
?>

<!DOCTYPE html>
<html lang="pl">

<head>

    <meta charset="UTF-8">
    <title>Lechpol - strona reklamowa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>

        <!--Captcha-->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <link href="style.css" rel="stylesheet" type="text/css">

</head>

<body>

    <div id="top"></div>
    <nav class="navbar navbar-expand-lg bg-light fixed-top">
        <div class="nav-container">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="index.html">Strona Główna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="naszemarki.html">Marki</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link js-scroll-trigger" href="kontakt.php">Kontakt</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="ofirmie.html">O firmie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="https://goo.gl/maps/ncpfqNVV5yAopX6bA" target="_blank">Trasa</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="container">

        <div id="messagebox">

        <form action="" method="post">

            <h1><u>Napisz do nas:</u></h1>

            Imie: <input name="imie" type="text" required/><br /><br />
            Nazwisko: <input name="nazwisko" type="text" required/> <br /><br />
            Numer telefonu*: <input name="numertelefonu" type="tel" /> <br /><br />
            Email*: <input name="email" type="text" /><br />
            Treść:<br /><textarea name="tresc" required></textarea> <br /><br />
            <div class="return-message">
                <?php echo $return_message; ?>
            </div>
            <div align="center" class="g-recaptcha" data-sitekey="<?php echo $SITE_KEY?>"></div>
            <br/>
            <input type="submit"/>

        </form>

        </div>

        <div id="feet">Strona zaprojektowana przez: Jakub Michalik</div>

    </div>

</body>

</html>