<?php

include_once('WeatherAPI.php');
include_once('Database.php');

use PHPUnit\Framework\TestCase;

class weatherAPITest extends TestCase
{
 
    protected $weatherAPI;

    public function setUp(): void
    {
        $WeatherParameters = array(
            'hourly' => array('temperature_2m', 'relativehumidity_2m', 'apparent_temperature', 'weathercode', 'surface_pressure', 'visibility', 'windspeed_10m', 'winddirection_10m', 'uv_index'),
            'daily' => array('weathercode', 'sunrise', 'sunset', 'uv_index_max', 'temperature_2m_max', 'temperature_2m_min')
        );

        $AirQualityParameters = array(
            'hourly' => array('pm2_5', 'nitrogen_dioxide', 'sulphur_dioxide', 'ozone')
        );
        $this->weatherAPI = new WeatherAPI($WeatherParameters, $AirQualityParameters, 31.20176, 29.91582);
    }

    /**
     * @test
     */
    
    public function testCreateWeatherURL()
    {
        $this->assertEquals('https://api.open-meteo.com/v1/forecast?latitude=' . $this->weatherAPI->latitude . '&longitude=' . $this->weatherAPI->longitude . '&hourly=temperature_2m,relativehumidity_2m,apparent_temperature,weathercode,surface_pressure,visibility,windspeed_10m,winddirection_10m,uv_index&daily=weathercode,sunrise,sunset,uv_index_max,temperature_2m_max,temperature_2m_min&current_weather=true&timeformat=unixtime&timezone=auto', $this->weatherAPI->createWeatherURL());
    }

    /**
     * @test
     */
    public function testCreateAirQualityURL()
    {
        $this->assertEquals('https://air-quality-api.open-meteo.com/v1/air-quality?latitude=' . $this->weatherAPI->latitude . '&longitude=' . $this->weatherAPI->longitude . '&hourly=pm2_5,nitrogen_dioxide,sulphur_dioxide,ozone&timeformat=unixtime', $this->weatherAPI->createAirQualityURL());
    }

    /**
     * @test
     */
    public function testCallWeatherAPI()
    {
        $weatherReturn = $this->weatherAPI->callWeatherAPI();
        $this->assertIsArray($weatherReturn);
        $this->assertArrayHasKey('current_weather', $weatherReturn);
        $this->assertArrayHasKey('hourly', $weatherReturn);
        $this->assertArrayHasKey('daily', $weatherReturn);
    }

    /**
     * @test
     */
    public function testCallAirQualityAPI()
    {
        $airQualityReturn = $this->weatherAPI->callAirQualityAPI();
        $this->assertIsArray($airQualityReturn);
        $this->assertArrayHasKey('hourly', $airQualityReturn);
    }

    public function testCurrentWeatherFormat()
    {
        $this->weatherAPI->callWeatherAPI();
        $currentWeather = $this->weatherAPI->getCurrentWeather();
        $this->assertIsArray($currentWeather);
        $this->assertArrayHasKey('time', $currentWeather);
        $this->assertArrayHasKey('temperature', $currentWeather);
        $this->assertEquals(0, strlen(substr(strrchr($currentWeather['temperature'], "."), 1)));
        $this->assertArrayHasKey('description', $currentWeather);
        $this->assertArrayHasKey('icon', $currentWeather);
        $this->assertArrayHasKey('pressure', $currentWeather);
        $this->assertArrayHasKey('feelsLike', $currentWeather);
        $this->assertArrayHasKey('visibility', $currentWeather);
        $this->assertEquals(0, strlen(substr(strrchr($currentWeather['feelsLike'], "."), 1)));
        $this->assertArrayHasKey('humidity', $currentWeather);
        $this->assertArrayHasKey('sunset', $currentWeather);
        $this->assertGreaterThanOrEqual(7, strlen($currentWeather['sunset']));
        $this->assertArrayHasKey('sunrise', $currentWeather);
        $this->assertGreaterThanOrEqual(7, strlen($currentWeather['sunrise']));
        $this->assertArrayHasKey('timezone', $currentWeather);
        $this->assertArrayHasKey('city', $currentWeather);
        $this->assertArrayHasKey('country', $currentWeather);
        foreach ($currentWeather as $key => $value) {
            $this->assertNotNull($value);
        }
    }

    public function testAirQuality()
    {
        $this->weatherAPI->callAirQualityAPI();
        $airQuality = $this->weatherAPI->getCurrentAirQuality();
        $this->assertIsArray($airQuality);
        $this->assertArrayHasKey('pm2_5', $airQuality);
        $this->assertArrayHasKey('no2', $airQuality);
        $this->assertArrayHasKey('so2', $airQuality);
        $this->assertArrayHasKey('o3', $airQuality);
        foreach ($airQuality as $key => $value) {
            $this->assertNotNull($value);
        }
    }

    public function testGet7DayWeatherArray()
    {
        $this->weatherAPI->callWeatherAPI();
        $daily = $this->weatherAPI->getWeatherData()['daily'];
        $this->assertIsArray($daily);
        $weatherArray = $this->weatherAPI->get7DayWeatherArray($daily, 'temperature_2m_max');
        $this->assertIsArray($weatherArray);
        $this->assertCount(7, $weatherArray);

    }

