<?php

require_once(__DIR__ . '/lib.inc.php');
$saveData['SiteURL'] = WebLib::GetVal($_POST, 'BaseURL', true);

if ($saveData['SiteURL'] !== '') {
  require_once(__DIR__ . '/class.MySQLiDBHelper.php');
  $Data = new MySQLiDBHelper(HOST_Name, MySQL_User, MySQL_Pass, MySQL_DB);
  $saveData['Data'] = json_encode($_POST);
  $saveData['Remark'] = $_SERVER['REMOTE_ADDR'];
  $Inserted = $Data->insert(MySQL_Pre . 'Deployed', $saveData);
  unset($saveData);
  unset($Data);
  exit();
}
?>