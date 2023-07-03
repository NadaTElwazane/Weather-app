<?php
require_once('Database.php');
$database = new database("localhost", "root", "", "weatherforecast");
$email = $_POST['email'];
$password = md5($_POST['password']);
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$signup = $_POST['signup'];

if ($database->checkduplicates($email) == true) {
    echo '<script>alert("User already exists")</script>';
    echo "<script type='text/javascript'>
    window.location = '" . $_SERVER['HTTP_REFERER'] . "';
    </script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Complete Profile</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/login-signup.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>


</head>

<body>
<?php
    $ip = isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']:'::1';

    if ($ip == '::1') {
        $ip = '41.237.99.154';
        // $ip ='118.70.126.159';
    }
    $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
    // get lat and lon from loc
    $lat_lon=explode(",",$details->loc);
    $lat=$lat_lon[0];
    $lon=$lat_lon[1];

    // decide if it's day or night
    $sun_info = date_sun_info(time(), $lat, $lon);
    $sunrise = $sun_info['sunrise'];
    $sunset = $sun_info['sunset'];
    $now = time();
    $day = ($now > $sunrise && $now < $sunset);
    // if day use morning sky background
    if ($day){
            // use blue-sky.mov as a background
            echo '
            <video autoplay muted loop id="myVideo" style="z-index:-2">
            <source src="blue-sky.mov" type="video/mp4">
        </video>
    
        <style>
            #myVideo {
                position: fixed;
                right: 0;
                bottom: 0;
                min-width: 100%;
                min-height: 100%;
            }
    
            /* Add some content at the bottom of the video/page */
            .content {
                position: fixed;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                color: #f1f1f1;
                width: 100%;
                padding: 20px;
            }
    
            *,
            *::before,
            *::after {
                box-sizing: border-box;
                color: #243738;
            }
            body {
                background-color: transparent;
            }
            h3{
                color: #243738;
            }
            label{
                color: #243738;
            }
            p{
                color: #243738;
            }
            input{
                color: #243738;
            }
            ::placeholder{
                color: #243738;
            }
            :-ms-input-placeholder{
                color: #243738;
            }
            ::-ms-input-placeholder{
                color: #243738;
            }
            table{
                color: #243738;
            } 
            th{
                color: #010101;
            }
            table>tbody>tr>td{
                color: #243738;
            }
            tr{
                color: #243738;
            }

        </style>
        <script>
        var vid = document.getElementById("myVideo");
        vid.playbackRate = 0.5;
        vid.play();
        vid.style.filter = "blur(20px)";
        </script>';
    }
        else{
            echo '<div class="background-container">
            <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1231630/moon2.png" alt="">
            <div class="stars"></div>
            <div class="twinkling"></div>
            <div class="clouds"></div>
        </div>';
        }
    ?>
<!--     
    <div class="background-container">
        <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1231630/moon2.png" alt="">
        <div class="stars"></div>
        <div class="twinkling"></div>
        <div class="clouds"></div>
    </div> -->
    <form action="index.php" style="width:70vw" method="POST" id="compProfile">

        <label class="profile">2. Current Location</label>

        <input type="text" id="keyword" name="keyword">
        <input type="hidden" id="email" name="email" value="<?php echo $email; ?>">
        <input type="hidden" id="password" name="password" value="<?php echo $password; ?>">
        <input type="hidden" id="firstname" name="firstname" value="<?php echo $firstname; ?>">
        <input type="hidden" id="lastname" name="lastname" value="<?php echo $lastname; ?>">
        <input type="hidden" id="signup" name="signup" value="<?php echo $signup; ?>">
        <div id="lat-lon">
            <input type="hidden" id="lat" name="lat">
            <input type="hidden" id="lon" name="lon">
        </div>
        <div id="result"></div>
        <button id="next-comp" value="next" type="submit">Next</button>
    </form>
    <script>
        var email = "<?php echo $email; ?>";
        var fname = "<?php echo $firstname; ?>";
        var lname = "<?php echo $lastname; ?>";
        var password = "<?php echo $password; ?>";



        $(document).ready(function () {
            $("#keyword").on("keyup", function () {
                $.ajax({
                    type: "POST",
                    url: "searchRequest.php",
                    data: {
                        keyword: $("#keyword").val(),
                        email: email,
                        fname: fname,
                        lname: lname,
                        password: password,
                    },
                    success: function (data) {

                        $("#result").html(data);
                    }
                });

            });
        });
    </script>


</body>

</html>