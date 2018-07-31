<?php

require("module/autoload.php");

require "mailer.php";

/*
 * Set the host address first before begin send mail jobs.
 * host: Relay.advantech.com.tw
 * 倉庫管理Add: 訂購一定數量可統一發mail(用schedule一天一次or使用者自行點選)
 * mail to repository owner
 * mail cc to system owner and oz.kao & order sender.
 * if mail is not setting, ignore
 */

check_permission(1);
//error_reporting(0);

$SESS = &get_session();

@$json = json_decode(file_get_contents("php://input"));
if ($json) {
    try {

        $floor_id = $SESS["floor_id"];

        $notifyUsers = get_floor_repository_owner($floor_id);

        if (empty($notifyUsers)) {
            $out = array(
                "message" => "NotifyUsers is empty."
            );
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
        }

        $notifyCcUsers = get_floor_notification_user("orders_alarm", $floor_id);
        $requestUser = array(
            "email" => $SESS["email"]
        );
        array_push($notifyCcUsers, $requestUser);

        $subject = "耗材需求通知"; //信件標題

//    處理body信件內容
        $body = "
        <h1>耗材需求</h1>
        <table style='border: 1px solid black; padding: 3px;'>
        <tr>
            <th>料號</th>
            <th>品名</th>
            <th>數量</th>
            <th>單位</th>
            <th>樓層</th>
            <th>單位</th>
            <th>需求者</th>
            <th>需求者名</th>
        </tr>
        <tr>
            <td>$json->list_key</td>
            <td>$json->name</td>
            <td>$json->number</td>
            <td>$json->dimension</td>
            <td>" . $SESS["floor_name"] . "</td>
            <td>" . $SESS["team_name"] . "</td>
            <td>" . $SESS["uid"] . "</td>
            <td>" . $SESS["name"] . "</td>
            </tr>
        </table>
    <h5><a href='http://$_SERVER[HTTP_HOST]/" . WS_PATH . "/repository.php'>前往庫存頁面</a></h5>";

        sendMail($notifyUsers, $notifyCcUsers, $subject, $body);

        $out = array(
            "message" => "Sending mail success"
        );

        echo json_encode($out, JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
} else {
    echo "goodbye";
}
