<?php
/**
 * helper func
 */
function con()
{
    static $mysqli = null;
    if (!$mysqli) {
        $mysqli = dbc();
    }
    return $mysqli;
}

function is_true($dict, $key)
{
    return isset($dict[$key]) && $dict[$key];
}

function last_id()
{
    return con()->insert_id;
}

function escape($s)
{
    return con()->escape_string($s);
}

function Q($q)
{
    $re = con()->query($q);
    // to debug syntax error
    if (con()->error) {
        throw new Exception("資料庫錯誤︰" . con()->error);
    }

    if ($re === true) {
        return con()->affected_rows;
    }

    return $re;
}

function get_row($q)
{
    $row = get_rows($q);
    return @$row[0];
}

function get_rows($q)
{
    $rows = [];

    if ($re = Q($q)) {
        while ($row = $re->fetch_assoc()) {
            $rows[] = $row;
        }
        $re->close();
    }

    if ($rows) correct_int($rows);
    return $rows;
}

// http://stackoverflow.com/questions/5323146/mysql-integer-field-is-returned-as-string-in-php
function correct_int(&$dict)
{
    foreach ($dict as $key => &$i) {
        if (is_array($i)) {
            correct_int($i);
        } else {
            if (is_numeric($i) && (!startwith($i, "0") || startwith($i, "0.")) || $i == "0") {
                $i = (float)$i;
            }
            if ($key == "pass_hash") {
                unset($dict[$key]);
            }
        }
    }
}

function startwith($s, $p)
{
    return strpos($s, $p) === 0;
}

/**
 * login
 */
function login($uid, $hash)
{
    $r = get_row(
        "SELECT 
			users.*, 
			teams.floor_id,
			teams.name as team_name,
			floors.name as floor_name
		FROM 
			users, 
			teams,
			floors
		WHERE 
			users.id = '$uid' && 
			users.pass_hash = '$hash' && 
			users.team_id = teams.id &&
			floors.id = teams.floor_id"
    );

    return $r;
}

/**
 * getters
 */
function get_item($item_id)
{
    return get_row("SELECT * FROM items WHERE id = $item_id");
}

function get_item_all()
{
    return get_rows("SELECT items.*, floors.name as floor_name FROM items, floors WHERE items.floor_id = floors.id");
}

function get_item_floor($floor_id)
{
    return get_rows("SELECT * FROM items WHERE isGeneric = 1 OR floor_id = $floor_id");
}

function get_team($team_id)
{
    return get_row("SELECT * FROM teams WHERE id = $team_id");
}

function get_team_all()
{
    return get_rows("
		SELECT 
			teams.*,
			floors.name as floor_name
		FROM
			teams, floors
		WHERE
			teams.floor_id = floors.id"
    );
}

function get_user($user_id)
{
    return get_row(
        "SELECT
			users.id,
			users.name,
			users.permission,
			users.team_id,
			teams.name as team_name,
			floors.name as floor_name
		FROM
			users, teams, floors
		WHERE 
			users.id = '$user_id' &&
			users.team_id = teams.id &&
			floors.id = teams.floor_id"
    );
}

function get_user_all()
{
    return get_rows(
        "SELECT 
			floors.*,
			teams.*,
			users.*,
			
			teams.name as team_name,
			floors.name as floor_name
		FROM
			users, teams, floors
		WHERE
			users.team_id = teams.id &&
			floors.id = teams.floor_id"
    );
}

function get_order_team($team_id)
{
    return get_rows(
        "SELECT 
			items.*,
			orders.*,
			items.name as item_name, 
			users.name as user_name 
		FROM 
			orders, items, users 
		WHERE 
			orders.item_id = items.id && 
			orders.team_id = $team_id && 
			orders.user_id = users.id"
    );
}

function get_order_floor_new($floor_id)
{
    return get_rows(
        "SELECT 
			orders.*, 			
			items.list_key as list_key,
			items.spec as spec,
			items.name as item_name, 
			items.supplier as supplier,
			items.price as price,
			users.name as user_name, 
			teams.name as team_name 
		FROM 
			orders, items, users, teams 
		WHERE
			orders.item_id = items.id && 
			orders.team_id = teams.id && 
			orders.user_id = users.id && 
			orders.checkout_time = 0 &&
			(items.isGeneric = 1 || items.floor_id = $floor_id)"
    );
}

