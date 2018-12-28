<?php
	include_once("db.class.php");
	include_once("shop.class.php");
    include_once("xmlworker.class.php");

	$db = new ModelDb();
    $shop = new Shop();
    $xmlw = new xmlworker();

	$db->host = "";
	$db->database = "";
	$db->user = "";
	$db->password = "";

	$db->connect();

	$shop->setDB($db);

	$categories = $shop->setCategories();
    $products = $shop->setProducts();

	$xmlw->setShopLink($shop)
        ->createXML();


?>
