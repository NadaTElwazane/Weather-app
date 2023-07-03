<!DOCTYPE html>
<html lang="en">

<head>
    <title>Signup</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/login-signup.css">
    <script>
        function validatesignup() {
            var firstname = document.getElementById("firstname");
            var lastname = document.getElementById("lastname");
            var email = document.getElementById("email");
            var password = document.getElementById("password");
            var confirmpassword = document.getElementById("confirmpassword");
            if (firstname.value.length == 0) {
                firstname.focus();
                return false;
            }
            if (lastname.value.length == 0) {
                // alert("Please enter your last name");
                lastname.focus();
                return false;
            }
            if (email.value.length == 0) {
                // alert("Please enter your email");
                email.focus();
                return false;
            }
            if (password.value.length == 0) {
                // alert("Please enter your password");
                password.focus();
                return false;
            }
            if (confirmpassword.value.length == 0) {
                // alert("Please enter your confirm password");
                confirmpassword.focus();
                return false;
            }
            if (password.value != confirmpassword.value) {
                alert("Password and confirm password must be same");
                confirmpassword.focus();
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

            a{
                color: #243738;
            }
            body {
                background-color: transparent;
            }
        </style>
        <script>
        var vid = document.getElementById("myVideo");
        vid.playbackRate = 0.5;
        vid.play();
        vid.style.filter = "blur(20px)";
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
    <div class="container">

        <form name="signup" id="signup" class="signup-form" action="compProfile.php" method="post"
            onsubmit="return validatesignup()">
            <h3 style="margin-bottom: 1rem;">Signup Here</h3>

            <div class="name" style="display: flex; flex-direction: row;">
                <label for="firstname">First Name</label>
                <input type="text" placeholder="First Name" id="firstname" name="firstname" required>
                <label for="lastname">Last Name</label>
                <input type="text" placeholder="Last Name" id="lastname" name="lastname" required>
            </div>
            <label for="email">Email</label>
            <input type="email" placeholder="Email" id="email" name="email" required>
            <label for="password">Password</label>
            <input type="password" placeholder="Password" id="password" name="password" required>
            <label for="password">Confirm Password</label>
            <input type="password" placeholder="Password" id="confirmpassword" name="confirmpassword" required>
            <input type="hidden" name="signup" value="2">
            <button>Sign up</button>
            <p>Aleady have an account?
                <a href="login.php" >Login</a>
            </p>


        </form>
    </div>





</body>

</html>