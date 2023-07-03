<!-- class for API Open-meteo -->

<?php
use GeoTimeZone\Calculator;

require_once('Database.php');
class WeatherAPI
{
    private $weatherData;
    private $airQualityData;
    public $latitude;
    public $longitude;
    public $city;
    public $country;
    private $weatherURL;
    private $airQualityURL;
    private $timezone;
    private $parametersWeather;
    private $parametersAirQuality;
    public $currentWeatherData;
    public $currentAirQualityData;
    public $dailyWeatherData;
    public $hourlyWeatherData;
    public $is_day;

    public function __construct($parametersWeather, $parametersAirQuality, $latitude = null, $longitude = null)
    {
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
            $userdatabase = new Database("localhost", "root", "", "weatherforecast");
            $result = $userdatabase->userinfo($email);
            $this->latitude = $result['lat'];
            $this->longitude = $result['lon'];
            echo '<script>console.log(' . $result['lon'] . ');</script>';
            echo '<script>console.log(' . $this->latitude . ');</script>';
            // $this->getClientTimezone();
            $this->reverseGeoCoding($this->latitude, $this->longitude);
        } elseif ($latitude == null && $longitude == null) {
            $this->setLatitudeAndLongitudeAndTimezone();
        } else {
            $this->latitude = $latitude;
            $this->longitude = $longitude;
            $this->getClientTimezone();
            $this->reverseGeoCoding($latitude, $longitude);
        }


