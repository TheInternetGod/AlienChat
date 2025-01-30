<?php
require 'ServerScripts.php';

$dataBaseName = 'chat_app';
$host = 'localhost';
$username = 'KAUSHIK000';
$password = '000KAUSHIK666';

// Initial connection to create database
$connection = mysqli_connect($host, $username, $password);
if ($connection) {
    consolelog("Connected To DataBase Server!");
} else {
    consolelog("Connection Error: " . mysqli_connect_error());
    die();
}

// Create database
$createDB = "CREATE DATABASE IF NOT EXISTS `$dataBaseName`";
if(!mysqli_query($connection, $createDB)){
    consolelog("Failed To Create DB: " . mysqli_error($connection));
    die();
}

// Connect to the created database
$connection = mysqli_connect($host, $username, $password, $dataBaseName);
if ($connection) {
    consolelog("DB Connected!");
} else {
    consolelog("Database Connection Error: " . mysqli_connect_error());
    die();
}

// Create Users table
$createUsers = "CREATE TABLE IF NOT EXISTS `Users`( 
    `id` int(3) NOT NULL auto_increment,   
    `username` varchar(20) NOT NULL UNIQUE,  
    `password` varchar(60) NOT NULL,
    PRIMARY KEY (`id`)
)";
if(!mysqli_query($connection, $createUsers)){
    consolelog("Failed To Create Users Table: " . mysqli_error($connection));
}

// Create Messages table
$createMessages = "CREATE TABLE IF NOT EXISTS `Messages`( 
    `id` int(3) NOT NULL auto_increment,
    `sender` varchar(20) NOT NULL,
    `message` TEXT NOT NULL,
    `time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
)";
if(!mysqli_query($connection, $createMessages)){
    consolelog("Failed To Create Messages Table: " . mysqli_error($connection));
}
?>