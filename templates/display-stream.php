<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access


?>
<div class="card twitch">
    <div class="card-image">
        <img src="images/sample-1.jpg">
        <span class="card-streamer"><?php echo $twitch_streamer;?></span>
    </div>
    <div class="card-stream-title">
       <p><?php echo $stream_title;?></p>
    </div>
    <div class="card-meta">
        <span class="card-viewers"><?php echo $viewers;?></span>
        <span class="card-game"><?php echo $game;?></span>
    </div>
</div>