        $this->parametersAirQuality = $parametersAirQuality;
        $this->parametersWeather = $parametersWeather;
        $this->weatherURL = $this->createWeatherURL();
        $this->airQualityURL = $this->createAirQualityURL();
    }
    public function getWeatherData()
    {
        return $this->weatherData;
    }

    public function getWeatherURL()
    {
        return $this->weatherURL;
    }
    public function getAirQualityURL()
    {
        return $this->airQualityURL;
    }
    public function getClientTimezone()
    {
        $ip = isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']:'::1';

        if ($ip == '::1') {
            $ip = '41.237.99.154';
        }
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        $timezone = $details->timezone;
        $this->timezone = $timezone;
        return $timezone;
    }

    public function setParameters($parametersAirQuality, $parametersWeather)
    {
        $this->parametersAirQuality = $parametersAirQuality;
        $this->parametersWeather = $parametersWeather;
        $this->weatherURL = $this->createWeatherURL();
    }

    public function createWeatherURL()
    {
        $hourly = $this->parametersWeather['hourly'];
        $daily = $this->parametersWeather['daily'];
        // $current = $this->parametersWeather['current'];

        $initial_URL = "https://api.open-meteo.com/v1/forecast?";
        // add latitude and longitude
        $initial_URL .= "latitude=" . $this->latitude . "&longitude=" . $this->longitude;
        // add parameters
        // check if hourly is empty
        if (empty($hourly)) {
            // add hourly
            $initial_URL .= "&hourly=";
            $initial_URL .= "&hourly=temperature_2m";
        } else {
            $initial_URL .= "&hourly=" . $hourly[0];
            for ($i = 1; $i < count($hourly); $i++) {
                $initial_URL .= ',' . $hourly[$i];
            }
        }
        // check if daily is empty
        if (empty($daily)) {
            // add daily
            $initial_URL .= "&daily=";
            $initial_URL .= "&daily=temperature_2m";
        } else {
            $initial_URL .= "&daily=" . $daily[0];
            for ($i = 1; $i < count($daily); $i++) {
                $initial_URL .= ',' . $daily[$i];
            }
        }
        // add current_weather=true
        $initial_URL .= "&current_weather=true";
        // timeformat unix
        $initial_URL .= "&timeformat=unixtime";
        $initial_URL .= "&timezone=auto";
        echo '<script>console.log(' . $initial_URL . ');</script>';
        $this->weatherURL = $initial_URL;
        return $initial_URL;
    }

    public function createAirQualityURL()
    {
        $initial_URL = "https://air-quality-api.open-meteo.com/v1/air-quality?";
        $initial_URL .= "latitude=" . $this->latitude . "&longitude=" . $this->longitude;
        $hourly = $this->parametersAirQuality['hourly'];
        if (empty($hourly)) {
            $initial_URL .= "&hourly=";
            $initial_URL .= "&hourly=pm25";
            $initial_URL .= "&timeformat=unixtime";
        } else {
            $initial_URL .= "&hourly=" . $hourly[0];
            for ($i = 1; $i < count($hourly); $i++) {
                $initial_URL .= ',' . $hourly[$i];
            }
            $initial_URL .= "&timeformat=unixtime";
        }
        // print_r($initial_URL);
        $this->airQualityURL = $initial_URL;
        return $initial_URL;
    }

    public function getCurrentLocation()
    {
        $ip = isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']:'::1';
        if ($ip == '::1') {
            $ip = '41.237.99.154';
        }
        $api_url = 'http://ipinfo.io/' . $ip . '/json';
        $json_data = file_get_contents($api_url);
        $response_data = json_decode($json_data);
        $this->timezone = $response_data->timezone;
        $this->city = $response_data->city;
        $this->country = $response_data->country;
        $loc = $response_data->loc;
        // $loc = explode(",", $loc);
        $this->latitude = explode(",", $loc)[0];
        $this->longitude = explode(",", $loc)[1];
        return array(
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timezone' => $this->timezone
        );
    }

    public function setLatitudeAndLongitudeAndTimezone()
    {
        if (isset($_GET['latitude']) && isset($_GET['longitude'])) {
            $this->latitude = $_GET['latitude'];
            $this->longitude = $_GET['longitude'];
            $this->timezone = $this->getClientTimezone();
            $this->reverseGeoCoding($this->latitude, $this->longitude);
        } else {
            $this->getCurrentLocation();
            $this->timezone = $this->getClientTimezone();
        }
    }

    public function APICall($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    public function callWeatherAPI()
    {
        $this->weatherData = $this->APICall($this->weatherURL);
        // print_r($this->weatherData);
        return $this->weatherData;
    }

    public function callAirQualityAPI()
    {
        $this->airQualityData = $this->APICall($this->airQualityURL);
        // print_r($this->airQualityData);
        return $this->airQualityData;
    }

    public function reverseGeoCoding($latitude, $longitude)
    {
        $api_url = 'https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=' . $latitude . '&longitude=' . $longitude . '&localityLanguage=en';
        $api_return_data = file_get_contents($api_url);
        $details = json_decode($api_return_data);
        $city = $details->city;
        $country = $details->countryCode;
        $this->city = $city;
        $this->country = $country;
        // use geonames api to get timezone
        $api_url = 'http://api.geonames.org/timezoneJSON?lat=' . $latitude . '&lng=' . $longitude . '&username=nada';
        $api_return_data = file_get_contents($api_url);
        $details = json_decode($api_return_data);
        $this->timezone = $details->timezoneId;
        echo "<script>console.log('timezone: " . $this->timezone . "');</script>";
        // $this->timezone = $details->timezone;
        return array(
            'cityName' => $city,
            'country' => $country
        );
    }
    public function getWeatherValueNow($hourly, $value)
    {
        $now = time();
        $closest = null;
        for ($i = 0; $i < count($hourly); $i++) {
            $time = $hourly['time'][$i];
            if ($closest === null || abs($now - $time) < abs($now - $closest)) {
                $closest = $time;
                $index = $i;
            }
        }
        return $hourly[$value][$index];
    }

    public function getWeatherDescription($weatherCode)
    {
        $weatherDescription = array(
            0 => "Clear sky",
            1 => "Mainly clear",
            2 => "Partly cloudy",
            3 => "Overcast",
            45 => "Fog",
            48 => "Depositing rime fog",
            51 => "Drizzle: Light intensity",
            53 => "Drizzle: Moderate intensity",
            55 => "Drizzle: Dense intensity",
            56 => "Freezing Drizzle: Light intensity",
            57 => "Freezing Drizzle: Dense intensity",
            61 => "Rain: Slight intensity",
            63 => "Rain: Moderate intensity",
            65 => "Rain: Heavy intensity",
            66 => "Freezing Rain: Light intensity",
            67 => "Freezing Rain: Heavy intensity",
            71 => "Snow fall: Slight intensity",
            73 => "Snow fall: Moderate intensity",
            75 => "Snow fall: Heavy intensity",
            77 => "Snow grains",
            80 => "Rain showers: Slight intensity",
            81 => "Rain showers: Moderate intensity",
            82 => "Rain showers: Violent intensity",
            85 => "Snow showers: Slight intensity",
            86 => "Snow showers: Heavy intensity",
            95 => "Thunderstorm: Slight intensity",
            96 => "Thunderstorm: Slight hail",
            99 => "Thunderstorm: Heavy hail"
        );

        if (array_key_exists($weatherCode, $weatherDescription)) {
            return $weatherDescription[$weatherCode];
        } else {
            return "Clear sky";
        }
    }
    public function getWeatherIcon($weatherCode, $isDay)
    {
        $weatherIcons = array(
            0 => "clear" . ($isDay ? "-day" : "-night") . ".svg",
            1 => "clear" . ($isDay ? "-day" : "-night") . ".svg",
            2 => "cloudy.svg",
            3 => "overcast" . ($isDay ? "-day" : "-night") . ".svg",
            45 => "fog.svg",
            48 => "fog.svg",
            51 => "partly-cloudy" . ($isDay ? "-day" : "-night") . "-drizzle.svg",
            53 => "drizzle.svg",
            55 => "drizzle.svg",
            56 => "drizzle.svg",
            59 => "drizzle.svg",
            61 => "rain.svg",
            63 => "rain.svg",
            65 => "rain.svg",
            66 => "rain.svg",
            67 => "rain.svg",
            71 => "snow.svg",
            73 => "snow.svg",
            75 => "snow.svg",
            77 => "snow.svg",
            80 => "rain.svg",
            81 => "rain.svg",
            82 => "rain.svg",
            85 => "rain.svg",
            86 => "rain.svg",
            95 => "thunderstorms" . ($isDay ? "-day" : "-night") . "-rain.svg",
            96 => "thunderstorms" . ($isDay ? "-day" : "-night") . "-snow.svg",
            99 => "thunderstorms" . ($isDay ? "-day" : "-night") . "-snow.svg"

        );

        if (array_key_exists($weatherCode, $weatherIcons)) {
            return $weatherIcons[$weatherCode];
        } else {
            return "clear-day.svg";
        }
    }
    public function getCurrentWeather()
    {

        $currentWeather = $this->weatherData['current_weather'];
        $currentWeatherData = array();
        $currentWeatherData['time'] = $currentWeather['time'];
        $currentWeatherData['time'] = date("l, F j, Y", $currentWeatherData['time']);
        $currentWeatherData['temperature'] = $currentWeather['temperature'];
        $currentWeatherData['temperature'] = round($currentWeatherData['temperature'], 0);
        $currentWeatherData['description'] = $this->getWeatherDescription($currentWeather['weathercode']);
        $this->is_day=$currentWeather['is_day'];
        $currentWeatherData['icon'] = $this->getWeatherIcon($currentWeather['weathercode'], $currentWeather['is_day']);
        $hourly = $this->weatherData['hourly'];
        $currentWeatherData['pressure'] = $this->getWeatherValueNow($hourly, 'surface_pressure');
        $currentWeatherData['feelsLike'] = $this->getWeatherValueNow($hourly, 'apparent_temperature');
        $currentWeatherData['feelsLike'] = round($currentWeatherData['feelsLike'], 0);
        $currentWeatherData['visibility'] = $this->getWeatherValueNow($hourly, 'visibility');
        $currentWeatherData['visibility'] = $currentWeatherData['visibility'] / 1000;
        $currentWeatherData['visibility'] = round($currentWeatherData['visibility'], 0);
        $currentWeatherData['humidity'] = $this->getWeatherValueNow($hourly, 'relativehumidity_2m');
        $currentWeatherData['sunset'] = $this->weatherData['daily']['sunset'][0];
        // $currentWeatherData['sunset'] = date("g:i A", $currentWeatherData['sunset']);
        // use DateTime class to convert to required timezone
        $sunset = new DateTime();
        $sunset->setTimestamp($currentWeatherData['sunset']);
        $sunset->setTimezone(new DateTimeZone($this->timezone));
        $currentWeatherData['sunset'] = $sunset->format('g:i A');
        $currentWeatherData['sunrise'] = $this->weatherData['daily']['sunrise'][0];
        // $currentWeatherData['sunrise'] = date("g:i A", $currentWeatherData['sunrise']);
        // use DateTime class to convert to required timezone
        $sunrise = new DateTime();
        $sunrise->setTimestamp($currentWeatherData['sunrise']);
        $sunrise->setTimezone(new DateTimeZone($this->timezone));
        $currentWeatherData['sunrise'] = $sunrise->format('g:i A');
        $currentWeatherData['timezone'] = $this->timezone;
        $currentWeatherData['city'] = $this->city;
        $currentWeatherData['country'] = $this->country;
        $this->currentWeatherData = $currentWeatherData;
        return $currentWeatherData;
    }

    public function getCurrentAirQuality()
    {

        $airQuality = $this->airQualityData['hourly'];
        $currentAirQualityData['pm2_5'] = $this->getWeatherValueNow($airQuality, 'pm2_5');
        $currentAirQualityData['no2'] = $this->getWeatherValueNow($airQuality, 'nitrogen_dioxide');
        $currentAirQualityData['so2'] = $this->getWeatherValueNow($airQuality, 'sulphur_dioxide');
        $currentAirQualityData['o3'] = $this->getWeatherValueNow($airQuality, 'ozone');

        $this->currentAirQualityData = $currentAirQualityData;
        return $currentAirQualityData;

    }

    public function get7DayWeatherArray($daily, $value)
    {
        // create an array to store the values
        $weatherArray = array();
        // for loop to get the values for the next 7 days
        for ($i = 0; $i < 7; $i++) {
            // get the value for the day
            $weatherArray[$i] = $daily[$value][$i];
            // round the value
            $weatherArray[$i] = round($weatherArray[$i]);
        }
        // return the array
        return $weatherArray;
    }

    public function getDailyWeather()
    {
        $dailyWeather = $this->weatherData['daily'];
        $dailyWeatherData = array();
        $dailyWeatherData['time'] = $this->get7DayWeatherArray($dailyWeather, 'time');
        $dailyWeatherData['temperatureMax'] = $this->get7DayWeatherArray($dailyWeather, 'temperature_2m_max');
        $dailyWeatherData['temperatureMin'] = $this->get7DayWeatherArray($dailyWeather, 'temperature_2m_min');
        $dailyWeatherData['icon'] = $this->get7DayWeatherArray($dailyWeather, 'weathercode');
        $dailyWeatherData['is_day'] = [1, 1, 1, 1, 1, 1, 1];
        for ($i = 0; $i < 7; $i++) {
            $dailyWeatherData['icon'][$i] = $this->getWeatherIcon($dailyWeatherData['icon'][$i], $dailyWeatherData['is_day'][$i]);
        }
        for ($i = 0; $i < 7; $i++) {
            $dailyWeatherData['day'][$i] = date("l", $dailyWeatherData['time'][$i]);
            $dailyWeatherData['date'][$i] = date("d M", $dailyWeatherData['time'][$i]);
        }
        $this->dailyWeatherData = $dailyWeatherData;
        return $dailyWeatherData;
    }
    public function getNextHour($hourly)
    {
        $currentTime = (int) date('G');
        $currentTime = $currentTime . ":00";
        $currentTime = strtotime($currentTime);
        $nextHourIndex = array_search($currentTime, $hourly['time'], true);
        if ($nextHourIndex === false) {
            $nextHourIndex = 0;
        } else {
            $nextHourIndex++;
        }
        return $nextHourIndex;
    }

    public function get24HourWeatherArray($hourly, $value)
    {
        $index = $this->getNextHour($hourly);
        for ($i = 0; $i < 24; $i++) {
            $weatherArray[$i] = $hourly[$value][$index];
            $index++;
        }
        return $weatherArray;
        // return $i;
    }
    public function getHourlyWeather()
    {
        $hourlyWeather = $this->weatherData['hourly'];
        $hourlyWeatherData = array();
        $hourlyWeatherData['time'] = $this->get24HourWeatherArray($hourlyWeather, 'time');
        $hourlyWeatherData['temperature'] = $this->get24HourWeatherArray($hourlyWeather, 'temperature_2m');
        for ($i = 0; $i < 24; $i++) {
            $hourlyWeatherData['temperature'][$i] = round($hourlyWeatherData['temperature'][$i]);
        }
        $hourlyWeatherData['icon'] = $this->get24HourWeatherArray($hourlyWeather, 'weathercode');
        $hourlyWeather['is_day'] = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];
        for ($i = 0; $i < 24; $i++) {
            $hourlyWeatherData['icon'][$i] = $this->getWeatherIcon($hourlyWeatherData['icon'][$i], $hourlyWeather['is_day'][$i]);
        }
        $hourlyWeatherData['windSpeed'] = $this->get24HourWeatherArray($hourlyWeather, 'windspeed_10m');
        $hourlyWeatherData['windDirection'] = $this->get24HourWeatherArray($hourlyWeather, 'winddirection_10m');
        for ($i = 0; $i < 24; $i++) {
            $hourlyWeatherData['time'][$i] = date("g:i A", $hourlyWeatherData['time'][$i]);
        }
        $this->hourlyWeatherData = $hourlyWeatherData;
        return $hourlyWeatherData;
    }
}



?>