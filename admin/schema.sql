CREATE TABLE IF NOT EXISTS floors (
	id INT UNSIGNED AUTO_INCREMENT,
	name CHAR(20) NOT NULL,
	PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS suppliers (
	id INT UNSIGNED AUTO_INCREMENT,
	name CHAR(20) NOT NULL,
	contact CHAR(20) DEFAULT NULL,
	tel CHAR(20) DEFAULT NULL,
	phone CHAR(20) DEFAULT NULL,
	fax CHAR(20) DEFAULT NULL,
	address CHAR(50) DEFAULT NULL,
	email CHAR(50) DEFAULT NULL,
	receipt_number CHAR(10) DEFAULT NULL,
	comment TEXT DEFAULT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY (name)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS teams (
	id INT UNSIGNED AUTO_INCREMENT,
	name CHAR(20) NOT NULL,
	floor_id INT UNSIGNED NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (floor_id)
		REFERENCES floors(id)
		ON DELETE RESTRICT
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS users (
	id CHAR(10) NOT NULL,
	permission INT UNSIGNED DEFAULT 1,
	pass_hash CHAR(36) NOT NULL,
	name CHAR(20) DEFAULT '',
	team_id INT UNSIGNED NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (team_id)
		REFERENCES teams(id)
		ON DELETE RESTRICT
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS items (
	id INT UNSIGNED AUTO_INCREMENT,
	list_key CHAR(20) DEFAULT NULL,
	name CHAR(20) DEFAULT '',
	spec CHAR(20) DEFAULT '',
	supplier CHAR(20) NOT NULL,
	price FLOAT UNSIGNED DEFAULT 0,
	dimension CHAR(20) NOT NULL,
	low_floor INT UNSIGNED DEFAULT 0,
	count INT UNSIGNED DEFAULT 0,
	moq INT UNSIGNED DEFAULT 0,
	comment TEXT DEFAULT NULL,
	floor_id INT UNSIGNED NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY (floor_id, list_key),
	FOREIGN KEY (floor_id)
		REFERENCES floors(id)
		ON DELETE RESTRICT
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS orders (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	user_id CHAR(10) NOT NULL,
	team_id INT UNSIGNED NOT NULL,
	item_id INT UNSIGNED NOT NULL,
	number INT NOT NULL,
	order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	checkout_time TIMESTAMP DEFAULT 0,
	floor_id INT UNSIGNED NOT NULL,
	reject BIT(1) DEFAULT 0,
	FOREIGN KEY (item_id)
		REFERENCES items(id)
		ON DELETE RESTRICT,
	FOREIGN KEY (user_id)
		REFERENCES users(id)
		ON DELETE RESTRICT,
	FOREIGN KEY (floor_id)
		REFERENCES floors(id)
		ON DELETE RESTRICT
) DEFAULT CHARACTER SET utf8;

INSERT IGNORE INTO floors (id, name) VALUE (1, "一樓");
INSERT IGNORE INTO floors (id, name) VALUE (2, "二樓");
INSERT IGNORE INTO floors (id, name) VALUE (3, "三樓");
INSERT IGNORE INTO floors (id, name) VALUE (4, "四樓");
INSERT IGNORE INTO floors (id, name) VALUE (5, "五樓");
INSERT IGNORE INTO floors (id, name) VALUE (6, "六樓");
INSERT IGNORE INTO floors (id, name) VALUE (7, "七樓");

INSERT IGNORE INTO teams (id, name, floor_id) VALUE (1, "Default team", 1);
INSERT IGNORE INTO teams (id, name, floor_id) VALUE (2, "A部門", 1);
INSERT IGNORE INTO teams (id, name, floor_id) VALUE (3, "B部門", 1);
INSERT IGNORE INTO teams (id, name, floor_id) VALUE (4, "C部門", 1);

INSERT IGNORE INTO users (id, pass_hash, permission, name, team_id) VALUES ('root', 'e0e614e4b04c29255fac10f852171926', 999, 'Administrator', 1);
INSERT IGNORE INTO users (id, pass_hash, permission, name, team_id) VALUES ('guest', 'e0e614e4b04c29255fac10f852171926', 1, '客人', 1);
INSERT IGNORE INTO users (id, pass_hash, permission, name, team_id) VALUES ('test1', 'e0e614e4b04c29255fac10f852171926', 2, 'A員工', 2);
INSERT IGNORE INTO users (id, pass_hash, permission, name, team_id) VALUES ('test2', 'e0e614e4b04c29255fac10f852171926', 2, 'B員工', 3);
INSERT IGNORE INTO users (id, pass_hash, permission, name, team_id) VALUES ('test3', 'e0e614e4b04c29255fac10f852171926', 2, 'C員工', 4);
INSERT IGNORE INTO users (id, pass_hash, permission, name, team_id) VALUES ('test4', 'e0e614e4b04c29255fac10f852171926', 3, '審核人員', 1);
INSERT IGNORE INTO users (id, pass_hash, permission, name, team_id) VALUES ('test5', 'e0e614e4b04c29255fac10f852171926', 4, '樓層管理員', 1);
