<?php
/**
 * Created by PhpStorm.
 * User: Wei.Cheng
 * Date: 2018/7/30
 * Time: 上午 09:25
 */

include "../module/autoload.php";

echo getHostByName(getHostName());

testFloorNotificationUsers();

function testFloorNotificationUsers()
{
    $floor_id = 5;
    $notification_name = "orders_alarm";
    $list = get_floor_notification_user($notification_name, $floor_id);
    echo print_r($list, TRUE);
}

function testUsers()
{
    $list = get_user_all();
    echo print_r($list, TRUE);
}

