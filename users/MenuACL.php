<?php
require_once __DIR__ . '/../lib.inc.php';

WebLib::AuthSession();
WebLib::Html5Header('Menu Management');
WebLib::IncludeCSS();
WebLib::JQueryInclude();
WebLib::IncludeCSS('css/chosen.css');
WebLib::IncludeJS('js/chosen.jquery.min.js');
WebLib::IncludeCSS('users/css/MenuACL.css');
?>
</head>
<body>
  <div class="TopPanel">
    <div class="LeftPanelSide"></div>
    <div class="RightPanelSide"></div>
    <h1><?php echo AppTitle; ?></h1>
  </div>
  <div class="Header">
  </div>
  <?php
  include __DIR__ . '/MenuData.php';
  WebLib::ShowMenuBar('USER');
  ?>
  <div class="content">
    <?php
    WebLib::ShowMsg();
    $Data = new MySQLiDBHelper();
    if ((WebLib::GetVal($_POST, 'CmdMenuAction') === 'Filter Users') && isset($_POST['MenuID'])) {
      $Query = 'Select `U`.`UserMapID`,CONCAT(`UserName`,\'-\',`U`.`UserMapID`) as `UserName` '
              . ' FROM `' . MySQL_Pre . 'Users` as `U` JOIN `' . MySQL_Pre . 'MenuACL` as `A` '
              . ' ON (`A`.`UserMapID`=`U`.`UserMapID`) Where `A`.`MenuID`=?  Order By `UserName`';
      $RowsUser = $Data->rawQuery($Query, array('MenuID' => $_POST['MenuID'][0]));
      $Query = 'Select `M`.`MenuID`,`AppID`,'
              . ' CONCAT(`Caption`,\'(\',`UserMapID`,\'-\',`M`.`MenuID`,\'-\',`A`.`Activated`,\')\') as `Caption`'
              . ' FROM `' . MySQL_Pre . 'MenuItems` as `M` JOIN `' . MySQL_Pre . 'MenuACL` as `A` '
              . ' ON (`A`.`MenuID`=`M`.`MenuID`) Where `A`.`MenuID`=? Order By `AppID`,`MenuOrder`';
      $RowsMenu = $Data->rawQuery($Query, array('MenuID' => $_POST['MenuID'][0]));
    } else if ((WebLib::GetVal($_POST, 'CmdMenuAction') === 'Filter Menus') && isset($_POST['UserMapID'])) {

      $Query = 'Select `M`.`MenuID`,`AppID`,'
              . ' CONCAT(`Caption`,\'(\',`UserMapID`,\'-\',`M`.`MenuID`,\'-\',`A`.`Activated`,\')\') as `Caption`'
              . ' FROM `' . MySQL_Pre . 'MenuItems` as `M` JOIN `' . MySQL_Pre . 'MenuACL` as `A` '
              . ' ON (`A`.`MenuID`=`M`.`MenuID`) Where `UserMapID`=? Order By `AppID`,`MenuOrder`';
      $RowsMenu = $Data->rawQuery($Query, array('UserMapID' => $_POST['UserMapID'][0]));

      $Query = 'Select `UserMapID`,CONCAT(`UserName`,\'-\',`UserMapID`) as `UserName` '
              . ' FROM `' . MySQL_Pre . 'Users` as `U` Where `UserMapID`=?  Order By `UserName`';
      $RowsUser = $Data->rawQuery($Query, array('UserMapID' => $_POST['UserMapID'][0]));
    } else {
      $RowsUser = $Data->rawQuery('Select `UserMapID`,`UserName` '
              . ' FROM `' . MySQL_Pre . 'Users`  Order By `UserName`');
      $RowsMenu = $Data->rawQuery('Select `MenuID`,`AppID`,`Caption` '
              . ' FROM `' . MySQL_Pre . 'MenuItems` Order By `AppID`,`MenuOrder`');
    }
    ?>
    <form method="post" action="<?php echo WebLib::GetVal($_SERVER, 'PHP_SELF'); ?>">
      <div class="column">
        <ul>
          <?php
          echo '<li class="ListItem">Total Users: ' . count($RowsUser) . '</li>';
          foreach ($RowsUser as $Index => $User) {
            echo '<li class="ListItem">'
            . '<label for="User' . $User['UserMapID'] . '" >'
            . '<input id="User' . $User['UserMapID'] . '" type="checkbox" name="UserMapID[]" '
            . 'value="' . $User['UserMapID'] . '" />'
            . $User['UserName']
            . '</label></li>';
          }
          ?>
        </ul>
      </div>
      <div class="column">
        <ul>
          <?php
          echo '<li class="ListItem">Total Menus: ' . count($RowsMenu) . '</li>';
          foreach ($RowsMenu as $Index => $Menu) {
            echo '<li class="ListItem">'
            . '<label for="Menu' . $Menu['MenuID'] . '" >'
            . '<input id="Menu' . $Menu['MenuID'] . '" type="checkbox" name="MenuID[]" '
            . 'value="' . $Menu['MenuID'] . '" />'
            . '<strong>' . $Menu['AppID'] . '=>' . $Menu['Caption'] . '</strong>'
            . '</label></li>';
          }
          ?>
        </ul>
      </div>
      <div  class="column">
        <pre>
          <?php
          print_r($_POST);
          ?>
        </pre>
      </div>
      <div style="clear: both;"></div>
      <input type="submit"  name="CmdMenuAction" value="Allow" />
      <input type="submit"  name="CmdMenuAction" value="Restrict" />
      <input type="submit"  name="CmdMenuAction" value="Activate" />
      <input type="submit"  name="CmdMenuAction" value="Deactivate" />
      <input type="submit"  name="CmdMenuAction" value="Filter Menus" />
      <input type="submit"  name="CmdMenuAction" value="Filter Users" />
      <input type="hidden" name="FormToken" value="<?php echo WebLib::GetVal($_SESSION, 'FormToken') ?>" />
    </form>
    <?php
    unset($Data);
    unset($RowsUser);
    unset($RowsMenu);
    ?>
  </div>
  <div class="pageinfo">
    <?php WebLib::PageInfo(); ?>
  </div>
  <div class="footer">
    <?php WebLib::FooterInfo(); ?>
  </div>
</body>
</html>

