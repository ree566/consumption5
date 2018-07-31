<?php
require("module/autoload.php");
check_permission(1);

$SESS = &get_session();

@$json = json_decode(file_get_contents("php://input"));
if ($json && isset($json->command)) {
    require_once "module/operator_func.php";

    $out = null;

    try {
        if ($SESS["permission"] >= 1) {
            /**
             * report.php
             */
            if ($json->command == "get-order-time") {
                $time_start = $json->time_start;
                $time_end = $json->time_end;
                $out = get_order_time($time_start, $time_end);
            }

            /**
             * report2.php
             */
            if ($json->command == "get-order-floor-history") {
                $floor = isset($json->floor) ? $json->floor : null;
                $time_start = $json->time_start;
                $time_end = $json->time_end;
                $out = get_order_floor_history($floor, $time_start, $time_end);
            }
        }

        if ($SESS["permission"] >= 2) {
            /**
             * account.php
             */
            if ($json->command == "set-pass") {
                error_dump($json);
                $old_pass = $json->old_pass;
                $new_pass = $json->new_pass;
                $out = set_pass($old_pass, $new_pass);
            }

            /**
             * order.php
             */
            if ($json->command == "add-new-order") {
                $item_id = $json->item_id;
                $number = $json->number;
//                add_new_order($item_id, $number);
                $out = get_order_team($SESS["team_id"]);
            }

            if ($json->command == "get-order-team") {
                $out = get_order_team($SESS["team_id"]);
            }
        }

        if ($SESS["permission"] >= 3) {
            /**
             * repository.php
             */
            if ($json->command == "get-order-new") {
                $out = get_order_floor_new($SESS["floor_id"]);
            }

            if ($json->command == "add-item-count") {
                $item_id = $json->item_id;
                $count = $json->count;
                add_item_count($item_id, $count);
                $out = get_item_floor($SESS["floor_id"]);
            }

            if ($json->command == "add-item") {
                $item = $json->item;
                add_item($item);
                $out = get_item_floor($SESS["floor_id"]);
            }

            if ($json->command == "set-item") {
                $item = $json->item;
                set_item($item);
                $out = get_item_floor($SESS["floor_id"]);
            }

            if ($json->command == "delete-item") {
                $item_id = $json->item_id;
                delete_item($item_id);
                $out = get_item_floor($SESS["floor_id"]);
            }

            if ($json->command == "checkout-order") {
                $order_id = $json->order_id;
                checkout_order($order_id);
                $out = [
                    "orders" => get_order_floor_new($SESS["floor_id"]),
                    "items" => get_item_floor($SESS["floor_id"])
                ];
            }

            if ($json->command == "reject-order") {
                $order_id = $json->order_id;
                reject_order($order_id);
                $out = get_order_floor_new($SESS["floor_id"]);
            }

            if ($json->command == "add-supplier") {
                add_supplier($json->supplier);
                $out = get_supplier_all();
            }

            if ($json->command == "set-supplier") {
                set_supplier($json->supplier);
                $out = get_supplier_all();
            }

            if ($json->command == "remove-supplier") {
                del_supplier($json->supplier_id);
                $out = get_supplier_all();
            }
        }

        if ($SESS["permission"] >= 4) {
            /**
             * control.php
             */
            if ($json->command == "add-user") {
                $user = $json->user;
                add_user($user);
                $out = get_user_all();
            }

            if ($json->command == "set-user") {
                $user = $json->user;
                set_user($user);
                $out = get_user_all();
            }

            if ($json->command == "set-users") {
                $users = $json->users;
                set_users($users);
                $out = get_user_all();
            }

            if ($json->command == "delete-user") {
                $user_id = $json->user_id;
                delete_user($user_id);
                $out = get_user_all();
            }

            if ($json->command == "add-team") {
                $team = $json->team;
                add_team($team);
                $out = get_team_all();
            }

            if ($json->command == "set-team") {
                $team = $json->team;
                set_team($team);
                $out = get_team_all();
            }

            if ($json->command == "delete-team") {
                $team_id = $json->team_id;
                delete_team($team_id);
                $out = get_team_all();
            }

            /**
             * floor.php
             */
            if ($json->command == "add-floor") {
                add_floor($json->floor);
                $out = get_floor_all();
            }

            if ($json->command == "set-floor") {
                set_floor($json->floor);
                $out = get_floor_all();
            }

            if ($json->command == "delete-floor") {
                delete_floor($json->floor_id);
                $out = get_floor_all();
            }
        }
    } catch (Exception $e) {
        $out = array(
            "error" => $e->getMessage(),
            "except" => (string)$e
        );
    }

    echo json_encode($out, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode("error");
}

