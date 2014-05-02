<?php
/** 
 * Postfix Admin 
 * 
 * LICENSE 
 * This source file is subject to the GPL license that is bundled with  
 * this package in the file LICENSE.TXT. 
 * 
 * Further details on the project are available at : 
 *     http://www.postfixadmin.com or http://postfixadmin.sf.net 
 * 
 * @version $Id: edit-mailbox.php 1479 2013-06-16 16:39:36Z christian_boltz $ 
 * @license GNU GPL v2 or later. 
 * 
 * File: edit-mailbox.php 
 * Used to update an existing mailboxes settings.
 * Template File: edit-mailbox.php
 *
 * Template Variables:
 *
 * tMessage
 * tName
 * tQuota
 *
 * Form POST \ GET Variables:
 *
 * fUsername
 * fDomain
 * fPassword
 * fPassword2
 * fName
 * fQuota
 * fActive
 */

require_once('common.php');

authentication_require_role('admin');
$SESSID_USERNAME = authentication_get_username();

$fUsername = 'x';
$fDomain = 'y';
$error = 0;

if (isset ($_GET['username'])) $fUsername = escape_string ($_GET['username']);
$fUsername = strtolower ($fUsername);
if (isset ($_GET['domain'])) $fDomain = escape_string ($_GET['domain']);

$pEdit_mailbox_name_text = $PALANG['pEdit_mailbox_name_text'];
$pEdit_mailbox_quota_text = $PALANG['pEdit_mailbox_quota_text'];


if (!(check_owner ($SESSID_USERNAME, $fDomain) || authentication_has_role('global-admin')) )
{
   $error = 1;
   $tName = $fName;
   $tQuota = $fQuota;
   $tActive = $fActive;
   $tMessage = $PALANG['pEdit_mailbox_domain_error'] . "$fDomain</span>";
}

$result = db_query("SELECT * FROM $table_mailbox WHERE username = '$fUsername' AND domain = '$fDomain'");
if($result['rows'] != 1) {
   die("Invalid username chosen; user does not exist in mailbox table");
}
$user_details = db_array($result['result']);

