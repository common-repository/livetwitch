<?php

/**
 * Everthing related to the Cronjob that fetches Twitch streamer data
 *
 * @since      0.0.1
 *
 * @package    LiveTwitch
 * @subpackage LiveTwitch/includes
 * @author     Jerry Isaksson <jerry@allofesports.com>
 */
class TwitchCron {

    /**
	 * Register Twitch data fetch cronjob.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
    public function registerCron() {
		// and make sure it's called whenever WordPress loads
		add_action('wp', [$this, 'cronstarterActivation']);
		// add deactivation event
		register_deactivation_hook(__FILE__, [$this, 'cronstarterDeactivate']);
		// hook that function onto our scheduled event:
		add_action('twitch_live_cron', [$this, 'cronJob']);
    }
    
    /**
	 * Activate Twitch data fetch cronjob (default 10min interval).
	 *
	 * @since    0.0.1
	 * @access   public
     * @var      string $interval Interval for the cronjob (default 10min).
     */
    public function cronstarterActivation($interval = "5min") {
		if (! wp_next_scheduled('twitch_live_cron')) {
			wp_schedule_event(time(), "hourly", 'twitch_live_cron');
		}
    }
    
    /**
	 * Deactivate Twitch data fetch cronjob.
	 *
	 * @since    0.0.1
	 * @access   public
	 */
    public function cronstarterDeactivate() {
		// find out when the last event was scheduled
		$timestamp = wp_next_scheduled('twitch_live_cron');
		// unschedule previous event if any
		wp_unschedule_event($timestamp, 'twitch_live_cron');
    }
    

    /**
	 * Run cronjob that fetches Twitch data.
	 *
	 * @since    0.0.1
	 * @access   public
	 */
    public function cronJob() {
        $this->LiveTwitch_RemoveLiveStatus();
        $streamers = $this->getStreamData();
        $twitch_call = $this->LiveTwitch_FetchTwitchData($streamers);
        $update = $this->LiveTwitch_UpdateLiveStatus($twitch_call);
        /*
        if($update){
            $log_message = "Cron Success ".time()."\r\n";
        }else {
            $log_message = "Cron Failed ".time()."\r\n";
        }
        error_log($log_message);*/
    }
    
    /**
	 * Get all the streamers titles (Twitch username).
	 *
	 * @since    0.0.1
	 * @access   private
	 */
    private function getStreamData() {
        $streamers = array();
        $args = [
			'post_type' => 'twitch',
			'posts_per_page' => -1,
        ];

        $posts = new WP_Query($args);
		while ($posts->have_posts()) : $posts->the_post();
            $stream_id = get_the_ID();
            $stream_title = get_the_title();
            $streamers[$stream_id] = $stream_title;
        endwhile;
		wp_reset_postdata();
        return $streamers;
    }

    /**
	 * Fetch live streamers form Twitch with Curl.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
    private function LiveTwitch_FetchTwitchData($streamers) {
        $api_url = "https://api.twitch.tv/helix/streams?";
		$options = get_option( 'twitch_settings' );
        $client_id = $options['twitch_apikey'];
        
        $i = true;
        foreach($streamers as $stream => $title) {
            if($i == true) {
                $api_url .= "user_login=".strtolower($title);
                $i = false;
            }else {
                $api_url .= "&user_login=".strtolower($title);
            }
        }
        
        $args = array(
            'headers' => 'Client-ID: ' . $client_id,
            'sslverify'   => false,
            'ssl_verify'   => false,
        );
        $response = wp_remote_get( $api_url, $args);
        $json = json_decode($response['body']);
        return $json;

    }

    /**
	 * Remove live status from streamers.
	 *
	 * @since    0.0.1
	 */
    public function LiveTwitch_RemoveLiveStatus() {
        $args = array(
            'post_type' => 'twitch', // Only get the posts
            'post_status' => 'publish', // Only the posts that are published
            'posts_per_page'   => -1 // Get every post
        );
        $posts = new WP_Query($args);
        
        while ($posts->have_posts()) {
			$posts->the_post();
            $test = get_the_ID();
            update_post_meta( $test, 'type', 'offline');
        }
    }
    
    /**
	 * Update Twitch Streamer metadata.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $postId    The ID of the streamer.
	 * @var      array     $data      Array of the metadata.
	 */

    private function LiveTwitch_UpdateLiveStatus($data) {
        $metadatas = ['viewer_count', 'type','title','game_id','thumbnail_url'];
        $r = false;
        $d = $data;
        if(!isset($d->status) || $d->status != '401'){
                if(is_array($d)){
                    $d = $d['data'];
                }elseif(is_object($d)) {
                    $d = $d->data;
                }
                foreach($d as $stream){
                    $post = get_page_by_title($stream->user_name,OBJECT,'twitch');

                    if( !is_null($post) ){
                        $game_name = $this->LiveTwitch_GameNameById($stream->game_id);
                        foreach($metadatas as $meta){
                            $value = $stream->$meta;
                            if($meta == "game_id"){
                                $value = $game_name->data[0]->name;
                            }
                            
                            update_post_meta($post->ID, $meta, $value);
                            $r = true;
                        }
                    }
                }
            
        }else {
            error_log( print_r( $data, true ) );
        }
    }

    public function LiveTwitch_GameNameById($game_id) {
        $api_url = "https://api.twitch.tv/helix/games?";
		$options = get_option( 'twitch_settings' );
        $client_id = $options['twitch_apikey'];
        
        $api_url .= "id=".$game_id;
        
        $args = array(
            'headers' => 'Client-ID: ' . $client_id,
            'sslverify'   => false,
            'ssl_verify'   => false,
        );
        $response = wp_remote_get( $api_url, $args);
        $json = json_decode($response['body']);

        return $json;
    }
}
