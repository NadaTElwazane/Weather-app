create database weatherforecast;
use weatherforecast;

create table registereduser(
    fname varchar(225) not null,
    lname varchar(225) not null,
    email varchar(225) not null primary key,
    `password` varchar(225) not null,
    lon varchar(225) not null,
    lat varchar(225) not null
);

create table regions(
    email varchar(225) not null,
    primary key(email,lon,lat),
    lon varchar(225) not null,
    lat varchar(225) not null
);

-- alter table regions add foreign key(email) references user(email);
