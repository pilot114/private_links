<?php

use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use App\Controllers\BaseController;

require_once "vendor/autoload.php";

$em = (new BaseController())->db();

$helperSet = new HelperSet([
    'em' => new EntityManagerHelper($em)
]);
return $helperSet;