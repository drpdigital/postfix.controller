<?php if( !defined('POSTFIXADMIN') ) { die( "This file cannot be used standalone." ); } ?>
    <footer class="container">
        <div class="row">
            <div class="col-sm-12 col-md-2 col-md-offset-1"> 
                <a target="_blank" href="http://postfixadmin.sf.net/">Postfix Admin <?php print $version; ?></a>
            </div>
            <div class="col-sm-12 col-md-2">
                <?php 
                if(isset($_SESSION['sessid']['username'])) {
                    printf($PALANG['pFooter_logged_as'], authentication_get_username());
                }
                ?> 
            </div>
            <div class="col-sm-12 col-md-2">
                <a target="_blank" href="http://postfixadmin.sf.net/update-check.php?version=<?php print $version; ?>"><?php print $PALANG['check_update']; ?></a>
            </div>
            <div class="col-sm-12 col-md-4">
            <?php
            if (($CONF['show_footer_text'] == "YES") and ($CONF['footer_link'])): ?>
                <a href="<?php echo $CONF['footer_link']; ?>"><?php echo $CONF['footer_text'] ?></a>
            <?php
            endif;
            ?>
        </div>
    </footer>
</body>
</html>