function get_order_floor_history($floor_id, $time_start, $time_end)
{
    return get_rows(
        "SELECT 
            floors.*,
			users.*,
			items.*,
			orders.*, 			
			items.name as item_name, 
			users.name as user_name, 
			teams.name as team_name,
			floors.name as floor_name
        FROM 
            orders, items, users, teams, floors
        WHERE 
            orders.item_id = items.id &&
			orders.team_id = teams.id &&
			orders.user_id = users.id &&
			orders.floor_id = floors.id &&
            ($floor_id = -1 OR orders.floor_id = $floor_id) &&
            orders.checkout_time >= '$time_start' &&
			orders.checkout_time <= '$time_end' &&
			reject = 0"
    );
}

function get_order_month()
{
    $this_month = date("Y-m-01 00:00:00");
    $now = date("Y-m-d 23:59:59");
    return get_order_time($this_month, $now);
}

function get_order_time($time_start, $time_end)
{
    return get_rows(
        "SELECT 
			floors.*,
			users.*,
			items.*,
			orders.*, 			
			items.name as item_name, 
			users.name as user_name, 
			teams.name as team_name,
			floors.name as floor_name
		FROM 
			orders, items, users, teams, floors
		WHERE 
			orders.item_id = items.id &&
			orders.team_id = teams.id &&
			orders.user_id = users.id &&
			orders.floor_id = floors.id &&
			orders.checkout_time >= '$time_start' &&
			orders.checkout_time <= '$time_end'"
    );
}

function get_floor_all()
{
    return get_rows("SELECT * FROM floors");
}

function get_supplier_all()
{
    return get_rows("SELECT * FROM suppliers");
}


/**
 * setters
 */
function add_item_count($item_id, $count)
{
    global $SESS;

    Q(
        "UPDATE	
			items 
		SET 
			count = count + $count 
		WHERE
			id = $item_id"
    );

    Q(
        "INSERT INTO
			orders
		SET
			item_id = $item_id,
			number = -$count,
			user_id = '{$SESS["uid"]}',
			team_id = '{$SESS["team_id"]}',
			floor_id = {$SESS["floor_id"]},
			checkout_time = NOW()"
    );
}

function add_item($item)
{
    global $SESS;
    $floor_id = $SESS["floor_id"];
    Q(
        "INSERT INTO 
			items 
		SET 
			name = '$item->name',
			spec = '$item->spec',
			list_key = NULLIF('$item->list_key', ''),
			price = '$item->price',
			supplier = '$item->supplier',
			dimension = '$item->dimension',
			moq = '$item->moq',
			comment = '$item->comment',
			low_floor = '$item->low_floor',
			floor_id = $floor_id"
    );
}

function set_item($item)
{
    if (!@$item->id) {
        return;
    }
    Q(
        "UPDATE
			items
		SET
			name = '$item->name',
			spec = '$item->spec',
			list_key = NULLIF('$item->list_key', ''),
			price = $item->price,
			supplier = '$item->supplier',
			dimension = '$item->dimension',
			moq = '$item->moq',
			comment = '$item->comment',
			low_floor = '$item->low_floor'
		WHERE
			id = $item->id"
    );
}

function delete_item($item_id)
{
    Q("DELETE FROM items WHERE id = $item_id");
}

function checkout_order($order_id)
{
    global $SESS;
    Q(
        "UPDATE
			orders
		SET
			checkout_time = NOW(),
			checkout_user_id = '{$SESS["uid"]}'
		WHERE
			orders.id = $order_id"
    );

    Q(
        "UPDATE
			items, orders
		SET
			items.count = items.count - orders.number
		WHERE
			orders.id = $order_id && orders.item_id = items.id"
    );
}

