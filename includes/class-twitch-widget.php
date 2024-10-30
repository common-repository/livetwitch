<?php
/**
 * Handles all functionality needed for LiveTwitch widget.
 *
 * @since      0.0.1
 *
 * @package    LiveTwitch
 * @subpackage LiveTwitch/includes
 * @author     Jerry Isaksson <jerry@allofesports.com>
 */
class LiveTwitchWidget extends WP_Widget {

    function __construct() {
        // Instantiate the parent object
        parent::__construct( false, 'LiveTwitch' );
        
    }

    /**
     * 
	 * Display LiveTwitch widget.
	 *
	 * @since    0.0.1
	 */
    public function widget( $args, $instance ) {
        $title = apply_filters('widget_title', $instance['title']);
		$options = get_option( 'twitch_settings' );
        $offline_text = $options['twitch_offlinetext'];

        echo $args['before_widget'];
        if( !empty($title)){
            echo "<h3>" . $title . "</h3>";
        } 

        $streamers = $this->LiveTwitch_GetStreams();
        $list = "";

        if( is_array($streamers) && !empty($streamers) ){
            foreach($streamers as $stream){
                $stream = array_map(array('LiveTwitchWidget','LiveTwitch_DisplayString'), $stream);
                echo LiveTwitch::display_stream($stream);
            }
        }else{
            if($offline_text == ''){
                $offline_text = "No live streams.";
            }
            echo "<p>".$offline_text."</p>";
        }

        echo $args['after_widget'];
    }

    /**
     * 
	 * Helper function that changes empty strings to N/A. 
	 *
	 * @since    0.0.1
     * 
	 * @var      string    $string    String to alter.
	 */
    static public function LiveTwitch_DisplayString($string) {
        if (is_array($string)){
            foreach ($string as $k => $v){
                $string[$k] = LiveTwitchWidget::LiveTwitch_DisplayString($v);
            }
            return $string;
        }elseif(empty($string)){
                $string = "N/A";
        }
        return $string;
    }

    /**
     * 
	 * Fetches streams (CPT) with given arguments and returns.
	 *
	 * @since    0.0.1
     * 
	 * @var      int       $count      Number of streams to fetch.
	 * @var      string    $orderby    Set the order for query (viewers,title,stream_title,game).
     * @var      string    $order      DESC or ASC.
	 */
    static public function LiveTwitch_GetStreams($count = 5, $orderby = "viewers", $order = "DESC") {

        $orderby = mb_convert_case($orderby, MB_CASE_LOWER, "UTF-8"); 
        $order = mb_convert_case($order, MB_CASE_UPPER, "UTF-8"); 

        if(! is_numeric($count)){
            $count = 5;
        }
        if($order != 'ASC' && $order != 'DESC'){
            $order = "DESC";
        }

        $customfields = ['viewer_count', 'title', 'game_id'];
        if(!in_array($orderby, $customfields)){
            $orderby = "viewer_count";
        }
        
        if($orderby == "Title" || $orderby == "title"){
            $args = [
                'post_type' => 'twitch',
                'post_status' => 'publish',
                'posts_per_page' => $count,
                'orderby' => 'post_title',
                'order' => $order,
                'meta_query' => array(
                    'key'   => 'type',    
                    'value' => 'live',
                ),
            ];
        }else {
            $args = [
                'post_type' => 'twitch',
                'post_status' => 'publish',
                'posts_per_page' => $count,
                'meta_key' => $orderby,
                'orderby' => 'meta_value_num',
                'order' => $order,
                'meta_query' => array(
                    'key'   => 'type',    
                    'value' => 'live',
                ),
            ];
        }

        

        
        $streams = [];
		$posts = new WP_Query($args);
		while ($posts->have_posts()) {
            $viewers = $game = $link = $image = $stream_title = "";
			$posts->the_post();
            $streamId = get_the_ID();
            $cf = get_post_custom();

            if(isset($cf['viewer_count'])){
                $viewers = $cf['viewer_count'][0];
            }
            if(isset($cf['game_id'])){
                $game = $cf['game_id'][0];
            }
            if(isset($cf['link'])){
                $link = $cf['link'][0];
            }
            if(isset($cf['thumbnail_url'])){
                $image = $cf['thumbnail_url'][0];
                $image = str_replace('{width}', '250', $image);
                $image = str_replace('{height}', '150', $image);
            }
            if(isset($cf['title'])){
                $stream_title = $cf['title'][0];
            }

            $data = [
                'title'         => get_the_title(),
                'stream_title'  => $stream_title,
                'viewers'       => $viewers,
                'game'          => $game,
                'link'          => $link,
                'image'         => $image,
            ];
            $streams[$streamId] = $data;
            

		}
		wp_reset_postdata();




		return $streams;
    }
    
    /**
     * 
	 * Updates widget settings.
	 *
	 * @since    0.0.1
     * 
	 */
    public function update( $new_instance, $old_instance ) {
        // Save widget options
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['count'] = ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';
        return $instance;
    }

    /**
     * 
	 * Create and display widget setting form.
	 *
	 * @since    0.0.1
     * 
	 */
    public function form( $instance ) {
        // Output admin widget options form
        if( isset($instance['title']) ){
            $title = $instance['title'];
        } else {
            $title = __('LiveTwitch', 'twitch');
        }
        if( isset($instance['count']) ){
            $count = $instance['count'];
        } else {
            $count = 5;
        }
        ?>
        <p>
          <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _ex( 'Count', 'Number/count of items' ); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
        </p>
        <?php
    }
}

//Register widget and init
function LiveTwitch_RegisterWidgets() {
    register_widget( 'LiveTwitchWidget' );
}

add_action( 'widgets_init', 'LiveTwitch_RegisterWidgets' );