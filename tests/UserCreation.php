<?php

global $dir;

$generator = new \core\Database\Generator();

//$id = $generator->faker->regexify('[A-D]{1}\d{4}');
$name = $generator->faker->name();
$id = 'D1234';
$class = strtoupper($generator->faker->regexify('[1-5]{1}[A-Z]{2}\d{1}'));
$phone = $generator->faker->phoneNumber();
$password = password_hash('abc', PASSWORD_BCRYPT);
$info = json_encode([]);

// add all above data to $data
$data = compact('id', 'name', 'class', 'phone', 'password', 'info');

$sql = $generator->generateInsertSQL('users', $data);
echo $sql . PHP_EOL;