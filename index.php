<?php include 'WeatherAPI.php'; ?>

<?php
require_once('Database.php');
$database = new Database("localhost", "root", "", "weatherforecast");

session_start();
if (isset($_POST['signup'])) {
    $_SESSION['email'] = $_POST['email'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    echo '<script> console.log(' . $lat . ')</script>';

    $database->insert($firstname, $lastname, $email, $password, $lon, $lat);
}

if (isset($_POST['compProfile'])) {
    $_SESSION['email'] = $_POST['email'];
    $email = $_POST['email'];
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    echo '<script> console.log(' . $firstname . ')</script>';
    if ($database->checkduplicates($email) == false) {

        echo '<script> console.log(' . $firstname . ')</script>';
        $database->insert($firstname, $lastname, $email, $password, $lon, $lat);
    }
}



if (isset($_POST['login'])) {
    $_SESSION['email'] = $_POST['email'];
    $email = $_POST['email'];
    $password = md5($_POST['psw']);

    if ($database->userexists($_POST['email'], md5($_POST['psw'])) == false) {
        echo '<script>alert("You entered incorrect password or email")</script>';
        echo "<script type='text/javascript'>
    window.location = '" . $_SERVER['HTTP_REFERER'] . "';
    </script>";
    }
}

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    echo "<script>console.log( 'Debug Objects: " . $email . "' );</script>";
}

function reverseGeoCoding($latitude, $longitude)
{
    $api_url = 'https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=' . $latitude . '&longitude=' . $longitude . '&localityLanguage=en';
    $api_return_data = file_get_contents($api_url);
    $details = json_decode($api_return_data);
    $city = $details->city;
    $country = $details->countryCode;

    return array(
        'cityName' => $city,
        'country' => $country
    );
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap-grid.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap-grid.rtl.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link rel="stylesheet" href="./bootstrap-5.3.0-dist/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js" integrity="sha512-2rNj2KJ+D8s1ceNasTIex6z4HWyOnEYLVC3FigGOmyQCZc2eBXKgOxQmo3oKLHyfcj53uz4QMsRCWNbLd32Q1g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script> -->
    </script>
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Weather App</title>
</head>
<?php



$WeatherParameters = array(
    'hourly' => array('temperature_2m', 'relativehumidity_2m', 'apparent_temperature', 'weathercode', 'surface_pressure', 'visibility', 'windspeed_10m', 'winddirection_10m', 'uv_index'),
    'daily' => array('weathercode', 'sunrise', 'sunset', 'uv_index_max', 'temperature_2m_max', 'temperature_2m_min')
);

$AirQualityParameters = array(
    'hourly' => array('pm2_5', 'nitrogen_dioxide', 'sulphur_dioxide', 'ozone')
);


$weather = new WeatherAPI($WeatherParameters, $AirQualityParameters);
$weather->callWeatherAPI();
$weather->callAirQualityAPI();
$dailyWeather = $weather->getDailyWeather();
$hourlyWeather = $weather->getHourlyWeather();
$currentWeather = $weather->getCurrentWeather();
$currentAirQuality = $weather->getCurrentAirQuality();
?>

<header class="mt-0">
    <nav class="navbar navbar-expand-lg bg-transparent" id="nav" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Weather App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <?php
                        if (isset($email)) {
                            echo '<a class="nav-link dropdown-toggle" href="#" id="navbarNav" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Favourite Locations
                        </a>';
                        };
                        ?>

                        <ul class="dropdown-menu dropdown-menu-dark bg-dark" aria-labelledby="navbarNav">
                            <?php
                            if (isset($email)) {
                                // $sql = "SELECT * FROM regions WHERE email='$email'";
                                // $result = mysqli_query($conn, $sql);
                                $result = $database->searchregions($email);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $location = reverseGeoCoding(trim($row['lat']), trim($row['lon']));
                                    $city = $location['cityName'];
                                    $country = $location['country'];
                                    echo '<li><a class="dropdown-item" href="index.php?latitude=' . trim($row['lat']) . '&longitude=' . trim($row['lon']) . '">' . $city . ' ' . $country . '</a></li>';
                                }
                            }
                            ?>
                        </ul>
                        <?php
                        if (isset($email)) {
                            echo '<li class="nav-item"><a class="nav-link" onclick="signout()" href="login.php">Sign Out</a></li>';
                        } else {
                            echo '<li class="nav-item"><a class="nav-link" href="login.php">Sign In</a></li>';
                        };
                        ?>
                        <!-- default region -->
                        <?php
                        if (isset($email)) {
                            $sql = $database->currentregion($email);
                            // $sql = "SELECT * FROM registereduser WHERE email='$email'";
                            $row = mysqli_fetch_assoc($sql);
                            $lat = $row['lat'];
                            $lon = $row['lon'];
                            $location = reverseGeoCoding(trim($lat), trim($lon));
                            $city = $location['cityName'];
                            $country = $location['country'];
                            echo '<li class="nav-item default-region"><a class="nav-link" href="index.php?latitude=' . trim($lat) . '&longitude=' . trim($lon) . '">' . $city . ' ' . $country . '</a></li>';

                            if ($lat == $weather->latitude && $lon == $weather->longitude) {
                                if ($weather->is_day) {
                                    echo "<style>
                                    .default-region {
                                        color: #000;
                                        background-color: #32bdcd47;
                                        padding: 0.5rem 1rem;
                                        margin-right: 1rem;
                                        border-radius: 0.25rem;
                                    }
                                    </style>";
                                } else {
                                    echo "<style>
                                .default-region {
                                    color: #fff;
                                    background-color: #34363987;
                                    padding: 0.5rem 1rem;
                                    margin-right: 1rem;
                                    border-radius: 0.25rem;
                                }
                                </style>";
                                }
                            }
                        }
                        ?>
                    </li>
                </ul>
            </div>
            <div class="search-wrapper d-flex">
                <input type="search" list="result" name="keyword" id="keyword" placeholder="   Search city..." autocomplete="off" class="form-control me-2 search-form" onchange="selectRow(value);" data-search-field>
                <span class="m-icon">search</span>
                <datalist id="result"></datalist>
            </div>
        </div>
    </nav>
