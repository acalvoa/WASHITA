<?php

if(!isset($SCRIPTS_FOOTER)){
  $SCRIPTS_FOOTER = "";
}

/*
$SCRIPTS_FOOTER.="
<!-- begin SnapEngage code -->
<script type='text/javascript'>
  (function() {
    var se = document.createElement('script'); se.type = 'text/javascript'; se.async = true;
    se.src = '//storage.googleapis.com/code.snapengage.com/js/37191446-dba1-4394-8d74-ed6cb2422c87.js';
    var done = false;
    se.onload = se.onreadystatechange = function() {
      if (!done&&(!this.readyState||this.readyState==='loaded'||this.readyState==='complete')) {
        done = true;
        /* Place your SnapEngage JS API code below */
        /* SnapEngage.allowChatSound(true); Example JS API: Enable sounds for Visitors. *//*


      }
    };
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(se, s);
  })();
</script>
<!-- end SnapEngage code -->
";*/

    
include_once(dirname(__FILE__)."/footer.php");   
?>

        