<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: errors.php
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
require_once __DIR__.'/../maincore.php';
if (!checkrights("ERRO") || !defined("iAUTH") || !isset($_GET['aid']) || $_GET['aid'] != iAUTH) {redirect("../index.php");}

require_once THEMES."templates/admin_header.php";
include LOCALE.LOCALESET."admin/errors.php";

add_to_head("<link rel='stylesheet' href='".THEMES."templates/errors.css' type='text/css' media='all' />");

// Setting maximum number of folders for an URL
function getMaxFolders($url, $level = 2) {
    $return = "";
    $tmpUrlArr = explode("/", $url);
    if (count($tmpUrlArr) > $level) {
        $tmpUrlArr = array_reverse($tmpUrlArr);
        for ($i = 0; $i < $level; $i++) {
            $return = $tmpUrlArr[$i].($i > 0 ? "/".$return : "");
        }
    } else {
        $return = implode("/", $tmpUrlArr);
    }

    return $return;
}

// Wrap code
function codeWrap($code, $maxLength = 150) {
    $lines = explode("\n", $code);
    $count = count($lines);
    for ($i = 0; $i < $count; ++$i) {
        preg_match('`^\s*`', $code, $matches);
        $lines[$i] = wordwrap($lines[$i], $maxLength, "\n$matches[0]\t", TRUE);
    }

    return implode("\n", $lines);
}

// Print code
function printCode($source_code, $starting_line, $error_line = "", array $error_message = [], $title = NULL) {
    $locale = fusion_get_locale();

    if (is_array($source_code)) {
        return FALSE;
    }

    $error_message = [
        'time' => !empty($error_message['time']) ? $error_message['time'] : time(),
        'text' => !empty($error_message['text']) ? $error_message['text'] : $locale['na'],];
    $source_code = explode("\n", str_replace(["\r\n", "\r"], "\n", $source_code));
    $line_count = $starting_line;
    $formatted_code = "";
    $error_message = "<div class='panel panel-default m-10'><div class='panel-heading'>Line ".$error_line." -- ".timer($error_message['time'])."</div><div class='panel-body strong required'>".$error_message['text']."</div>\n";
    foreach ($source_code as $code_line) {
        $code_line = codeWrap($code_line, 145);
        $line_class = ($line_count == $error_line ? "err_tbl-error-line" : "err_tbl1");
        $formatted_code .= "<tr>\n<td class='err_tbl2' style='text-align:right;width:1%;'>".$line_count."</td>\n";
        if (preg_match('#<\?(php)?[^[:graph:]]#', $code_line)) {
            $formatted_code .= "<td class='".$line_class."'>".str_replace(['<code>', '</code>'], '', highlight_string($code_line, TRUE))."</td>\n</tr>\n";
        } else {
            $formatted_code .= "<td class='".$line_class."'>".preg_replace('#(&lt;\?php&nbsp;)+#', '', str_replace(['<code>', '</code>'], '', highlight_string('<?php '.$code_line, TRUE)))."
            </td>\n</tr>\n";
            if ($line_count == $error_line) {
                $formatted_code .= "<tr>\n<td colspan='2'>".$error_message."</td></tr>\n";
            }
        }
        $line_count++;
    }

    $title = !empty($title) ? '<thead><tr><th colspan="2" class="p-10">'.$title.'</th></tr></thead>' : '';

    return "<table class='table-bordered err_tbl-border center' cellspacing='0' cellpadding='0'>".$title."<tbody>".$formatted_code."</tbody></table>";
}

if (isset($_POST['error_status']) && isnum($_POST['error_status']) && isset($_POST['error_id']) && isnum($_POST['error_id'])) {
    $result = dbquery(
        "UPDATE ".DB_ERRORS." SET error_status='".$_POST['error_status']."'
        WHERE error_id='".$_POST['error_id']."'"
    );
}

if (isset($_POST['delete_entries']) && isset($_POST['delete_status']) && isnum($_POST['delete_status'])) {
    $result = dbquery("DELETE FROM ".DB_ERRORS." WHERE error_status='".$_POST['delete_status']."'");
}