    public function testGetWeatherValueNow()
    {
        $this->weatherAPI->callWeatherAPI();
        $hourly = $this->weatherAPI->getWeatherData()['hourly'];
        $this->assertIsArray($hourly);
        $weatherValue = $this->weatherAPI->getWeatherValueNow($hourly, 'surface_pressure');
        $this->assertIsNumeric($weatherValue);
        $this->assertGreaterThanOrEqual(870, $weatherValue);
    }
    public function testGet24HourWeatherArray()
    {
        $this->weatherAPI->callWeatherAPI();
        $hourly = $this->weatherAPI->getWeatherData()['hourly'];
        $this->assertIsArray($hourly);
        $weatherArray = $this->weatherAPI->get24HourWeatherArray($hourly, 'temperature_2m');
        $this->assertIsArray($weatherArray);
        $this->assertCount(24, $weatherArray);
    }

    public function testGetDailyWeather()
    {
        $this->weatherAPI->callWeatherAPI();
        $daily = $this->weatherAPI->getDailyWeather();
        $this->assertIsArray($daily);
        $this->assertArrayHasKey('icon', $daily);
        $this->assertArrayHasKey('time', $daily);
        $this->assertArrayHasKey('temperatureMax', $daily);
        $this->assertArrayHasKey('temperatureMin', $daily);
        $this->assertArrayHasKey('day', $daily);
        $this->assertArrayHasKey('date', $daily);
        foreach ($daily as $key => $value) {
            $this->assertCount(7, $value);
            foreach ($value as $key => $value) {
                $this->assertNotNull($value);
            }
        }

    }

    public function testGetNextHour()
    {
        $this->weatherAPI->callWeatherAPI();
        $hourly = $this->weatherAPI->getWeatherData()['hourly'];
        $this->assertIsArray($hourly);
        $nextHour = $this->weatherAPI->getNextHour($hourly);
        $this->assertIsNumeric($nextHour);
    }

    public function testGetHourlyWeather()
    {
        $this->weatherAPI->callWeatherAPI();
        $hourly = $this->weatherAPI->getHourlyWeather();
        $this->assertIsArray($hourly);
        $this->assertArrayHasKey('icon', $hourly);
        $this->assertArrayHasKey('time', $hourly);
        $this->assertArrayHasKey('temperature', $hourly);
        $this->assertArrayHasKey('windSpeed', $hourly);
        $this->assertArrayHasKey('windDirection', $hourly);

        foreach ($hourly as $key => $value) {
            $this->assertCount(24, $value);
            foreach ($value as $key => $value) {
                $this->assertNotNull($value);
            }
        }
    }

    public function testReverseGeoCoding()
    {
        $latitude = 51.50853;
        $longitude = -0.12574;

        $this->weatherAPI->reverseGeoCoding($latitude, $longitude);
        $cityName = $this->weatherAPI->city;
        $countryCode = $this->weatherAPI->country;

        $this->assertEquals('Greater London', $cityName);
        $this->assertEquals('GB', $countryCode);
    }
    public function testGetWeatherIcon()
    {
        // Test for clear sky
        $weatherCode = 0;
        $isDay = true;
        $expectedIcon = 'clear-day.svg';
        $actualIcon = $this->weatherAPI->getWeatherIcon($weatherCode, $isDay);
        $this->assertEquals($expectedIcon, $actualIcon);

        // Test for overcast
        $weatherCode = 3;
        $isDay = true;
        $expectedIcon = 'overcast-day.svg';
        $actualIcon = $this->weatherAPI->getWeatherIcon($weatherCode, $isDay);
        $this->assertEquals($expectedIcon, $actualIcon);

        // Test for rain
        $weatherCode = 61;
        $isDay = true;
        $expectedIcon = 'rain.svg';
        $actualIcon = $this->weatherAPI->getWeatherIcon($weatherCode, $isDay);
        $this->assertEquals($expectedIcon, $actualIcon);
    }

    public function testGetWeatherDescription()
    {
        // Test for clear sky
        $weatherCode = 0;
        $expectedDescription = 'Clear sky';
        $actualDescription = $this->weatherAPI->getWeatherDescription($weatherCode);
        $this->assertEquals($expectedDescription, $actualDescription);

        // Test for overcast
        $weatherCode = 3;
        $expectedDescription = 'Overcast';
        $actualDescription = $this->weatherAPI->getWeatherDescription($weatherCode);
        $this->assertEquals($expectedDescription, $actualDescription);

        // Test for rain
        $weatherCode = 61;
        $expectedDescription = 'Rain: Slight intensity';
        $actualDescription = $this->weatherAPI->getWeatherDescription($weatherCode);
        $this->assertEquals($expectedDescription, $actualDescription);
    }

    public function testGetCurrentLocation(){
        $this->weatherAPI->getCurrentLocation();
        $this->assertIsString($this->weatherAPI->city);
        $this->assertIsString($this->weatherAPI->country);
    }
}