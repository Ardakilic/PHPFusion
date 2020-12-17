<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: header.php
| Author: Core Development Team (coredevs@phpfusion.com)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) {
    die("Access Denied");
}


// Check if Maintenance is Enabled
if ($settings['maintenance'] == "1" && ((iMEMBER && $settings['maintenance_level'] == "1"
            && $userdata['user_id'] != "1") || ($settings['maintenance_level'] > $userdata['user_level'])
    )) {
    redirect(BASEDIR."maintenance.php");
}

if ($settings['site_seo']) {
    $permalink = \PHPFusion\Rewrite\Permalinks::getPermalinkInstance();
}

require_once INCLUDES."output_handling_include.php";
require_once INCLUDES."breadcrumbs.php";
if (file_exists(INCLUDES.'header_includes.php')) {
    require_once INCLUDES."header_includes.php";
}
include_once THEMES.'templates/dynamics.micro.php';
require_once THEME."theme.php";
require_once INCLUDES."theme_functions_include.php";
require_once THEMES."templates/render_functions.php";

if (iMEMBER) {
    $result = dbquery("UPDATE ".DB_USERS." SET user_lastvisit='".time()."', user_ip='".USER_IP."', user_ip_type='".USER_IP_TYPE."' WHERE user_id='".$userdata['user_id']."'");
}

if (fusion_get_settings('debug_seo') && iSUPERADMIN && iADMIN) {
    $router = PHPFusion\Rewrite\Router::getRouterInstance();
    $router->displayWarnings();
}

ob_start();