opentable($locale['400']);
$rows = dbcount("(error_id)", DB_ERRORS);
if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) {
    $_GET['rowstart'] = 0;
}
if ($rows != 0) {
    $i = 0;
    $result = dbquery(
        "SELECT * FROM ".DB_ERRORS." ORDER BY error_timestamp DESC
        LIMIT ".$_GET['rowstart'].",20"
    );
    echo "<a name='top'></a>\n<table cellpadding='0' cellspacing='1' class='tbl-border center table-striped'>\n";
    echo '<thead>';
    echo "<tr>\n";
    echo "<th class='tbl1' colspan='4' style='text-align:center;'>";
    echo "<form name='delete_form' action='".FUSION_SELF.$aidlink."' method='post'>";
    echo "".$locale['440']." <select name='delete_status' class='textbox va'>";
    echo "<option>---</option>\n";
    echo "<option value='0'>".$locale['450']."</option>\n";
    echo "<option value='1'>".$locale['451']."</option>\n";
    echo "<option value='2'>".$locale['452']."</option>\n";
    echo "</select>\n<input type='submit' class='button' name='delete_entries' value='".$locale['453']."' style='margin-left:5px;' />";
    echo "</form>\n";
    echo "</th>\n";
    echo "</tr>\n<tr>\n";
    echo "<th class='tbl2' style='font-weight:bold;'>".$locale['410']."</th>\n";
    echo "<th class='tbl2' style='font-weight:bold;width:5%;'>".$locale['413']."</th>\n";
    echo "<th class='tbl2' style='text-align:center;width:5%;font-weight:bold;'>".$locale['414']."</th>\n";
    echo "</tr>\n";
    echo '</thead>';
    echo '<tbody>';
    while ($data = dbarray($result)) {
        $row_color = ($i % 2 == 0 ? "tbl1" : "tbl2");
        echo "<tr>\n";
        echo "<td class='p-5 ".$row_color."'>\n";
        echo "<a href='".FUSION_SELF.$aidlink."&amp;rowstart=".$_GET['rowstart']."&amp;error_id=".$data['error_id']."#file' title='".$data['error_file']."'>";
        echo getMaxFolders($data['error_file'], 2)."</a><br />\n";
        echo "<span class='small2'>".htmlspecialchars_decode(descript($data['error_message']))." ".$locale['415']." ".$data['error_line']."</span>";
        echo "</td>\n";
        echo "<td class='p-5 ".$row_color."' style='white-space:nowrap;'>".showdate("longdate", $data['error_timestamp'])."</td>\n";
        echo "<td class='p-5 ".$row_color."' style='white-space:nowrap;'>\n";
        echo "<form action='".FUSION_SELF.$aidlink."&amp;rowstart=".$_GET['rowstart']."' method='post'>";
        echo "<input type='hidden' name='error_id' value='".$data['error_id']."' />";
        echo "<select name='error_status' class='textbox' onchange='this.form.submit();'>";
        echo "<option value='0'".($data['error_status'] == 0 ? " selected='selected'" : "").">".$locale['450']."</option>\n";
        echo "<option value='1'".($data['error_status'] == 1 ? " selected='selected'" : "").">".$locale['451']."</option>\n";
        echo "<option value='2'".($data['error_status'] == 2 ? " selected='selected'" : "").">".$locale['452']."</option>\n";
        echo "</select>\n<input type='submit' class='button change_status' value='".$locale['453']."' style='margin-left:5px;' /></form>\n";
        echo "</td>\n";
        echo "</tr>\n";
        $i++;
    }
    echo '</tbody>';
    echo "</table>\n";
} else {
    echo "<div style='text-align:center'><br />\n".$locale['418']."<br /><br />\n</div>\n";
}
if ($rows > 20) {
    echo "<div style='margin-top:5px;text-align:center;'>\n".makepagenav($_GET['rowstart'], 20, $rows, 3, FUSION_SELF.$aidlink."&amp;")."\n</div>\n";
}
closetable();