</header>

<body>
    <?php
    if ($weather->is_day) {
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
        sub{
            color: #243738;
        }
        .highlight-value {
            color: #000;
        }

        .aqi-title {
            color: #000;
        }

        .aqi-value {
            color: #000;
        }
        .favourite{
            background-color:transparent;
            border: #ddfffe61 2px solid;
        }
        .shadow-add{
            box-shadow: 0 4px 8px 0 rgb(0 0 0 / 9%);
        }
    </style>
    <script>
        var vid = document.getElementById("myVideo");
        vid.playbackRate = 0.5;
        vid.play();
        vid.style.filter = "blur(20px)";
    </script>';

        // replace data-bs-theme="dark" with data-bs-theme="light"
        echo '<script>
        var nav = document.getElementById("nav");
        nav.setAttribute("data-bs-theme", "light");
        </script>
        ';
        
    } else {
        echo '<div class="animated-background-container">
        <img class="moon-corner" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1231630/moon2.png" alt="">
        <div class="stars"></div>
        <div class="twinkling"></div>
        <div class="clouds"></div>
    </div>';
    // make h5 text color white
    echo '<style>
    h5,h3,.h5{
        color: white;
    }
    </style>';
    }
    ?>
    <div class="container-fluid" style="background-color: transparent; padding: 5px; height:80vh;">
        <div class="row" style="background-color: transparent;">
            <div class="col-xl-3 col-12 card left-content">
                <div class="card-body current-weather shadow-add" style="border-bottom: 0;">
                    <h3>Now</h3>
                    <div class="card-title">
                        <div class="row">
                            <div class="col-sm-6 col-6" style="font-size: 2.8rem;" data-current-temperature>
                                <?php echo $weather->currentWeatherData['temperature'] ?>&deg;c
                                <div class="description" style="font-size: 1.1rem;" data-current-description>
                                    <?php echo $weather->currentWeatherData['description'] ?>
                                </div>
                            </div>
                            <div class="col-sm-6 col-6">
                                <img src="./assets/images/weather_icons/animated/<?php echo $weather->currentWeatherData['icon'] ?>" width="120" height="120" alt="">
                            </div>
                        </div>

                    </div>
                    <!-- </hr> -->
                    <p class="card-text">
                    <ul class="meta-list border-bottom-0">
                        <li class="meta-item">
                            <span class="m-icon">calendar_today</span>

                            <p class="title-3 meta-text">
                                <?php echo $weather->currentWeatherData['time'] ?>
                            </p>
                        </li>
                        <li class="weather_add" style="display: flex; justify-content: space-between;">
                            <div class="meta-item">
                                <span class="m-icon">location_on</span>

                                <p class="title-3 meta-text">
                                    <?php echo $weather->city . ' ' . $weather->country ?>
                                </p>
                            </div>
                            <?php
                            if (isset($email)) {
                                $sql = $database->currentregion($email);
                                $row = mysqli_fetch_assoc($sql);
                                $lat = $row['lat'];
                                $lon = $row['lon'];
                                if ($lat != $weather->latitude && $lon != $weather->longitude) {
                                    if ($database->checkregion($email, $weather->latitude, $weather->longitude)) {
                                        echo '<button class="favourite" onclick="removeRegion()" style="color: white; background-color:transparent;font-size:0.5rem;">
                                        <span class="material-symbols-outlined" style="font-size:20px">star</span></button>';
                                        echo "<style>
                                        .material-symbols-outlined {
                                        font-variation-settings:
                                        'FILL' 1,
                                        'wght' 400,
                                        'GRAD' 0,
                                        'opsz' 48
                                        }
                                        .material-symbols-outlined:hover {
                                        font-variation-settings:
                                        'FILL' 0,
                                        'wght' 400,
                                        'GRAD' 0,
                                        'opsz' 48
                                        }
                                        </style>";
                                    } else {
                                        echo '<button class="favourite" onclick="addRegion()" style="color: white; background-color:transparent;font-size:0.5rem;">
                                        <span class="material-symbols-outlined" style="font-size:20px">star</span></button>';
                                        echo "<style>
                                        .material-symbols-outlined {
                                            font-variation-settings:
                                            'FILL' 0,
                                            'wght' 400,
                                            'GRAD' 0,
                                            'opsz' 48
                                            }
                                        .material-symbols-outlined:hover {
                                        font-variation-settings:
                                        'FILL' 1,
                                        'wght' 400,
                                        'GRAD' 0,
                                        'opsz' 48
                                        }
                                        </style>";
                                    }
                                }
                            }
                            ?>

                        </li>
                    </ul>
                    </p>
                </div>
                <div class="card-body daily-forecast shadow-add" style="border-top: 0;" data-daily-forecast>
                    <div class="card-body">
                        <div class="card-title" style="padding-bottom: 2%;">
                            <div class="h5">7 Day Forecast</div>
                        </div>
                        <ul>
                            <?php
                            for ($i = 0; $i < 7; $i++) {
                                echo '
                                <li class="daily-forecast-item border-0">
                                <div class="daily-temperature-img">
                                    <img src="./assets/images/weather_icons/animated/' . $weather->dailyWeatherData['icon'][$i] . '" width="26" height="26" alt="">
                                    <div class="daily-temp">' . $weather->dailyWeatherData['temperatureMax'][$i] . '&deg;c</div>
                                    <div class="daily-temp" style="padding-left:1rem;padding-right:1rem">' . $weather->dailyWeatherData['temperatureMin'][$i] . '&deg;c</div>
                                </div>

                                <div class="forecast-date">' . $weather->dailyWeatherData['date'][$i] . '</div>
                                <div class="forecast-day">' . $weather->dailyWeatherData['day'][$i] . '</div>
                            </li>
                                
                                ';
                            }

                            ?>

                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 col-12 card right-content slider-container overflow-auto" style="background-color:transparent;">
                <div class="col card shadow-add" style="background-color:transparent;">
                    <h5 class="p-3">Today's Highlights</h5>
                    <div class="air-quality-index col card shadow-add">
                        <div class="title-m_icon row">
                            <div class="highlight-title col-lg-2 col-3">Air Quality Index</div>
                            <div class="col-lg-2 col-2">
                                <span class="m-icon">air</span>
                            </div>
                        </div>
                        <div class="aqi-container">
                            <div class="col">
                                <div class="row">
                                    <div class="col-lg-3 col-6">
                                        <div class="aqi-block">
                                            <div class="aqi-title">PM<sub>2.5</sub></div>
                                            <div class="aqi-value">
                                                <?php echo $weather->currentAirQualityData['pm2_5']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="aqi-block">
                                            <div class="aqi-title">SO<sub>2.5</sub></div>
                                            <div class="aqi-value">
                                                <?php echo $weather->currentAirQualityData['so2']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="aqi-block">
                                            <div class="aqi-title">NO<sub>2</sub></div>
                                            <div class="aqi-value">
                                                <?php echo $weather->currentAirQualityData['no2']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="aqi-block">
                                            <div class="aqi-title">O<sub>3</sub></div>
                                            <div class="aqi-value">
                                                <?php echo $weather->currentAirQualityData['o3']; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>


                    </div>
                    <div class="sunrise-sunset col card">
                        <div class="title-m_icon row">
                            <div class="highlight-title col-lg-2 col-4">
                                <h5>Sunset & Sunrise</h5>
                            </div>
                        </div>
                        <!-- <div class="aqi-container"> -->
                        <div class="row">
                            <div class="col-lg">
                                <div class="col card shadow-add">


                                    <div class="col-md-auto" style="padding: 0; margin-right: 0;">
                                        <span class="m-icon">Clear_day</span>
                                    </div>
                                    <div class="col-md-auto" style="margin-left: 10%;">
                                        <p class="label-1">Sunrise</p>
                                        <p class="title-1">
                                            <?php echo $weather->currentWeatherData['sunrise']; ?>
                                        </p>
                                    </div>

                                </div>
                            </div>
                            <!-- <div class="col-lg-3 col-6"> -->
                            <div class="col-lg">
                                <div class="col card shadow-add">
                                    <!-- <div class="col card aqi-block"> -->
                                    <div class="row-md">
                                        <span class="m-icon">Clear_night</span>
                                    </div>

                                    <div class="row-md" style="margin-left: 10%;">
                                        <p class="label-1">Sunset</p>
                                        <p class="title-1">
                                            <?php echo $weather->currentWeatherData['sunset']; ?>
                                        </p>
                                    </div>

                                    <!-- <div class=" aqi-value">Sunset</div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row highlights ">
                        <div class="humidity highlight-card card col-md-5 col-10 shadow-add">

                            <div class="row">
                                <div class="title-m_icon col-sm-8 col-7">
                                    <h5 class="highlight-title row-lg-6 col-6" style="font-weight: 400">
                                        Humidity</h5>
                                    <div class="col-lg-6 row-6">
                                        <span class="m-icon">humidity_percentage</span>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-5">
                                    <div class="highlight-value">
                                        <?php echo $weather->currentWeatherData['humidity']; ?>%
                                    </div>
                                </div>

                            </div>



                        </div>
                        <div class="pressure highlight-card card col-md-5 col-10 shadow-add">
                            <div class="row">
                                <div class="title-m_icon col-sm-6 col-6">
                                    <h5 class="highlight-title row-lg-6 col-6" style="font-weight: 400">
                                        Pressure</h5>
                                    <div class="col-lg-6 row-6">
                                        <span class="m-icon">airwave</span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-6">
                                    <div class="highlight-value">
                                        <?php echo $weather->currentWeatherData['pressure']; ?>hPa
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="visibility highlight-card card col-md-5 col-10 shadow-add">
                            <div class="row">
                                <div class="title-m_icon col-sm-6 col-6">
                                    <h5 class="highlight-title row-lg-6 col-6" style="font-weight: 400">
                                        Visibility</h5>
                                    <div class="col-lg-6 row-6">
                                        <span class="m-icon">visibility</span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-6">
                                    <div class="highlight-value">
                                        <?php echo $weather->currentWeatherData['visibility']; ?>km
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="feels-like highlight-card card col-md-5 col-10 shadow-add">
                            <div class="row">
                                <div class="title-m_icon col-sm-6 col-6">
                                    <h5 class="highlight-title row-lg-6 col-6" style="font-weight: 400">
                                        Feels like</h5>
                                    <div class="col-lg-6 row-6">
                                        <span class="m-icon">thermostat</span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-6">
                                    <div class="highlight-value">
                                        <?php echo $weather->currentWeatherData['feelsLike']; ?>°C
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row section hourly-forecast" style="background-color: transparent;">
                        <h5 class="p-5">Today At</h5>
                        <div class="slider-container" style="padding: 3rem;">
                            <ul class="slider-container row d-flex flex-row flex-nowrap overflow-auto slider-list" data-temp>
                                <?php
                                for ($i = 0; $i < 24; $i++) {
                                    echo '
                                    <li class="col-xl-3 col-lg-3  col-md-3 col-5" style="padding: 1rem;">
                                    <div class="hourly-card row shadow-add" style="padding-bottom:2rem;">
                                        <div class="hourly-time-icon row">
                                            <div class="time col-sm-6 col-12">' . $weather->hourlyWeatherData['time'][$i] . '</div>
                                            <img class="hourly-icon col-sm-6 col-12"
                                                src="./assets/images/weather_icons/animated/' . $weather->hourlyWeatherData['icon'][$i] . '"
                                        </div>
                                        <div class="hourly-temp row">
                                            <div class="temp col-sm-6 col-12" style="font-weight:600; padding-bottom:0.5rem">' . $hourlyWeather['temperature'][$i] . '&deg;C</div>
                                        </div>
                                    </div>
                                    <div class="hourly-card row ">
                                        <div class="hourly-time-icon row">
                                            <div class="time col-sm-6 col-12">' . $weather->hourlyWeatherData['time'][$i] . '</div>
                                            
                                            <img class="wind-icon col-sm-6 col-12"
                                                src="./assets/images/weather_icons/direction.png" height="48" width="48" style="transform: rotate(' . $weather->hourlyWeatherData['windDirection'][$i] - 180 . 'deg)">
                                        </div>
                                        <div class="hourly-wind row">
                                            <div class="wind col-sm-6 col-12"><br>' . round($weather->hourlyWeatherData['windSpeed'][$i]) . '<br>km/hr</div>
                                        </div>
                                    </div>
                                    </li>
                                    ';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
<script>
    <?php
    if (isset($email)) {
    ?>
        var email = "<?php echo $email; ?> ";
        $(document).ready(function() {
            var searchInput = $("#keyword");
            searchInput.on("keyup", function() {
                var keyword = $(this).val();
                $.ajax({
                    url: "validate.php",
                    type: "POST",
                    data: {
                        keyword: keyword,
                        email: email
                    },
                    success: function(data) {
                        $("#result").html(data);

                    }
                });
            });
        });
        <?php
    } else { ?>$(document).ready(function() {
            var searchInput = $("#keyword");
            searchInput.on("keyup", function() {
                var keyword = $(this).val();
                $.ajax({
                    url: "validate.php",
                    type: "POST",
                    data: {
                        keyword: keyword
                    },
                    success: function(data) {
                        $("#result").html(data);
                        // console.log(data);

                    }
                });
            });
        });;
    <?php }
    ?>

    var lat = "<?php echo $weather->latitude; ?> ";
    var lon = "<?php echo $weather->longitude; ?> ";

    function addRegion() {
        $.ajax({
            url: "addRegion.php",
            type: "POST",
            data: {
                email: email,
                lat: lat,
                lon: lon
            },
            success: function(data) {
                $("#result").html(data);
                // console.log(data);

            }
        });

        $(".weather_add").html(
            '<div class="meta-item"><span class="m-icon">location_on</span><p class="title-3 meta-text"><?php echo $weather->city . ' ' . $weather->country ?></p></div><button class="favourite" onclick="removeRegion()" style="color: white; background-color:transparent;font-size:0.5rem;"><span class="material-symbols-outlined" style="font-size:20px">star</span></button>'
        );
        $(".material-symbols-outlined").css("font-variation-settings", "'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 48");

    }

    function removeRegion() {
        $.ajax({
            url: "removeRegion.php",
            type: "POST",
            data: {
                email: email,
                lat: lat,
                lon: lon
            },
            success: function(data) {
                console.log(data);

            }
        });

        $(".weather_add").html(
            '<div class="meta-item"><span class="m-icon">location_on</span><p class="title-3 meta-text"><?php echo $weather->city . ' ' . $weather->country ?></p></div><button class="favourite" onclick="addRegion()" style="color: white; background-color:transparent;font-size:0.5rem;"><span class="material-symbols-outlined" style="font-size:20px">star</span></button>'
        );
        $(".material-symbols-outlined").css("font-variation-settings", "'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 48");

    }

    function signout() {
        $.ajax({
            url: "signout.php",
            type: "POST",
            data: {
                email: email
            },
            success: function(data) {
                $("#result").html(data);
                // console.log(data);

            }
        });

        window.location.href = "login.php";

    }
</script>

</html>