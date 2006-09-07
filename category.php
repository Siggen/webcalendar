<?php
/* $Id$ */
include_once 'includes/init.php';

// load user and global cats
load_user_categories ();

$error = '';

$icon_path = 'icons/';

if ( $CATEGORIES_ENABLED == 'N' ) {
  send_to_preferred_view ();
  exit;
}

// If editing, make sure they are editing their own
// (or they are an admin user).
if ( ! empty ( $id ) ) {
  $res = dbi_execute ( 'SELECT cat_id, cat_owner FROM webcal_categories WHERE ' .
    'cat_id = ?', array( $id ) );
  if ( $res ) {
    if ( $row = dbi_fetch_row ( $res ) ) {
      if ( $row[0] != $id )
        $error = translate( 'Invalid entry id' ) . ': ' . $id;
      else if ( $row[1] != $login && ! $is_admin )
        $error = translate ( 'You are not authorized' ) . '.';
    }
    dbi_free_result ( $res );
  } else {
    $error = db_error();
  }
}

print_header();
?>
<h2><?php etranslate( 'Categories' )?></h2>
<?php
echo display_admin_link();

if ( empty ( $add ) )
  $add = 0;

// Adding/Editing category
if ( ( ( $add == '1' ) || ( ! empty ( $id ) ) ) && empty ( $error ) ) {
  $button = translate( 'Add' );
  ?>
  <form action="category_handler.php" method="post" name="catform" enctype="multipart/form-data">
  <?php
  if ( ! empty ( $id ) ) {
    echo "<input name=\"id\" type=\"hidden\" value=\"$id\" />";
    $button = translate( 'Save' );
    $catname = $categories[$id];
    $catowner = $category_owners[$id];
    $catIcon = $icon_path . 'cat-' . $id . '.gif';
  } else {
    $catname = '';
  }

  etranslate( 'Category Name' )?>: <input type="text" name="catname" size="20" value="<?php   echo htmlspecialchars ( $catname ); ?>" />
  <br />
  <?php if ( ! empty ( $catIcon ) && file_exists ( $catIcon ) ){
      echo '<br />' . translate ( 'Category Icon' ) . ":  <img src=\"$catIcon\" alt=\"" . translate( 'Category Icon' ) . "\" /><br />\n";
      echo translate( 'Remove Icon' ) . 
        ':&nbsp;<input type="checkbox" name="delIcon" value="Y" />';
  }
  if ( $is_admin && empty ( $id ) ) {
    etranslate( 'Global' )?>:
      <label><input type="radio" name="isglobal" value="N" <?php if ( ! empty ( $catowner ) || empty ( $id ) ) echo ' checked="checked"';?> />&nbsp;<?php etranslate ( 'No')?></label>&nbsp;&nbsp;
      <label><input type="radio" name="isglobal" value="Y" <?php if ( empty ( $catowner ) && ! empty ( $id ) ) echo ' checked="checked"';?> />&nbsp;<?php etranslate ( 'Yes' )?></label>
  <?php } ?>
  <br /><br />
<?php if ( is_dir($icon_path) && ( ! empty ( $ENABLE_ICON_UPLOADS ) &&
  $ENABLE_ICON_UPLOADS == 'Y' || $is_admin )) { 
  echo translate ( 'Add Icon to Category' ) . '<br />&nbsp;&nbsp;&nbsp;'.
   translate ( 'Upload' ) . '&nbsp;<span style="font-size:small;">(' . 
   translate ('gif 3kb max') . ')</span> :'; ?>
 <input type="file" name="FileName" id="fileupload" size="45" maxlength="50" /> 
 <br />
 <br />
  <input type="hidden" name="urlname" size="50"   />
  &nbsp;&nbsp;&nbsp;<input type="button" value="<?php 
    etranslate ( 'Search for existing icons', true);?>" onclick="window.open('icons.php', 'icons','dependent,menubar=no,scrollbars=n0,height=300,width=400, outerHeight=320,outerWidth=420');" />
  &nbsp;&nbsp;&nbsp;<img src="" name="urlpic" id="urlpic" >
  <br /><br />
<?php } //end test of ENABLE_ICON_UPLOADS ?>
  <input type="submit" name="action" value="<?php echo $button;?>" />
  <?php if ( ! empty ( $id ) ) {  ?>
 <input type="submit" name="delete" value="<?php etranslate( 'Delete' );?>" onclick="return confirm('<?php etranslate( 'Are you sure you want to delete this entry?', true); ?>')" />
  <?php }  ?>
  </form>
  <?php
} else if ( empty ( $error ) ) {
  // Displaying Categories
  $global_found = false;
  if ( ! empty ( $categories ) ) {
    echo '<ul>';
    foreach ( $categories as $K => $V ) {
      if ( $K <1 ) continue;
      $catIcon = $icon_path. 'cat-' . $K . '.gif';
      echo '<li>';
      if ( $category_owners[$K] == $login || $is_admin )
        echo "<a href=\"category.php?id=$K\">$V</a>";
      else
        echo $V;
      if ( empty ( $category_owners[$K] ) ) {
        echo '<sup>*</sup>';
        $global_found = true;
      }
      if ( file_exists ( $catIcon ) ){
        echo "&nbsp;&nbsp;<img src=\"$catIcon\" alt=\"" . 
          translate( 'Category Icon' ) . '" title="' . 
          translate( 'Category Icon' ) . "\" />\n";
      }
      echo "</li>\n";
    }
    echo '</ul>';
  }
  if ( $global_found )
    echo "<br /><br />\n<sup>*</sup> " . translate ( 'Global' );
  echo '<p><a href="category.php?add=1">' . translate( 'Add New Category' ) . "</a></p><br />\n";
}

if ( ! empty ( $error ) ) {
  echo "<span style=\"font-weight:bold;\">" . translate ( 'Error' ) . ':</span>' . $error;
}
echo print_trailer(); ?>
