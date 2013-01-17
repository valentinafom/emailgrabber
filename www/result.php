<?php

header('Content-Type: text/xml; charset=UTF-8');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$xml = urldecode($_POST['resultEmail']);
$xml = str_replace("&", "&amp;", $xml);
echo $xml;
?>
