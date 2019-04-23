<?php
/* settings you can change */
date_default_timezone_set("America/Phoenix"); //de pus identic cu al serverului de mail
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'email@email.com';
$password = 'parola';
$refresh_in = 180; //seconds
$alert = 'https://notificationsounds.com/soundfiles/6c524f9d5d7027454a783c841250ba71/file-23_applause.mp3'; //mp3 path

$search_in_subject = array();
$search_in_subject[] = 'DGT'; //dognet
$search_in_subject[] = 'You have generated a new'; //2performant
$search_in_subject[] = 'Ai inregistrat un comision'; //2performant

/* do not change */
$since = ($_GET['since'] == "") ? strtotime("-1 hour") : $_GET['since'];
$valid_emails=0;

/* utility */
function strposa($haystack, $needle, $offset=0) {
    foreach($needle as $query) {
        if(strpos($haystack, $query, $offset) !== false) { return true; }
    }
    return false;
}

/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die(imap_last_error());

/* grab emails */
$emails = imap_search($inbox,'ALL');

/* if emails are returned, cycle through each... */
if($emails) {

    /* put the newest emails on top */
    rsort($emails);

    /* for every email... */
    foreach($emails as $email_number) {

        /* get information specific to this email */
        $overview = imap_fetch_overview($inbox,$email_number,0);
        $subject = $overview[0]->subject;
        $time = strtotime($overview[0]->date);



        /* check if is newer than last check */
        if($time >= $since) {

            /* check if subject match our filters */
            if(strposa($subject, $search_in_subject)) {
                $valid_emails++;
            }

        }

    }
}
else {
    //no emails
}

/* close the connection */
imap_close($inbox);
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="author" content="cipy.ro"/>
    <meta name="robots" content="noindex"/>

    <title>Afiliere CaCING</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css"/>

    <!-- Custom CSS -->
    <style>
        body {
            padding-top: 50px;
        }
        .starter-template {
            padding: 40px 15px;
            text-align: center;
        }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

<!-- Page Content -->
<div class="container">
    <div class="row">
        <div class="starter-template">

            <?php
            if($valid_emails == 0) {
                echo'
                <div>
                    <h1>Așteptăm comisioanele...</h1>
                    <p class="lead">Nu-ți pierde speranța!</p>
                </div>';
            }
            else {
                echo'
                <div>
                    <p class="grey small">Verificăm mailuri mai noi de '.date('Y-m-d H:i:s',$since).'</p>
                    <br/>
                    <h1>CaCING!</h1>
                    <p class="lead">Număr de comisioane: '.$valid_emails.'</p>
                    <br/>
    
                    <div class="col-lg-6 col-lg-offset-3">
                        <audio controls autoplay>
                          <source src="'.$alert.'" type="audio/mpeg">
                        </audio>
                    </div>
                </div>';
            }
            ?>


            <div class="col-lg-12">
                <br/><br/>
                <p class="grey small">Următoarea verificare în <span id="countdown"><?=$refresh_in?></span> secunde.</p>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.container -->

<script>

    var timeleft = <?=$refresh_in?>;
    var downloadTimer = setInterval(function(){
        document.getElementById("countdown").innerHTML = timeleft;
        timeleft -= 1;
        if(timeleft <= 0){
            clearInterval(downloadTimer);
            document.getElementById("countdown").innerHTML = "-"
        }
    }, 1000);

    setTimeout(function(){
        document.location.href='<?=basename($_SERVER['PHP_SELF'])?>?since=<?=$since+$refresh_in?>';
    }, <?=$refresh_in*1000?>);
</script>
</body>
</html>