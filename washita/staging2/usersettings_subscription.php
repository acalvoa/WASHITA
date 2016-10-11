<?php
require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");

require_once(dirname(__FILE__)."/php/hybridauth/WashitaUserSettings.php");

$userSettings = new WashitaUserSettings();

include_once(dirname(__FILE__)."/templates/usersettings_header.php");
    
?>
<section>
         <div class="section-heading section-order">
                <h1>Subscription</h1>
                <div class="divider"></div>
                <form method="post"> 
                    <p>Subscription information</p>
                    
                    <div class="row text-right">
                        <div class="col col-sm-12">
                            <input type="submit" class="btn btn-info" value="Save" />
                        </div>
                    </div>
                </form>
        </div>
</section>

<?php
    include_once(dirname(__FILE__)."/templates/usersettings_footer.php");
?>