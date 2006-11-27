<?php
/* $Id$ */
include_once 'includes/init.php';

if ( ! $is_admin )
  $user = $login;

print_header ( array ( 'js/views.php/true', 'js/visible.php' ) );

ob_start ();

echo display_admin_link () . '
<!-- TABS -->
    <div id="tabs">
      <span class="tabfor" id="tab_views"><a href="#tabviews" onclick="return '
 . 'showTab (\'views\')>' . $translations['Views'] . '</a></span>
    </div>

<!-- TABS BODY -->
    <div id="tabscontent">
<!-- VIEWS -->
      <a name="tabviews"></a>
      <div id="tabscontent_views">
        <a title="' . translate ( 'Add New View' )
 . '" href="views_edit.php" target="viewiframe" onclick="javascript:show '
 . '(\'viewiframe\');">' . $translations['Add New View'] . '</a>
        <ul>';

$global_found = false;
for ( $i = 0, $cnt = count ( $views ); $i < $cnt; $i++ ) {
  if ( $views[$i]['cal_is_global'] != 'Y' || $is_admin ) {
    echo '
          <li><a title="' . htmlspecialchars ( $views[$i]['cal_name'] )
     . '" href="views_edit.php?id=' . $views[$i]['cal_view_id']
     . '" target="viewiframe" onclick="javascript:show (\'viewiframe\');">'
     . htmlspecialchars ( $views[$i]['cal_name'] ) . '</a>';
    if ( $views[$i]['cal_is_global'] == 'Y' ) {
      echo '&nbsp;<abbr title="' . $translations['Global'] . '">*</abbr>';
      $global_found = true;
    }
    echo '</li>';
  }
}

ob_end_flush ();

echo '
        </ul>' . ( $global_found ? '<br />
        *&nbsp;' . $translations['Global'] : '' ) . '<br />
        <iframe name="viewiframe" id="viewiframe" style="width: 90%; border: 0;'
 . ' height: 343px;"></iframe>
      </div>
    </div>
    ' . print_trailer ();

?>
