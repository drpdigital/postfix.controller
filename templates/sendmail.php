<?php if( !defined('POSTFIXADMIN') ) die( "This file cannot be used standalone." ); ?>
<div class="container">
<form name="mailbox" method="post">
<table class="table table-responsive table-striped">
   <tr>
      <td colspan="3"><h3><?php print $PALANG['pSendmail_welcome']; ?></h3></td>
   </tr>
   <tr>
      <td><?php print $PALANG['pSendmail_admin'] . ":"; ?></td>
      <td><?php print $SESSID_USERNAME; ?></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td><?php print $PALANG['pSendmail_to'] . ":"; ?></td>
      <td><input class="form-control" type="text" name="fTo" /></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td><?php print $PALANG['pSendmail_subject'] . ":"; ?></td>
      <td><input class="form-control" type="text" name="fSubject" value="<?php print $PALANG['pSendmail_subject_text']; ?>" /></td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td><?php print $PALANG['pSendmail_body'] . ":" ?></td>
      <td>
      <textarea class="form-control" rows="10" cols="60" name="fBody"><?php print $CONF['welcome_text']; ?></textarea>
      </td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td colspan="3" class="hlp_center"><input class="btn btn-primary" type="submit" name="submit" value="<?php print $PALANG['pSendmail_button']; ?>" /></td>
   </tr>
   <tr>
      <td colspan="3" class="standout"><?php print $tMessage; ?></td>
   </tr>
</table>
</form>
</div>
