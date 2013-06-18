<?php 
/**
 * This is a Anax pagecontroller.
 *
 */
// Include the essential config-file which also creates the $anax variable with its defaults.
include(__DIR__.'/config.php'); 


// Do it and store it all in variables in the Anax container.
$anax['header'] = <<<EOD
<img class='sitelogo' src='img/anax.png' alt='Anax Logo'/>
<span class='sitetitle'>Anax - en webbtemplate för PHP-projekt</span>
<span class='siteslogan'>Återanvändbara moduler för webbutveckling</span>
EOD;

$anax['main'] = <<<EOD
<h1>Hej Världen</h1>
<p>Detta är en exempelsida som visar hur Anax ser ut och fungerar.</p>
EOD;

$anax['footer'] = <<<EOD
<span class='sitefooter'>Copyright (c) Mikael Roos (me@mikaelroos.se) | <a href='https://github.com/mosbth/Anax-base'>Anax på GitHub</a></span>
EOD;


// Finally, leave it all to the rendering phase of Anax.
include(ANAX_THEME_PATH);
