<?php
require __DIR__ . '/vendor/autoload.php';

$table = new Lucinda\Console\Table(array_map(function ($column) {
    return strtoupper($column);
}, ["Class", "Status", "Message"]));
$table->addRow(["Foo\\Bar", "SUCCESS", "this is good"]);
$table->addRow(["Foo\\Baz", "FAILED", "this is bad"]);
$table->display();
