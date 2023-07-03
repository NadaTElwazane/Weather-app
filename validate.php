<?php

session_start();

$keyword = $_POST['keyword'];
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $_SESSION['email'] = $email;
}

$output = searchForLocation($keyword);

if (strpos(json_encode($output), 'result') != false) {
    echo '<datalist>';
    foreach ($output['results'] as $key => $value) {
        if (isset($value['name'])) {
            echo '
            <option id="' . $key . '" value="' . $value['country_code'] . '" data-value="' . $value['latitude'] . ',' . $value['longitude'] . '">' . $value['name'] . '</option>
            ';
        }
    }
    echo '
    </datalist>
    ';

} else {

    echo "no results";
}

function searchForLocation($keyword)
{

    // Create a cURL object
    $ch = curl_init();
    if (strpos($keyword, ' ') !== false) {
        $keyword = str_replace(' ', '+', $keyword);
    }
    // Set the cURL options
    curl_setopt($ch, CURLOPT_URL, "https://geocoding-api.open-meteo.com/v1/search?name=" . $keyword . "&count=10&language=en&format=json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Execute the cURL request
    $output = curl_exec($ch);

    // Close the cURL object
    curl_close($ch);

    // Decode the JSON response
    $output = json_decode($output, true);

    // Return the results
    return $output;

}

?>

<script>
// var country_code = "";

function selectRow(key) {
    console.log(key);
    list = document.getElementById('result');
    options = document.querySelectorAll('#result option[value="' + key + '"]'), hiddenInput = document.getElementById(
        'result' + '-hidden');
    // get option with value
    var option = document.querySelector('#result option[value="' + key + '"]');
    // get lat and lon from option check if there are options
    if (options.length > 0) {
        var lat = option.getAttribute('data-value').split(',')[0];
        var lon = option.getAttribute('data-value').split(',')[1];
        console.log(lat);
        console.log(lon);
        window.location.href = "index.php?latitude=" + lat + "&longitude=" + lon;

    }

}

// $(document).ready(function () {
//     // Get the select element
//     var selectElement = document.getElementById("result");

//     // Attach an event listener to the change event
//     selectElement.addEventListener("change", function () {
//         // Get the value of the selected option
//         var selectedValue = this.value;

//         // Call the selectRow function with the value of the selected option
//         selectRow(selectedValue);
//     });
// });
</script>