<?php
require("../vendor/autoload.php");
$openapi = \OpenApi\Generator::scan(['../app/Http/Controllers']);
header('Content-Type: application/json');
echo $openapi->toJSON();