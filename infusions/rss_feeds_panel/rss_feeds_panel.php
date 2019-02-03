<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: rss_feeds_panel.php
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
if (!defined("IN_FUSION")) { die("Access Denied"); }

if (file_exists(INFUSIONS."rss_feeds_panel/locale/".LANGUAGE."/rss.php")) {
	include INFUSIONS."rss_feeds_panel/locale/".LANGUAGE."/rss.php";
} else {
	include INFUSIONS."rss_feeds_panel/locale/English/rss.php";
}

add_to_head("
<style type='text/css'>
<!--
.rss-button {
    background: #FF9800;
    padding: 1px 15px;
    color: #fff!important;
    -webkit-border-radius: 4px;
    border-radius: 4px;
    margin: 3px 0;
    display: block;
}
.rss-button:hover,
.rss-button:focus {
    background: #F57C00;
    color: #fff;
    text-decoration: none;
}
.rss-button .fa {
    padding-right: 5px;
}
-->
</style>
");

openside($locale['rss_title']);
echo '<a href="'.INFUSIONS.'rss_feeds_panel/feeds/rss_articles.php" target="_blank" class="rss-button"><i class="entypo rss"></i> '.$locale['rss_articles'].'</a>';
echo '<a href="'.INFUSIONS.'rss_feeds_panel/feeds/rss_blog.php" target="_blank" class="rss-button"><i class="entypo rss"></i> '.$locale['rss_blog'].'</a>';
echo '<a href="'.INFUSIONS.'rss_feeds_panel/feeds/rss_downloads.php" target="_blank" class="rss-button"><i class="entypo rss"></i> '.$locale['rss_downloads'].'</a>';
echo '<a href="'.INFUSIONS.'rss_feeds_panel/feeds/rss_forums.php" target="_blank" class="rss-button"><i class="entypo rss"></i> '.$locale['rss_forums'].'</a>';
echo '<a href="'.INFUSIONS.'rss_feeds_panel/feeds/rss_news.php" target="_blank" class="rss-button"><i class="entypo rss"></i> '.$locale['rss_news'].'</a>';
echo '<a href="'.INFUSIONS.'rss_feeds_panel/feeds/rss_weblinks.php" target="_blank" class="rss-button"><i class="entypo rss"></i> '.$locale['rss_weblinks'].'</a>';
closeside();
