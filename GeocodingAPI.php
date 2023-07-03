<?php

class GeocodingAPI
{
    public function searchForLocation($keyword, $count = 10)
    {
        $ch = curl_init();
        if (strpos($keyword, ' ') !== false) {
            $keyword = str_replace(' ', '+', $keyword);
        }
        curl_setopt($ch, CURLOPT_URL, "https://geocoding-api.open-meteo.com/v1/search?name=" . $keyword . "&count=" . $count . "&language=en&format=json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }
    public function displayResults($keyword, $count = 10)
    {
        $output = $this->searchForLocation($keyword,$count);
        $returnString="";
        if (strpos(json_encode($output), 'result') != false) {
            $returnString.='<table id="table">
                    <tr>
                    <th>Name</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Country Code</th>
                    <th>Country</th>
                    <th>Add</th>
                    </tr>';
            echo '
            <table id="table">
            <tr>
            <th>Name</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Country Code</th>
            <th>Country</th>
            <th>Add</th>
            </tr>
         
            ';
            foreach ($output['results'] as $key => $value) {
                echo '<tr id="' . $key . '">';
                $returnString.='<tr id="' . $key . '">';
                if (isset($value['name'])) {
                    echo ' <td>' . $value['name'] . '</td>';
                    $returnString.=' <td>' . $value['name'] . '</td>';
                } else {
                    echo ' <td> </td>';
                    $returnString.=' <td> </td>';
                }
                if (isset($value['latitude'])) {
                    echo ' <td>' . $value['latitude'] . '</td>';
                    $returnString.=' <td>' . $value['latitude'] . '</td>';
                } else {
                    echo ' <td> </td>';
                    $returnString.=' <td> </td>';
                }
                if (isset($value['longitude'])) {
                    echo ' <td>' . $value['longitude'] . '</td>';
                    $returnString.=' <td>' . $value['longitude'] . '</td>';
                } else {
                    echo ' <td> </td>';
                    $returnString.=' <td> </td>';
                }
                if (isset($value['country_code'])) {
                    echo ' <td>' . $value['country_code'] . '</td>';
                    $returnString.=' <td>' . $value['country_code'] . '</td>';
                } else {
                    echo ' <td> </td>';
                    $returnString.=' <td> </td>';
                }
                if (isset($value['country'])) {
                    echo ' <td>' . $value['country'] . '</td>';
                    $returnString.=' <td>' . $value['country'] . '</td>';
                } else {
                    echo ' <td> </td>';
                    $returnString.=' <td> </td>';
                }
                echo '<td><a href="javascript:selectRow(' . $key . ');">+</a></td>';
                $returnString.='<td><a href="javascript:selectRow(' . $key . ');">+</a></td>';
                echo '</tr>';
                $returnString.='</tr>';
            }
            echo '   </table>';
            $returnString.='</table>';
        } else {
            echo "no results";
            $returnString.="no results";
        }
        return $returnString;
    }

}
