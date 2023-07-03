<?php
include_once "GeocodingAPI.php";

use PHPUnit\Framework\TestCase;

class GeocodingAPITest extends TestCase
{

    protected $geocodingAPI;

    public function setUp() : void
    {
        $this->geocodingAPI = new GeocodingAPI();
    }

    public function testSearchForLocation()
    {
        $keyword = 'London';
        $expectedFirstResult= array(
            'id' => 2643743,
            'name' => 'London',
            'latitude' => 51.50853,
            'longitude' => -0.12574,
            'elevation' => 25.0,
            'feature_code' => 'PPLC',
            'country_code' => 'GB',
            'admin1_id' => 6269131,
            'admin2_id' => 2648110,
            'timezone' => 'Europe/London',
            'population' => 7556900,
            'country_id' => 2635167,
            'country' => 'United Kingdom',
            'admin1' => 'England',
            'admin2' => 'Greater London'
        );
        $actualResult = $this->geocodingAPI->searchForLocation($keyword);
        $firstResult = $actualResult['results'][0];
        $this->assertEquals($expectedFirstResult, $firstResult);
    }
    public function testSearchforLocationNonexistent(){
        $keyword = 'asdfghjllkjhgfddfghjl';
        $output=$this->geocodingAPI->searchForLocation($keyword);
        $this->assertArrayNotHasKey('results', $output);
        
    }
    public function testDisplayResults()
    {
        $keyword = 'London';
        $expectedOutput = '<table id="table">
        <tr>
        <th>Name</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Country Code</th>
        <th>Country</th>
        <th>Add</th>
        </tr>
        <tr id="0">
        <td>London</td>
        <td>51.50853</td>
        <td>-0.12574</td>
        <td>GB</td>
        <td>United Kingdom</td>
        <td><a href="javascript:selectRow(0);">+</a></td>
        </tr>
        </table>';
        $expectedOutput = preg_replace('/\s+/', '', $expectedOutput); // remove whitespace
        $actualOutput = $this->geocodingAPI->displayResults($keyword,1);
        $actualOutput = preg_replace('/\s+/', '', $actualOutput); // remove whitespace
        $this->assertEquals($expectedOutput, $actualOutput);
    }
    public function testDisplayResultsNonexistent(){
        $keyword = 'asdfghjllkjhgfddfghjl';
        $expectedOutput = 'no results';
        $expectedOutput = preg_replace('/\s+/', '', $expectedOutput); // remove whitespace
        $actualOutput = $this->geocodingAPI->displayResults($keyword,1);
        $actualOutput = preg_replace('/\s+/', '', $actualOutput); // remove whitespace
        $this->assertEquals($expectedOutput, $actualOutput);
    }
}


