<link rel="stylesheet" type="text/css" href="<?php echo plugins_url( 'styles/dashboard.css', dirname( __FILE__ ) ) ?>">

<?php $momently_script = get_option( 'momently_script' ); ?>

<?php if ( $momently_script ) { ?>
    <div id="momently_blocked" style="display:none;position: absolute;z-index: -1; top:60px;left:60px;font-size:24px;line-height:35px">
        If you are seeing this, your adBlocker is blocking Momently. <br>
        You will need to either disable your adBlocker or whitelist your own site : <?php print get_bloginfo( 'wpurl' ) ?>
        <br><br>
        Why Momently is blocked by adBlocker ?<br>
        Generally speaking adBlockers privacy lists block all the analytics platform out there.
    </div>
    <iframe id="momently-iframe"
            style="padding:0; min-height: 800px; width: 100%;display:block; height: calc(100vh - 32px);"
            src="<?php echo esc_url( 'https://momently.com/?platform=wordpress' ); ?>"></iframe>
<?php } else {
    $momently_access_url = add_query_arg( array(
        'site'     => get_bloginfo( 'wpurl' ),
        'name'     => get_bloginfo( 'name' ),
        'platform' => 'wordpress',
        'next'     => 'access'
    ), 'https://momently.com/access' );
    ?>
    <div id="momently_blocked" style="display:none;position: absolute;z-index: -1; top:60px;left:60px;font-size:24px;line-height:35px">
        If you are seeing this, your adBlocker is blocking Momently. <br>
        You will need to either disable your adBlocker or whitelist your own site : <?php print get_bloginfo( 'wpurl' ) ?>
        <br><br>
        Why Momently is blocked by adBlocker ?<br>
        Generally speaking adBlockers privacy lists block all the analytics platform out there.
    </div>
    <iframe id="momently-iframe"
            style="padding:0; min-height: 800px; width: 100%;display:block; height: calc(100vh - 32px);"
            src="<?php echo esc_url( $momently_access_url ); ?>"></iframe>
    <?php
}
?>
