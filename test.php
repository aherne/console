<?php
require __DIR__ . '/vendor/autoload.php';

use Lucinda\Console\Wrapper;
ini_set("display_errors",1);

$html = '
<div>Hey!</div>
<div>hello, <u>Lucinda</u></div>
    
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
    <caption style="color: blue">Is <b>rrr</b> ttt?</caption>
    <li>
        <ul>
            <caption>xxx</caption>
            <li style="background-color: blue">aaa</li>
            <li>bbb</li>
        </ul>
    </li>
    <li>No</li>
    <li>
        <ol>
            <caption>yyy</caption>
            <li>ccc</li>
            <li>ddd <u>ooo</u></li>
        </ol>
    </li>
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
';

$wrapper = new Wrapper($html);
echo $wrapper->getBody();