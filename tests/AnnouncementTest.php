<?php

global $dir;
$generator = new \core\Database\Generator();

$data_set = [];
$sql = [];
$iterations = 10;

// Generate lorem ipsum
for ($i = 0; $i < $iterations; $i++) {
    $data_set[] = [
        'title' => $generator->faker->sentence(),
        'content' => $generator->faker->paragraphs(3, true),
        'created_at' => $generator->faker->date(),
    ];
}

for ($i = 0; $i < $iterations; $i++) {
    $sql[] = $generator->generateInsertSQL('announcements', $data_set[$i]);
    echo $sql[$i] . PHP_EOL;
}
