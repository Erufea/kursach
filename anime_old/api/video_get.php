<?php
require_once 'config.php';
$result = '';
try {
	$db = new PDO ('pgsql:host=' .DB_HOST.';dbname=' .DB_NAME, DB_USER, DB_PASS);
} catch (PDOException $e) {
	$result = '{"error": {"text: "' . $e->getMessage().'"})'; 
	die($result);
}

if (isset($_GET['token'])) {
	$token = $_GET['token'];
	// Проверка токена
	$sql = sprintf ('SELECT "ID" FROM "users" WHERE "TOKEN" LIKE \'%s\' AND "EXPIRATION" > CURRENT_TIMESTAMP', $token);
	$stmt = $db->query($sql)->fetch();
	
	if (isset($stmt['ID'])) {
		$sql = 'SELECT "ID", "TITLE", "URL", "Description", "Html", "Cart" FROM "Video"';
		$stmt = $db->query($sql);
		$result = '{"videos": [';
		while($row = $stmt->fetch()) {
			$result .=sprintf('{"id":%d,"title":"%s","url":"%s","description":"%s","Html":"%s","Cart":"%s"},',$row['ID'],$row['TITLE'],$row['URL'],$row['Description'],$row['Html'],$row['Cart']);
		}
		$result = rtrim($result, ",");
		$result .= ']}';
			
	}
	
	else {
		$result = '{"error": {"text":"ОШИБКА ТОКЕНА"}}';
	}
}
else {
	$result = '{"error": {"text":"не передан токен"}}';
}
echo $result;

?>
