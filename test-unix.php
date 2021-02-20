<?php
require __DIR__ . '/vendor/autoload.php';

$failed = new Lucinda\Console\Text(" FAILED ");
$failed->setBackgroundColor(Lucinda\Console\BackgroundColor::RED);

$success = new Lucinda\Console\Text(" PASSED ");
$success->setBackgroundColor(Lucinda\Console\BackgroundColor::GREEN);

$table = new Lucinda\Console\Table(array_map(function ($column) {
    $text = new Lucinda\Console\Text($column);
    $text->setFontStyle(Lucinda\Console\FontStyle::BOLD);
    return $text;
}, ["Class", "Status", "Message"]));
$table->addRow(["Foo\\Bar", $success, "this is good"]);
$table->addRow(["Foo\\Baz", $failed, "this is bad"]);
$table->display();
