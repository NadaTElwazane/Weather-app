<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="login-signup.css">
</head>

</html>

<?php
include_once 'GeocodingAPI.php';
session_start();
$keyword = $_POST['keyword'];
$email = $_POST['email'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$password = $_POST['password'];

$geocoding=new GeocodingAPI();
$output=$geocoding->displayResults($keyword);

?>

<script>
var email = "<?php echo $email; ?>";
var fname = "<?php echo $fname; ?>";
var lname = "<?php echo $lname; ?>";
var password = "<?php echo $password; ?>";
var keyword = "<?php echo $keyword; ?>";

function selectRow(key) {
    var row = document.getElementById(key);
    var lat = row.cells[1].innerHTML;
    var lon = row.cells[2].innerHTML;
    var name = row.cells[0].innerHTML;
    var country = row.cells[4].innerHTML;
    document.getElementById("table").innerHTML = "<tr><th>Name</th><th>Country</th></tr><tr><td>" + name + "</td><td>" +
        country + "</td></tr>";
    document.getElementById("lat-lon").innerHTML = "<input type='hidden' id='lat' name='lat' value='" + lat +
        "'><input type='hidden' id='lon' name='lon' value='" + lon + "'>";

    $(document).on('submit', function() {
        $.ajax({
            type: "POST",
            url: "compProfile.php",
            data: {
                lat: lat,
                lon: lon,
                email: email,
                fname: fname,
                lname: lname,
                password: password,

            },
            success: function(data) {
                console.log(lat);
                console.log(lon);
            }
        });
    });

}
</script>

