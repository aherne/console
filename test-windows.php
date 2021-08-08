<?php

use Lucinda\Console\Text;
use Lucinda\Console\Table;
use Lucinda\Console\OrderedList;
use Lucinda\Console\AbstractList;

require __DIR__ . '/vendor/autoload.php';
ini_set("display_errors", 1);

// $table = new Lucinda\Console\Table(array_map(function ($column) {
//     return strtoupper($column);
// }, ["Class", "Status", "Message"]));
// $table->addRow(["Foo\\Bar", "SUCCESS", "this is good"]);
// $table->addRow(["Foo\\Baz", "FAILED", "this is bad"]);
// echo $table->toString()."\n";

// $list = new Lucinda\Console\UnorderedList("Is Lucinda a great genius?");
// $sublist = $list->addList("Yes");
// $sublist->addItem("K");
// $sublist->addItem("V");
// $list->addItem("No");
// echo $list->toString()."\n";

$object = new Lucinda\Console\Wrapper('<div>Hey!</div>
<br/>
<div style="font-weight: bold">hello</div>
    
<table>
    <thead>
        <tr>
            <td style="background-color: red">Name</td>
            <td>Value</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="color: green">qqq</td>
            <td>sss</td>
        </tr>
        <tr>
            <td>ddd</td>
            <td>fff</td>
        </tr>
    </tbody>
</table>
    
<ol>
    <caption style="color: blue">Is Lucinda a genius?</caption>
    <li>
        <ol>
            <caption>Yes</caption>
            <li style="background-color: blue">qwerty</li>
            <li>asdfgh</li>
        </ol>
    </li>
    <li>No</li>
</ol>
    
<table>
    <thead>
        <tr>
            <td>Name</td>
            <td>Value</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>xxx</td>
            <td>www</td>
        </tr>
        <tr>
            <td>yyy</td>
            <td>uuu</td>
        </tr>
    </tbody>
</table>
');
echo $object->getBody();
/**
 <!--...-->	Defines a comment
 <p>	Defines a paragraph
 <progress>	Represents the progress of a task
 <q>	Defines a short quotation

 <table>	Defines a table
 <tbody>	Groups the body content in a table
 <td>	Defines a cell in a table
 <tfoot>	Groups the footer content in a table
 <th>	Defines a header cell in a table
 <thead>	Groups the header content in a table
 <tr>	Defines a row in a table

 <ol>	Defines an ordered list
 <ul>	Defines an unordered list
 */