function reject_order($order_id)
{
    global $SESS;
    Q(
        "UPDATE
			orders
		SET
			checkout_time = NOW(), 
			checkout_user_id = '{$SESS["uid"]}',
			reject = 1
		WHERE
			id = $order_id"
    );
}

function add_supplier($supplier)
{
    Q(
        "INSERT INTO
			suppliers
		SET
			address = '$supplier->address',
			comment = '$supplier->comment',
			contact = '$supplier->contact',
			email = '$supplier->email',
			fax = '$supplier->fax',
			name = '$supplier->name',
			phone = '$supplier->phone',
			receipt_number = '$supplier->receipt_number',
			tel = '$supplier->tel'"
    );
}

function set_supplier($supplier)
{
    Q(
        "UPDATE
			suppliers
		SET
			address = '$supplier->address',
			comment = '$supplier->comment',
			contact = '$supplier->contact',
			email = '$supplier->email',
			fax = '$supplier->fax',
			name = '$supplier->name',
			phone = '$supplier->phone',
			receipt_number = '$supplier->receipt_number',
			tel = '$supplier->tel'
		WHERE
			id = $supplier->id"
    );
}

function del_supplier($supplier_id)
{
    Q("DELETE FROM suppliers WHERE id = '$supplier_id'");
}

function add_new_order($item_id, $number)
{
    global $SESS;

    Q(
        "INSERT INTO
			orders
		SET
			item_id = $item_id,
			number = $number,
			team_id = '{$SESS["team_id"]}',
			user_id = '{$SESS["uid"]}',
			floor_id = '{$SESS["floor_id"]}'"
    );
}

function add_user($user)
{
    $pass_hash = pw_hash($user->id);
    Q(
        "INSERT INTO
			users
		SET
			id = '$user->id',
			name = '$user->name',
			pass_hash = '$pass_hash',
			permission = $user->permission,
			team_id = $user->team_id"
    );
}

function set_user($user)
{
    global $SESS;
    $row = get_user($user->id);
    error_dump($row);
    if ($row && ($row["permission"] >= $SESS["permission"] || $user->permission >= $SESS["permission"])) {
        throw new Exception("權限錯誤");
    }

    if (!$row) {
        add_user($user);
    }

    Q(
        "UPDATE
			users
		SET
			name = '$user->name',
			team_id = $user->team_id,
			permission = '$user->permission'
		WHERE
			id = '$user->id'"
    );
}

function set_users($users)
{
    foreach ($users as $user) {
        set_user($user);
    }
}

function delete_user($user_id)
{
    global $SESS;
    $row = get_user($user_id);
    if ($row && $row["permission"] >= $SESS["permission"]) {
        throw new Exception("權限錯誤");
    }

    Q("DELETE FROM users WHERE id = '$user_id'");
}

function add_team($team)
{
    Q(
        "INSERT INTO
			teams
		SET
			name = '$team->name',
			floor_id = $team->floor_id"
    );
}

function set_team($team)
{
    Q(
        "UPDATE
			teams
		SET
			name = '$team->name',
			floor_id = $team->floor_id
		WHERE
			id = $team->id"
    );
}

function delete_team($team_id)
{
    Q("DELETE FROM teams WHERE id = $team_id");
}

function add_floor($floor)
{
    Q("INSERT INTO floors SET name = '$floor->name'");
}

function set_floor($floor)
{
    Q("UPDATE floors SET name = '$floor->name' WHERE id = $floor->id");
}

function delete_floor($floor_id)
{
    Q("DELETE FROM floors WHERE id = $floor_id");
}

function set_pass($old_pass, $new_pass)
{
    global $SESS;

    $old_hash = pw_hash($old_pass);
    $new_hash = pw_hash($new_pass);
    $user_id = $SESS["uid"];
    if (get_row("SELECT * FROM users WHERE id = '$user_id' && pass_hash = '$old_hash'")) {
        Q(
            "UPDATE
				users
			SET
				pass_hash = '$new_hash'
			WHERE
				id = '$user_id'"
        );
        return array(
            "success" => true
        );
    } else {
        return array(
            "error" => "舊密碼錯誤"
        );
    }
}
