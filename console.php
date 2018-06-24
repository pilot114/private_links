<?php

use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use App\Controllers\BaseController;

require_once "vendor/autoload.php";

function createThumb($filename, $sec)
{
    $config = (new BaseController())->config;
    $movie = $config['root_dir'] . '/' . $filename;
    $thumbnail = $config['root_dir'] . '/' . $filename . '.png';

    $ffmpeg = \FFMpeg\FFMpeg::create();
    $video = $ffmpeg->open($movie);
    $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($sec));
    $frame->save($thumbnail);
    return $thumbnail;
}

foreach (glob('public/conf/*') as $video) {
    $thumb = createThumb($video, 20);
    echo sprintf("%s\n", $thumb);
}
