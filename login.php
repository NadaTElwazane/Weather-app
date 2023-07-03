<!DOCTYPE html>
<html lang="en">

<head>
    <title>login</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/login-signup.css">
    <script>
        function validatelogin() {
            var emaillog = document.getElementById("email");
            var passwordlogin = document.getElementById("psw");
            if (emaillog.value.length == 0) {
                alert("Please enter your email");
                emaillog.focus();
                return false;
            }
            // validate email using regex
            var emailRegex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
            if (!emailRegex.test(emaillog.value)) {
                alert("Please enter a valid email");
                emaillog.focus();
                return false;
            }
            
            if (passwordlogin.value.length == 0) {
                alert("Please enter your password");
                passwordlogin.focus();
                return false;
            }
            return true;
        }
    </script>
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
           
        </style>
        <script>
        var vid = document.getElementById("myVideo");
        vid.playbackRate = 0.5;
        vid.play();
        vid.style.filter = "blur(10px)";
        </script>';}
        else{
            echo '<div class="background-container">
            <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1231630/moon2.png" alt="">
            <div class="stars"></div>
            <div class="twinkling"></div>
            <div class="clouds"></div>
        </div>';
        }
    ?>
    

    
    <form id="login" name="login" action="index.php" method="post" onsubmit="return validatelogin()">
        <h3>Login Here</h3>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input id="password-field" type="password" id="psw" name="psw" minlength="3" required>
        <button>Log In</button>
        <input type="hidden" name="login" value="1">
        <p>Don't have an account?
            <button><a href="Signup.php" style="color: #080710;">Sign up</a></button>
        </p>


    </form>

</body>

</html>