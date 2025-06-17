<?php
global $dir;

$generator = new \core\Database\Generator();

$data = [];
$tables = 11;

for ($i = 1; $i < $tables; $i++) {
    $data[] = [
        'tableId' => sprintf('T%03d', $i),
        'tableName' => 'Table ' . $i,
        'participants' => json_encode(['D001', 'D002', 'D003']),
    ];
}

for ($i = 1; $i < $tables; $i++) {
    echo $generator->generateInsertSQL('orders', $data[$i - 1]) . PHP_EOL;
}