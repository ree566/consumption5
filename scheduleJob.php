<?php
require 'vendor/autoload.php';
/**
 * Created by PhpStorm.
 * User: Wei.Cheng
 * Date: 2018/7/25
 * Time: 下午 04:05
 */
$timeZone = new \DateTimeZone("Asia/Taipei");
$from = new \DateTime('2018-01-01 00:00:00', $timeZone);
$to = new \DateTime('2018-12-31 23:00:00', $timeZone);

//echo $from->getTimestamp() . "\n";

$job = \Scheduler\Job\Job::createFromString(
    'FREQ=MONTHLY;COUNT=5',            //Cron syntax recurrence rule
    '2018-01-01T17:00:00',  //Start date
    function () {
        //echo "processing...\n";
    },          //Callback
    $timeZone
);

$scheduler = new \Scheduler\Scheduler();
$scheduler->addJob($job);
$jobRunner = new \Scheduler\JobRunner\JobRunner();

$reports = $jobRunner->run($scheduler, $from, $to, true);

echo "Total processing " . count($reports) . " times";

//foreach ($reports as $report) {
//    echo $report->getType() . "\n";
//}