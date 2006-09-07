<?php
/* $Id$ */
include_once 'includes/init.php';
$INC = array('js/edit_nonuser.php/false');
print_header( $INC, '', '', true );

if ( ! $is_admin ) {
  echo '<h2>' . translate( 'Error' ) . "</h2>\n" . 
    translate( 'You are not authorized' ) . ".\n";
  echo "</body>\n</html>";
  exit;
}
if ( ! $NONUSER_PREFIX ) {
  echo '<h2>' . translate( 'Error' ) . "</h2>\n" . 
    translate( 'NONUSER_PREFIX not set' ) . ".\n";
  echo "</body>\n</html>";
  exit;
}
$add = getValue ( 'add' );
$nid = getValue ( 'nid' );

// Adding/Editing nonuser calendar
if (( ($add == '1') || (! empty ($nid)) ) && empty ($error)) {
  $userlist = user_get_users ();
  $button = translate( 'Add' );
  $nid = clean_html($nid);
?>

<form action="edit_nonusers_handler.php" name="editnonuser" method="post" onsubmit="return valid_form(this);">
  <?php
  if ( ! empty ( $nid ) ) {
    nonuser_load_variables ( $nid, 'nonusertemp_' );
    $id_display = "$nid <input type=\"hidden\" name=\"nid\" value=\"$nid\" />";
    $button = translate( 'Save' );
    $nonusertemp_login = substr($nonusertemp_login, strlen($NONUSER_PREFIX));
  } else {
    $id_display = '<input type="text" name="nid" id="calid" size="20" onchange="check_name();" maxlength="20" /> ' . translate ( 'word characters only' );
  }
  if (! empty ( $nonusertemp_admin ) ){
    echo "<input type=\"hidden\" name=\"old_admin\" value=\"{$nonusertemp_admin}\" />";
  }
  ?>
<h2><?php
  if ( ! empty ( $nid ) ) {
 nonuser_load_variables ( $nid, 'nonusertemp_' );
 echo translate( 'Edit User' );
  } else {
 echo translate( 'Add User' );
  }
?></h2>
<table>
 <tr><td>
  <label for="calid"><?php etranslate( 'Calendar ID' )?>:</label></td><td>
  <?php echo $id_display ?>
 </td></tr>
 <tr><td>
  <label for="nfirstname"><?php etranslate( 'First Name' )?>:</label></td><td>
  <input type="text" name="nfirstname" id="nfirstname" size="20" maxlength="25" value="<?php echo empty ( $nonusertemp_firstname ) ? '' : htmlspecialchars ( $nonusertemp_firstname ); ?>" />
 </td></tr>
 <tr><td>
  <label for="nlastname"><?php etranslate( 'Last Name' )?>:</label></td><td>
  <input type="text" name="nlastname" id="nlastname" size="20" maxlength="25" value="<?php echo empty ( $nonusertemp_lastname ) ? '' : htmlspecialchars ( $nonusertemp_lastname ); ?>" />
 </td></tr>
 <tr><td>
  <label for="nadmin"><?php etranslate( 'Admin' )?>:</label></td><td>
  <select name="nadmin" id="nadmin">
<?php
  for ( $i = 0, $cnt = count ( $userlist ); $i < $cnt; $i++ ) {
 echo '<option value="' .$userlist[$i]['cal_login']. '"';
 if (! empty ( $nonusertemp_admin ) &&
            $nonusertemp_admin == $userlist[$i]['cal_login'] ) 
  echo ' selected="selected"';
 echo '>' . $userlist[$i]['cal_fullname']."</option>\n";
  }
?>
  </select>
 </td></tr>

<?php if ( ! $use_http_auth ) { ?>
 <tr><td valign="top"><label for="ispublic"><?php
   etranslate( 'Is public calendar' );?>:</td>
 <td><input type="radio" name="ispublic" value="Y" <?php
   if ( ! empty ( $nonusertemp_is_public ) &&
     $nonusertemp_is_public == 'Y' ) echo ' checked="checked"';
   echo "> " . translate ( 'Yes' ) . "&nbsp;&nbsp;\n";?>
 <input type="radio" name="ispublic" value="N" <?php
   if ( empty ( $nonusertemp_is_public ) ||
     $nonusertemp_is_public != 'Y' ) echo ' checked="checked"';
   echo '> ' . translate ( 'No' );?><br />
 <?php if ( ! empty ( $nonusertemp_login ) ) {
                $nu_url = $SERVER_URL . "nulogin.php?login=$nonusertemp_login";
                echo "<a href=\"$nu_url\">$nu_url</a>\n";
              }
        ?>
 </td></tr>
<?php } ?>
</table>
  <br />
  <input type="submit" name="action" value="<?php echo $button;?>" />
  <?php if ( ! empty ( $nid ) ) {  ?>
    <input type="submit" name="delete" value="<?php etranslate( 'Delete' );?>" onclick="return confirm('<?php etranslate( 'Are you sure you want to delete this entry?', true); ?>')" />
  <?php }  ?>
  </form>
<?php }
echo print_trailer ( false, true, true ); ?>