if ($_SERVER['REQUEST_METHOD'] == "GET")
{
   if (check_owner($SESSID_USERNAME, $fDomain) || authentication_has_role('global-admin'))
   {
      $tName = $user_details['name'];
      $tQuota = divide_quota($user_details['quota']);
      $tActive = $user_details['active'];
      if ('pgsql'==$CONF['database_type']) {
         $tActive = ('t'==$user_details['active']) ? 1 : 0;
      }

      $result = db_query ("SELECT * FROM $table_domain WHERE domain='$fDomain'");
      if ($result['rows'] == 1)
      {
         $row = db_array ($result['result']);
         $tMaxquota = $row['maxquota'];
      }
   }

$table_alias = table_by_key('alias');
$alias_list = array();
$orig_alias_list = array();
$result = db_query ("SELECT * FROM $table_alias WHERE address='$fUsername' AND domain='$fDomain'");
if ($result['rows'] == 1)
{
    $row = db_array ($result['result']);
    $tGoto = $row['goto'];

    $orig_alias_list = explode(',', $tGoto);
    $tGoto = str_replace(',', "\n", $tGoto);
    $alias_list = $orig_alias_list;
    /* Has a mailbox as well? Remove the address from $tGoto in order to edit just the real aliases */
    $result = db_query ("SELECT * FROM $table_mailbox WHERE username='$fUsername' AND domain='$fDomain'");
    if ($result['rows'] == 1)
    {
        $alias_list = array(); // empty it, repopulated again below
        foreach($orig_alias_list as $alias) {
            if(strtolower($alias) == strtolower($fUsername)) {
                // mailbox address is dropped if they don't have special_alias_control enabled, and/or not a global-admin 
            }
            else {
                $alias_list[] = $alias;
            }
        }
    }   
   // we unset the mailbox address alias_list array, but not the attribute that goes into the alias text area
   // update the value of tGoto my imploding the array we built above
   $tGoto = implode("\n", $alias_list);
}


}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel'])) {
    header("Location: list-virtual.php?domain=$fDomain");
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
   if (isset ($_POST['fPassword'])) $fPassword = escape_string ($_POST['fPassword']);
   if (isset ($_POST['fPassword2'])) $fPassword2 = escape_string ($_POST['fPassword2']);
   if (isset ($_POST['fName'])) $fName = escape_string ($_POST['fName']);
   if (isset ($_POST['fQuota'])) $fQuota = intval ($_POST['fQuota']);
   if (isset ($_POST['fActive'])) $fActive = escape_string ($_POST['fActive']);
   //get the value of the new aliases
   if (isset ($_POST['fGoto'])) $fGoto = escape_string ($_POST['fGoto']);
   $fGoto = strtolower ($fGoto);
   //do some string replacement so we end up with a set of CSVs
   $goto = preg_replace ('/\\\r\\\n/', ',', $fGoto);
   $goto = preg_replace ('/\r\n/', ',', $goto);
   $goto = preg_replace ('/,[\s]+/i', ',', $goto); 
   $goto = preg_replace ('/[\s]+,/i', ',', $goto); 
   $goto = preg_replace ('/,*$|^,*/', '', $goto);
   $goto = preg_replace ('/,,*/', ',', $goto);


    $new_aliases = array();
    if ($error != 1)
    {   
        $new_aliases = explode(',', $goto);
    }   
    $new_aliases = array_unique($new_aliases);

    foreach($new_aliases as $address) {
        if (in_array($address, $CONF['default_aliases'])) continue;
        if (empty($address)) continue; # TODO: should never happen - remove after 2.2 release
        if (!check_email($address))
        {
            $error = 1;
            $tGoto = $goto;
            if (!empty($tMessage)) $tMessage .= "<br />";
            $tMessage .= $PALANG['pEdit_alias_goto_text_error2'] . htmlentities($address) . "</span>"; 
        }
    }   

   $table_alias = table_by_key('alias');
   $orig_alias_list = array();
   $result = db_query ("SELECT * FROM $table_alias WHERE address='$fUsername' AND domain='$fDomain'");
   if ($result['rows'] == 1)
   {
      $row = db_array ($result['result']);
      $tGoto = $row['goto'];

      $orig_alias_list = explode(',', $tGoto);
   }

   // we are editing a mailbox alias, so ensure the updated one has a mail box alias
   $new_aliases[] = $fUsername;

    // duplicates suck, mmkay..
    $new_aliases = array_unique($new_aliases);

    $goto = implode(',', $new_aliases);

    if ($error != 1)
    {   
        $goto = escape_string($goto);
        $result = db_query ("UPDATE $table_alias SET goto='$goto',modified=NOW() WHERE address='$fUsername' AND domain='$fDomain'");
        if ($result['rows'] != 1)
        {
            $tMessage = $PALANG['pEdit_alias_result_error'];
        }
        else
        {
            db_log ($SESSID_USERNAME, $fDomain, 'edit_alias', "$fUsername -> $goto");
            header ("Location: list-virtual.php?domain=$fDomain");
            exit;
        }
    } else { # on error
        $tGoto = htmlentities($_POST['fGoto']);
    }




   if($fPassword != $user_details['password'] || $fPassword2 != $user_details['password']){
      $min_length = $CONF['min_password_length'];

      if($fPassword == $fPassword2) {
         if ($fPassword != "") {
            if($min_length > 0 && strlen($fPassword) < $min_length) {
               flash_error(sprintf($PALANG['pPasswordTooShort'], $CONF['min_password_length']));
               $error = 1;
            }
            $formvars['password'] = pacrypt($fPassword);
         }
      }
      else {
         flash_error($PALANG['pEdit_mailbox_password_text_error']);
         $error = 1;
      }
   }
   if ($CONF['quota'] == "YES")
   {
      if (!check_quota ($fQuota, $fDomain))
      {
         $error = 1;
         $tName = $fName;
         $tQuota = $fQuota;
         $tActive = $fActive;
         $pEdit_mailbox_quota_text = $PALANG['pEdit_mailbox_quota_text_error'];
      }
   }
   if ($error != 1)
   {
      if (!empty ($fQuota))
      {
         $quota = multiply_quota ($fQuota);
      }
      else
      {
         $quota = 0;
      }

      if ($fActive == "on")
      {
         $sqlActive = db_get_boolean(True);
         $fActive = 1;
      }
      else
      {
         $sqlActive = db_get_boolean(False);
         $fActive = 0;
      }

      $formvars['name'] = $fName;
      $formvars['quota'] =$quota;
      $formvars['active']=$sqlActive;
      if(preg_match('/^(.*)@/', $fUsername, $matches)) {
         $formvars['local_part'] = $matches[1];
      }
      $result = db_update('mailbox', "username='$fUsername' AND domain='$fDomain'", $formvars, array('modified'));
      $maildir = $user_details['maildir'];
      if ($result != 1 || !mailbox_postedit($fUsername,$fDomain,$maildir, $quota)) {
         $tMessage = $PALANG['pEdit_mailbox_result_error'];
      }
      else {
         db_log ($SESSID_USERNAME, $fDomain, 'edit_mailbox', $fUsername);

         $result = db_query ("UPDATE $table_alias SET active=$sqlActive WHERE address='$fUsername' AND domain='$fDomain'");
         if ($result['rows'] != 1)
         {
            $error = 1;
            $tMessage = $PALANG['pEdit_mailbox_result_error'];
         }
         else
         {
            db_log ($SESSID_USERNAME, $fDomain, 'edit_alias_state', $fUsername);
         }

         header ("Location: list-virtual.php?domain=$fDomain");
         exit(0);
      }
   } 
   else 
   {
      # error detected. Put the values the user entered in the form again.
      $tName = $fName;
      $tQuota = $fQuota;
      $tActive = $fActive;
   }
}

include ("templates/header.php");
include ("templates/menu.php");
include ("templates/edit-mailbox.php");
include ("templates/footer.php");
/* vim: set expandtab softtabstop=3 tabstop=3 shiftwidth=3: */
?>
