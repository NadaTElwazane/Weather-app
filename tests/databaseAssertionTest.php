<?php

include_once 'Database.php';
use PHPUnit\Framework\TestCase;

class databaseAssertionTest extends TestCase
{
    /**
     * @var Database
     */
    protected $database;

    public function setUp(): void
    {
        $this->database = new Database('localhost', 'root', '', 'test');
    }

    /**
     * @test
     */
    public function testInsert()
    {
        $firstname = 'fake';
        $lastname = 'fake';
        $email = 'fake@fake.com';
        $password = 'password';
        $lon = 123.456;
        $lat = 789.012;

        $this->database->insert($firstname, $lastname, $email, $password, $lon, $lat);

        $this->assertTrue($this->database->checkduplicates($email));
    }

    /**
     * @test
     */
    public function testAddregion()
    {
        $email = 'fake@fake.com';
        $lat = 80.456;
        $lon = 97.012;

        $this->database->addregion($email, $lat, $lon);

        $this->assertTrue($this->database->checkregion($email, $lat, $lon));
    }

    /**
     * @test
     */
    public function testDeleteregion()
    {
        $email = 'fake@fake.com';
        $lat = 80.456;
        $lon = 97.012;

        $this->database->deleteregion($email, $lat, $lon);

        $this->assertFalse($this->database->checkregion($email, $lat, $lon));
    }



    /**
     * @test
     */
    public function testUserexists()
    {
        $email = 'fake@fake.com';
        $password = 'password';

        $this->assertTrue($this->database->userexists($email, $password));
    }

    /**
     * @test
     */
    public function testUserinfo()
    {
        $email = 'fake@fake.com';

        $userinfo = $this->database->userinfo($email);

        $this->assertArrayHasKey('fname', $userinfo);
        $this->assertArrayHasKey('lname', $userinfo);
        $this->assertArrayHasKey('email', $userinfo);
        $this->assertArrayHasKey('password', $userinfo);
        $this->assertArrayHasKey('lon', $userinfo);
        $this->assertArrayHasKey('lat', $userinfo);
    }

    /**
     * @test
     */
    public function testSearchregions()
    {
        $email = 'fake@fake.com';
        $lat = 80.456;
        $lon = 79.012;

        $this->database->addregion($email, $lat, $lon);

        $regions = $this->database->searchregions($email)->fetch_assoc();

        $this->assertIsArray($regions);
        // foreach ($regions as $region) {
        $this->assertArrayHasKey('email', $regions);
        $this->assertArrayHasKey('lat', $regions);
        $this->assertArrayHasKey('lon', $regions);
        // }
    }


}

?>