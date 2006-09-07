<?php
/*
 * $Id$
 *
 * Description:
 *  Display the system activity log.
 *
 * Input Parameters:
 *  startid - specified the id of the first log entry to display
 *
 * Security:
 *  User must be an admin user
 *  AND, if user access control is enabled, they must have access to
 *  activity logs.  (This is because users may see event details
 *  for other groups that they are not supposed to have access to.)
 */
include_once 'includes/init.php';

$PAGE_SIZE = 25; // number of entries to show at once

if ( ! $is_admin || ( access_is_enabled () &&
  ! access_can_access_function ( ACCESS_ACTIVITY_LOG ) ) ) {
  die_miserable_death ( translate ( 'You are not authorized' ) );
}

print_header();

$startid = getIntValue ( 'startid' );

echo '<h2>' . translate( 'Activity Log' ) . "</h2>\n";

echo display_admin_link();

echo "<table class=\"embactlog\">\n";
echo "<tr><th class=\"usr\">\n" .
  translate( 'User' ) . "</th><th class=\"cal\">\n" .
  translate( 'Calendar' ) . "</th><th class=\"scheduled\">\n" .
  translate( 'Date' ) . "/" . translate( 'Time' ) . "</th><th class=\"dsc\">\n" .
  translate( 'Event' ) . "</th><th class=\"action\">\n" .
  translate( 'Action' ) . "\n</th></tr>\n";
$sql = 'SELECT webcal_entry_log.cal_login, webcal_entry_log.cal_user_cal, ' .
  'webcal_entry_log.cal_type, webcal_entry_log.cal_date, ' .
  'webcal_entry_log.cal_time, webcal_entry_log.cal_text, webcal_entry.cal_id, ' .
  'webcal_entry.cal_name, webcal_entry_log.cal_log_id, webcal_entry.cal_type ' .
  'FROM webcal_entry_log, webcal_entry ' .
  'WHERE webcal_entry_log.cal_entry_id = webcal_entry.cal_id ';
$startid = getIntValue ( 'startid', true );
if ( ! empty ( $startid ) )
  $sql .= "AND webcal_entry_log.cal_log_id <= $startid ";
$sql .= 'ORDER BY webcal_entry_log.cal_log_id DESC';
$res = dbi_execute ( $sql );

$nextpage = '';

if ( $res ) {
  $num = 0;
  while ( $row = dbi_fetch_row ( $res ) ) {
    $num++;
    if ( $num > $PAGE_SIZE ) {
      $nextpage = $row[8];
      break;
    } else {
      echo '<tr';
      if ( $num % 2 ) {
        echo ' class="odd"';
      }
      $view_link = 'view_entry';      
      echo "><td>\n" .
      $row[0] . "</td><td>\n" .
      $row[1] . "</td><td>\n" . 
      date_to_str ( $row[3] ) . '&nbsp;' ;
      // Added TZ conversion
      $use_gmt = ( ! empty ( $GENERAL_USE_GMT ) && $GENERAL_USE_GMT == 'Y' ? 3 : 2 );
      echo display_time ( $row[3] . $row[4], $use_gmt ) ;
      echo "</td><td>\n" . '<a title="' .
      htmlspecialchars($row[7]) . "\" href=\"$view_link.php?id=$row[6]\">" .
      htmlspecialchars($row[7]) . "</a></td><td>\n";
      echo display_activity_log ( $row[2] );
      echo "\n</td></tr>\n";
    }
  }
  dbi_free_result ( $res );
} else {
  echo db_error ();
}
?>
</table>
<div class="navigation">
<?php
//go BACK in time
if ( ! empty ( $nextpage ) ) {
  echo '<a title="' . 
    translate( 'Previous' ) . "&nbsp;$PAGE_SIZE&nbsp;" . 
    translate ( 'Events' ) .
    "\" class=\"prev\" href=\"activity_log.php?startid=$nextpage\">" . 
    translate( 'Previous' ) . "&nbsp;$PAGE_SIZE&nbsp;" . 
    translate ( 'Events' ) . "</a>\n";
}

if ( ! empty ( $startid ) ) {
  $previd = $startid + $PAGE_SIZE;
  $res = dbi_execute ( 'SELECT MAX(cal_log_id) FROM webcal_entry_log' );
  if ( $res ) {
    if ( $row = dbi_fetch_row ( $res ) ) {
      if ( $row[0] <= $previd ) {
        $prevarg = '';
      } else {
        $prevarg = "?startid=$previd";
      }
      //go FORWARD in time
      echo '<a title="' .  translate( 'Next' ) . "&nbsp;$PAGE_SIZE&nbsp;" . 
        translate ( 'Events' ) .
        "\" class=\"next\" href=\"activity_log.php$prevarg\">" . 
        translate( 'Next' ) . "&nbsp;$PAGE_SIZE&nbsp;" . 
        translate ( 'Events' ) . "</a><br />\n";
    }
    dbi_free_result ( $res );
  }
}
?>
</div>
<?php echo print_trailer(); ?>

