<?php
/**
 * List View Loop
 * This file sets up the structure for the list loop
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list/loop.php
 *
 * @version 4.4
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php
global $post;
global $more;
$more = false;

// Get array of venues (per day??) and sort alphabetically
$venues = tribe_get_venues($only_with_upcoming = true);
sort($venues);

$conf_events = tribe_get_events(array());
/* ?> <pre> <?php var_dump($conf_events); ?></pre>
<?
*/

/*  Get array of events, with heading only when start day changes 
From https://theeventscalendar.com/support/forums/topic/how-to-customize-the-upcoming-events-loop/ 
*/
// $different_day =  '';
     //Grab  current event date
     // $eventDate = tribe_get_start_date($post->ID, true, $format = 'l, F j, Y' ); 
         
      //If the current event date does not match the variable, print the date heading
     // if ($different_day != $eventDate){
       //  echo "<h2>".$eventDate."</h2>"; }
         
        //Update the date variable to the most recent event
      // $different_day = $eventDate; 
     ?>
         
<!-- <table class="tribe-events-calendar">
		<thead>
		<tr>
			<th>Room</th>
			<?php foreach ( $venues as $venue ) : ?>
				<th title="<?php echo esc_attr( $venue->post_title ); ?>" ><?php echo $venue->post_title ?></th>
			<?php endforeach; ?>
		</tr>
		</thead>
		<tbody>
		<tr>
			<?php while ( tribe_events_have_month_days() ) : tribe_events_the_month_day(); ?>
			<?php if ( $week != tribe_events_get_current_week() ) : $week ++; ?>
		</tr>
		<tr>
			<?php endif; ?>

			<?php
			// Get data for this day within the loop.
			$daydata = tribe_events_get_current_month_day(); ?>

			<td>
				<?php tribe_get_template_part( 'month/single', 'day' ) ?>
			</td>
			<?php endwhile; ?>
		</tr>
		</tbody>
	</table> -->


<div class="tribe-events-loop">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php do_action( 'tribe_events_inside_before_loop' ); ?>

		<!-- Month / Year Headers -->
		<?php tribe_events_list_the_date_headers(); ?>

		<!-- Event  -->
		<?php
		$post_parent = '';
		if ( $post->post_parent ) {
			$post_parent = ' data-parent-post-id="' . absint( $post->post_parent ) . '"';
		}
		?>
		<div id="post-<?php the_ID() ?>" class="<?php tribe_events_event_classes() ?>" <?php echo $post_parent; ?>>
			<?php
			$event_type = tribe( 'tec.featured_events' )->is_featured( $post->ID ) ? 'featured' : 'event';

			/**
			 * Filters the event type used when selecting a template to render
			 *
			 * @param $event_type
			 */
			$event_type = apply_filters( 'tribe_events_list_view_event_type', $event_type );

			tribe_get_template_part( 'list/single', $event_type );
			?>
		</div>


		<?php do_action( 'tribe_events_inside_after_loop' ); ?>
	<?php endwhile; ?>

</div><!-- .tribe-events-loop -->
