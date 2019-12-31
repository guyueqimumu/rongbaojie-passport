<?php
/**
 * Created by QiLin.
 * User: NO.01
 * Date: 2019/12/30
 * Time: 18:13
 */


use Passport\Client\Encryption\Encryption;

include '../vendor/autoload.php';


$handle = new Passport\Client\handle([
    'appId' => '10000',
    'appSecret' => '4b797e878798db8353bdbcc9a631398c',
]);
$ticket = "+WzwWxIetTgVUblwfWIuX2l53zIuXf56ajgLNwrDaVxeHoGxSqXYXluM4D0VDxkBKjtBHBMLwEAr8DxGB/zmT619Zt5v4JMGZKcb5z7Ngx3SDrMQ/0xZ/+vsoiPLETACU507Xe+J+WiRPSSK095orkuL8hT/a2SvYXKV1daWP7MX1Z9ONGcIUOh0iXKwwIe526KwxFHs6GW34AqRzEA6GCV0q3Onu45pOo7j1h2Wo+7nVsaWhWLqE71djPuf1aRw8cLa7ak+yMPk/cYOLdNOiFQNawpnGtowpGEjOQCttfWhT5eqZJYbdbsestGmOuB2EO5KDgixZCKkGhQMZMTNUPK3IvE8JwN+4Nd/jJvnX6ow6IHj6GtlTE/dBuZBcaLVApw0jFehBqo=";
$encryption = new Encryption();
$res = $encryption->decode(base64_decode($ticket));
parse_str($res, $data);
print_r($data);
$url = "http://open.insurance.cn/Session/getUserInfo";
$res = $handle->getUserInfo($url, $data['ticket']);

var_dump($res);