if (isset($_GET['error_id']) && isnum($_GET['error_id'])) {
    $result = dbquery("SELECT * FROM ".DB_ERRORS." WHERE error_id='".$_GET['error_id']."' LIMIT 1");
    if (dbrows($result) == 0) {
        redirect(FUSION_SELF.$aidlink);
    }

    $data = dbarray($result);
    $error_file = html_entity_decode($data['error_file'], ENT_QUOTES, fusion_get_locale('charset'));
    $thisFileContent = is_file($error_file) ? file($error_file) : [];
    $line_start = max($data['error_line'] - 10, 1);
    $line_end = min($data['error_line'] + 10, count($thisFileContent));
    $output = implode("", array_slice($thisFileContent, $line_start - 1, $line_end - $line_start + 1));
    $pageFilePath = BASEDIR.$data['error_page'];
    $pageContent = is_file($pageFilePath) ? file_get_contents($pageFilePath) : '';

    opentable($locale['401']." ".getMaxFolders($error_file, 3));
    echo "<table cellpadding='0' cellspacing='1' class='tbl-border center' style='border-collapse:collapse;'>\n";
    echo "<tr>\n";
    echo "<td colspan='4' class='tbl2'><a name='file'></a>\n<strong>".$locale['420']."</strong></td>\n";
    echo "</tr>\n<tr>\n";
    echo "<td class='tbl2 err_tbl-error' style='width:5%;white-space:nowrap;'>".$locale['410'].":</td>\n";
    echo "<td class='tbl1 err_tbl-error'>".$data['error_message']."</td>\n";
    echo "<td class='tbl2 err_tbl-error' style='width:5%;white-space:nowrap;'>".$locale['415']."</td>\n";
    echo "<td class='tbl1 err_tbl-error'>".$data['error_line']."</td>\n";
    echo "</tr>\n<tr>\n";
    echo "<td class='tbl2' style='width:5%;white-space:nowrap;'>".$locale['419'].":</td>\n";
    echo "<td class='tbl1'><strong>".getMaxFolders($error_file, 3)."</strong></td>\n";
    echo "<td class='tbl2' style='width:5%;white-space:nowrap;'>".$locale['411'].":</td>\n";
    echo "<td class='tbl1'>";
    echo "<a href='".FUSION_SELF.$aidlink."&amp;rowstart=".$_GET['rowstart']."&amp;error_id=".$data['error_id']."#page' title='".$data['error_page']."'>";
    echo getMaxFolders($data['error_page'], 3)."</a></td>\n";
    echo "</tr>\n<tr>\n";
    echo "<td class='tbl2' style='width:5%;white-space:nowrap;'>".$locale['412']."-".$locale['416']."</td>\n";
    echo "<td class='tbl1'>".$data['error_user_level']."</td>\n";
    echo "<td class='tbl2' style='width:5%;white-space:nowrap;'>".$locale['417']."</td>\n";
    echo "<td class='tbl1'>".$data['error_user_ip']."</td>\n";
    echo "</tr>\n<tr>\n";
    echo "<td class='tbl2' style='width:5%;white-space:nowrap;'>".$locale['413'].":</td>\n";
    echo "<td class='tbl1'>".showdate("longdate", $data['error_timestamp'])."</td>\n";
    echo "<td class='tbl2' style='width:5%;white-space:nowrap;'>".$locale['414'].":</td>\n";
    echo "<td class='tbl1'>";
    echo "<form action='".FUSION_SELF.$aidlink."&amp;rowstart=".$_GET['rowstart']."&amp;error_id=".$data['error_id']."#file' method='post'>";
    echo "<input type='hidden' name='error_id' value='".$data['error_id']."' />";
    echo "<select name='error_status' class='textbox' onchange='this.form.submit();'>";
    echo "<option value='0'".($data['error_status'] == 0 ? " selected='selected'" : "").">".$locale['450']."</option>\n";
    echo "<option value='1'".($data['error_status'] == 1 ? " selected='selected'" : "").">".$locale['451']."</option>\n";
    echo "<option value='2'".($data['error_status'] == 2 ? " selected='selected'" : "").">".$locale['452']."</option>\n";
    echo "</select>\n<input type='submit' class='button change_status' value='".$locale['453']."' style='margin-left:5px;' /></form>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

    echo "<div style='text-align:center;font-weight:bold;'><a href='#top' title='".$locale['422']."'>".$locale['422']."</a></div>\n";
    ?><div class='m-t-10'>
        <div class="table-responsive">
            <?php echo printCode($output, $line_start, $data['error_line'], [
                'time' => $data['error_timestamp'],
                'text' => $data['error_message']
            ], '<strong>'.$locale['421'].'</strong> ('.$locale['415'].' '.$line_start.' - '.$line_end.')'); ?>
        </div>
    </div><?php

    echo "<hr />\n<div style='text-align:center;font-weight:bold;'><a href='#top' title='".$locale['422']."'>".$locale['422']."</a></div>\n";

    ?><div class='m-t-10'>
        <div class="table-responsive">
            <?php echo printCode($pageContent, 1, NULL, [], "<strong>".$locale['411'].": ".getMaxFolders($data['error_page'], 2)."</strong>"); ?>
        </div>
    </div><?php

    echo "<div style='text-align:center;font-weight:bold;'><a href='#top' title='".$locale['422']."'>".$locale['422']."</a></div>\n";
    closetable();
}

// Show the "Apply"-button only when javascript is disabled"
echo "<script language='JavaScript' type='text/javascript'>\n";
echo "$(document).ready(function() {
    $('.change_status').hide();

    $('#top').click(function(){
        $('html, body').animate({scrollTop:0}, 'slow');
        return false;
    });
});";
echo "</script>\n";

require_once THEMES."templates/footer.php";
