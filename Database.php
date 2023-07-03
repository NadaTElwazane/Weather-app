<?php
class Database
{
    public $servername;
    public $username;
    public $pw;
    public $dbname;
    public $conn;


    public function __construct($servername, $username, $pw, $dbname)
    {
        $this->servername = $servername;
        $this->username = $username;
        $this->pw = $pw;
        $this->dbname = $dbname;
        $this->conn = new mysqli($servername, $username, $pw, $dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }


    public function insert($firstname, $lastname, $email, $password, $lon, $lat)
    {
        $find = "INSERT INTO registereduser(fname,lname,email,password,lon,lat) VALUES ('$firstname','$lastname','$email','$password','$lon','$lat')";
        if ($this->conn->query($find) === TRUE) {
        }

    }

    public function checkduplicates($email)
    {
        $sql = "SELECT * FROM registereduser WHERE email='$email'";
        $result = $this->conn->query($sql);
        $numrows = mysqli_num_rows($result);
        //if more than one return false otherwise return true
        if ($numrows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteregion($email, $lat, $lon)
    {
        $sql = "DELETE FROM regions WHERE email='$email' AND lat='$lat' AND lon='$lon'";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function addregion($email, $lat, $lon)
    {
        $sql = "INSERT INTO regions(email,lat,lon) VALUES ('$email','$lat','$lon')";
        $result = $this->conn->query($sql);
        return $result;
    }

    public function currentregion($email)
    {
        $sql = "SELECT * FROM registereduser WHERE email='$email'";
        $result = $this->conn->query($sql);
        return $result;


    }
    public function userexists($email, $password)
    {
        $sql = "SELECT * FROM registereduser WHERE email='$email' AND `password`='$password'";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }


    }
    public function userinfo($email)
    {
        $sql = "SELECT * FROM registereduser WHERE email='$email'";
        $result = $this->conn->query($sql);
        $result = $result->fetch_assoc();
        return $result;
    }
    public function searchregions($email)
    {
        $sql = "SELECT * FROM regions WHERE email='$email'";
        $result = $this->conn->query($sql);
        return $result;
    }
    public function checkregion($email, $lat, $lon)
    {
        $sql = "SELECT * FROM regions WHERE email='$email' AND lat='$lat' AND lon='$lon'";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }


}

?>