<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: go.php
| Author: PHP-Fusion Development Team
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once __DIR__.'/../maincore.php';
pageAccess('SU');
include THEME."theme.php";
include THEMES.'templates/render_functions.php';

$urlprefix = "";
$url = BASEDIR."index.php";

if (isset($_GET['id']) && isnum($_GET['id'])) {
    $id = form_sanitizer($_GET['id'], '', 'id');
    $result = dbquery("SELECT submit_criteria
        FROM ".DB_SUBMISSIONS."
        WHERE submit_type=:typ AND submit_id=:id", [':typ' => 'l', ':id' => $id]
    );
    if (dbrows($result)) {
        $data = dbarray($result);
        $submit_criteria = unserialize($data['submit_criteria']);
        if (!strstr($submit_criteria['link_url'], "http://") && !strstr($submit_criteria['link_url'], "https://")) {
            $urlprefix = "http://";
        } else {
            $urlprefix = "";
        }
        $url = $submit_criteria['link_url'];
    }
}

ob_start();

echo '<!DOCTYPE html>';
echo '<html dir="'.fusion_get_locale('text-direction').'">';
echo '<head>';
echo '<meta charset="'.fusion_get_locale('charset').'"/>';
echo '<title>'.fusion_get_settings('sitename').'</title>';
echo '<link rel="stylesheet" type="text/css" href="'.THEME.'styles.css"/>';
if (!defined('NO_DEFAULT_CSS')) {
    echo '<link rel="stylesheet" type="text/css" href="'.THEMES.'templates/default.min.css"/>';
}
echo '<meta http-equiv="refresh" content="2; url='.$urlprefix.$url.'" />';
echo render_favicons(defined('THEME_ICON') ? THEME_ICON : IMAGES.'favicons/');
echo '</head>';
echo '<body>';
echo '<div class="align-center" style="margin-top: 15%;">';
echo '<img src="'.BASEDIR.fusion_get_settings('sitebanner').'" alt="'.fusion_get_settings('sitename').'"/><br/>';
echo '<a href="'.$urlprefix.$url.'" rel="nofollow">'.sprintf($locale['global_500'], $urlprefix.$url).'</a>';
echo '</div>';

echo \PHPFusion\OutputHandler::$pageFooterTags;

$fusion_jquery_tags = PHPFusion\OutputHandler::$jqueryTags;
if (!empty($fusion_jquery_tags)) {
    $minifier = new PHPFusion\Minify\JS($fusion_jquery_tags);
    echo "<script type='text/javascript'>$(function(){".$minifier->minify()."});</script>\n";
}
echo '</body>';
echo '</html>';

$output = ob_get_contents();
if (ob_get_length() !== FALSE) {
    ob_end_clean();
}
$output = handle_output($output);
echo $output;
if ((ob_get_length() > 0)) {
    ob_end_flush();
}
