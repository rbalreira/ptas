<?php
$host='host';
$port = 5432;
$db = 'database';
$username = 'username';
$password = 'password';

date_default_timezone_set('Europe/Lisbon');

$db_conn = "pgsql:host=$host;port=$port;dbname=$db;user=$username;password=$password";

$conn = new PDO($db_conn);

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
