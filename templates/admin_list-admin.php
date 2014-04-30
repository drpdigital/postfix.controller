<?php if( !defined('POSTFIXADMIN') ) die( "This file cannot be used standalone." ); ?>
<?php 
if (sizeof ($list_admins) > 0): ?>
<div class="container">
   <table id="admin_table" class="table table-responsive table-striped">
      <thead>
         <tr class="header">
            <th><?php echo $PALANG['pAdminList_admin_username']; ?></th>
            <th><?php echo $PALANG['pAdminList_admin_count']; ?></th>
            <th><?php echo $PALANG['pAdminList_admin_modified']; ?></th>
            <th><?php echo $PALANG['pAdminList_admin_active']; ?></th>
            <th colspan="2">&nbsp;</th>
         </tr>
      </thead>
<?php
   for ($i = 0; $i < sizeof ($list_admins); $i++): 
      if ((is_array ($list_admins) and sizeof ($list_admins) > 0)):
?>
      <tr>
         <td><a href="list-domain.php?username=<?php echo $list_admins[$i] ?>"><?php echo $list_admins[$i]; ?></a></td>
<?php
         if ($admin_properties[$i]['domain_count'] == 'ALL'){
             $admin_properties[$i]['domain_count'] = $PALANG['pAdminEdit_admin_super_admin'];
         }
?>
         <td><?php echo $admin_properties[$i]['domain_count']; ?></td>
         <td><?php echo $admin_properties[$i]['modified']; ?></td>
         <?php $active = ($admin_properties[$i]['active'] == 1) ? $PALANG['YES'] : $PALANG['NO']; ?>
         <td><a href="edit-active-admin.php?username=<?php echo $list_admins[$i]; ?>"><?php echo $active; ?></a></td>
         <td><a href="edit-admin.php?username=<?php echo $list_admins[$i]; ?>"><?php echo $PALANG['edit']; ?></a></td>
         <td><a href="delete.php?table=admin&delete=<?php echo $list_admins[$i]; ?>" onclick="return confirm ('<?php echo $PALANG['confirm'] . $PALANG['pAdminList_admin_username'] . ': ' . $list_admins[$i]; ?>')"> <?php echo $PALANG['del']; ?></a></td>
      </tr>
<?php
		endif;
   endfor;
?>

</table>
<p><a href="create-admin.php"><?php echo $PALANG['pAdminMenu_create_admin']; ?></a>
</div>
<?php 
endif; 

