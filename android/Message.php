<?php

require_once __DIR__ . '/../lib.inc.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Contact.php';
require_once __DIR__ . '/Group.php';
require_once(__DIR__ . '/../smsgw/smsgw.inc.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Message
 *
 * @author Abu Salam
 */
class Message
{

  protected $User;
  protected $MsgID;
  protected $Msg;

  public function setMsg($Msg) {
    $this->Msg = $Msg;
  }

  public function getMsg() {
    return $this->Msg;
  }

  //put your code here
  function createSMS($User, $Message, $GroupName) {
    $Group = new Group();
    $Group->setGroup($GroupName);
    $Gid = $Group->getGroupID();
    $this->User=$User;
    $DB = new MySQLiDBHelper();
    $insertData['UserID'] = $User->getMobileNo();
    $insertData['GroupID'] = $Group->getGroupID();
    $insertData['MsgText'] = $Message;
    //$insertData['SentTime'] =time();
    $MessageID = $DB->insert(MySQL_Pre . 'SMS_Messages', $insertData);
    $getCon = new Contact();
    $Cont = $getCon->getContactByGroup($Gid);
    //print_r($Cont);
    foreach ($Cont as $ContactID) {
      $this->sendSMS($Message, $ContactID['MobileNo']);
    }
    $this->Msg = $Message;
    $Mid = $this->MsgID = $MessageID;
    return $Mid;
  }

  function sendSMS($MobileNo, $Message) {
    echo $MobileNo.'--'. $Message;
    SMSGW::SendSMS($Message . "\n--\n".$this->User->getDesignation(), $MobileNo);
  }

  function getAllSMS() {
    $DB = new MySQLiDBHelper();
    $SMS = $DB->get(MySQL_Pre . 'SMS_Messages');
    return $SMS;
  }

  function getSMSByUser() {
    $DB = new MySQLiDBHelper();
    $Uid = $this->getUser();
    $DB->where('UserID', $Uid);
    $SMS = $DB->get(MySQL_Pre . 'SMS_Messages');
    print_r($SMS);
    return $SMS;
  }
}

?>