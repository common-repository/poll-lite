<div class="wrap merlic_poll">
    <h2>Poll Settings</h2>
    <div id="navmenu">
        <ul>
            <li>
                <a href="<?php echo '?page=merlic_poll_admin&tab=preferences'; ?>">Preferences</a>|
            </li>
            <li>
                <a href="<?php echo '?page=merlic_poll_admin&tab=uninstall'; ?>">Uninstall</a>|
            </li>
        </ul>
    </div>
    <?php 
    switch ($_GET['tab']) {
    	case 'preferences':
		default:
    		include 'preferences.php';
    		break;
    		
    	case 'uninstall':
    		include 'uninstall.php';
    		break;
    }
	
    ?>
</div>