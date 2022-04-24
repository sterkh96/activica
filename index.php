<?php
include 'vendor/autoload.php';

use Sterkh\Activica\Shop;

$shop = new Shop();
if(!$shop->isParsed()){
    $shop->parse(__DIR__ . '/src/goods.xml');
    $shop->save();
}
$shop->view();