<?php
/*-------------------------------------------------------+
| PHPFusion Content Management System
| Copyright (C) PHP Fusion Inc
| https://www.phpfusion.com/
+--------------------------------------------------------+
| Filename: profile.tpl.php
| Author: Frederick MC Chan (Chan)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
defined('IN_FUSION') || exit;

if (!function_exists('display_register_form')) {
    /**
     * Registration Form Template
     * The tags {%xyz%} are default replacement that the core will perform
     * echo output design in compatible with Version 7.xx theme set.
     *
     * @param $info - the array output that is accessible for your custom requirements
     */
    function display_register_form(array $info = []) {
        $opentab = "";
        $closetab = "";
        if (!empty($info["tab_info"])) {
            $opentab = opentab($info["tab_info"], $_GET["section"], "user-profile-form", TRUE);
            $closetab = closetab();
        }
        echo "<!--HTML-->";
        opentable();
        echo $opentab;
        echo "<!--register_pre_idx--><div class='spacer-sm'><div id='register_form' class='row'><div class='col-xs-12 col-sm-12'>";
        echo $info["openform"].
            $info["user_id"].
            $info["user_name"].
            $info["user_email"].
            $info["user_hide_email"].
            $info["user_avatar"].
            $info["user_password"].
            $info["user_admin_password"].
            $info["user_custom"].
            $info["validate"].
            $info["terms"].
            $info["button"];
        echo "</div></div></div><!--register_sub_idx-->";
        echo $closetab;
        closetable();
        echo "<!--//HTML-->";
    }
}

if (!function_exists('display_profile_form')) {
    /**
     * Edit Profile Form Template
     * The tags {%xyz%} are default replacement that the core will perform
     * echo output design in compatible with Version 7.xx theme set.
     *
     * @param $info - the array output that is accessible for your custom requirements
     */
    function display_profile_form(array $info = []) {
        $opentab = "";
        $closetab = "";
        if (!empty($info["tab_info"])) {
            $opentab = opentab($info["tab_info"], $_GET["section"], "user-profile-form", TRUE);
            $closetab = closetab();
        }
        echo "<!--HTML-->";
        opentable('');
        echo $opentab;
        echo "<!--editprofile_pre_idx--><div class='spacer-sm'><div id='profile_form' class='row'><div class='col-xs-12 col-sm-12'>";
        echo $info["openform"].
            $info["user_id"].
            $info["user_name"].
            $info["user_email"].
            $info["user_hide_email"].
            $info["user_reputation"].
            $info["user_avatar"].
            $info["user_password"].
            $info["user_admin_password"].
            $info["user_custom"].
            $info["validate"].
            $info["terms"].
            $info["button"];
        echo " </div ></div ></div ><!--editprofile_sub_idx-->";
        echo $closetab;
        closetable();
        echo "<!--//HTML-->";
    }
}

/**
 * Profile display view
 *
 * @param $info (array) - prepared responsive fields
 * To get information of the current raw userData
 * global $userFields; // profile object at profile.php
 * $current_user_info = $userFields->getUserData(); // returns array();
 * print_p($current_user_info); // debug print
 */
if (!function_exists('display_user_profile')) {
    function display_user_profile($info) {
        $locale = fusion_get_locale();

        add_to_css('.social-icons>img,.cat-field img{max-width:25px;}');

        opentable('');
        echo '<section id="user-profile">';
        echo '<div class="row m-b-20">';
        echo '<div class="col-xs-12 col-sm-2">';
            $avatar['user_id'] = $info['user_id'];
            $avatar['user_name'] = $info['user_name'];
            $avatar['user_avatar'] = $info['core_field']['profile_user_avatar']['value'];
            $avatar['user_status'] = $info['core_field']['profile_user_avatar']['status'];
            echo display_avatar($avatar, '130px', 'profile-avatar', FALSE, 'img-responsive');

            if (!empty($info['buttons'])) {
                echo '<a class="btn btn-success btn-block spacer-sm" href="'.$info['buttons']['user_pm_link'].'">'.$locale['send_message'].'</a>';
            }
        echo '</div>';

        echo '<div class="col-xs-12 col-sm-10">';
            if (!empty($info['user_admin'])) {
                $button = $info['user_admin'];
                echo '<div class="pull-right btn-group">
                    <a class="btn btn-sm btn-default" href="'.$button['user_susp_link'].'">'.$button['user_susp_title'].'</a>
                    <a class="btn btn-sm btn-default" href="'.$button['user_edit_link'].'">'.$button['user_edit_title'].'</a>
                    <a class="btn btn-sm btn-default" href="'.$button['user_ban_link'].'">'.$button['user_ban_title'].'</a>
                    <a class="btn btn-sm btn-default" href="'.$button['user_suspend_link'].'">'.$button['user_suspend_title'].'</a>
                    <a class="btn btn-sm btn-danger" href="'.$button['user_delete_link'].'">'.$button['user_delete_title'].'</a>
                </div>';
            }

            echo '<h2 class="m-0">'.$info['core_field']['profile_user_name']['value'].'</h2>';
            echo $info['core_field']['profile_user_level']['value'];

            if (!empty($info['user_field'])) {
                echo '<div class="m-t-5">';
                foreach ($info['user_field'] as $cat_id => $category_data) {
                    if (!empty($category_data['fields'])) {
                        foreach ($category_data['fields'] as $field_id => $field_data) {
                            if (!empty($field_data['type']) && $field_data['type'] == 'social') {
                                echo '<a class="social-icons" href="'.$field_data['link'].'">'.$field_data['icon'].'</a>';
                            }
                        }
                    }
                }
                echo '</div>';
            }

            if (!empty($info['core_field'])) {
                echo '<hr>';
                foreach ($info['core_field'] as $field_id => $field_data) {
                    switch ($field_id) {
                        case 'profile_user_group':
                            if (!empty($field_data['value']) && is_array($field_data['value'])) {
                                foreach ($field_data['value'] as $groups) {
                                    $user_groups[] = $groups;
                                }
                            }
                            break;
                        case 'profile_user_avatar':
                            $avatar['user_avatar'] = $field_data['value'];
                            $avatar['user_status'] = $field_data['status'];
                            break;
                        case 'profile_user_name':
                            $user_level['user_name'] = $field_data['value'];
                            break;
                        case 'profile_user_level':
                            $user_level['user_level'] = $field_data['value'];
                            break;
                        default:
                            if (!empty($field_data['value'])) {
                                echo '<div id="'.$field_id.'" class="row cat-field">';
                                    echo '<div class="col-xs-12 col-sm-3"><strong>'.$field_data['title'].'</strong></div>';
                                    echo '<div class="col-xs-12 col-sm-9">'.$field_data['value'].'</div>';
                                echo '</div>';
                            }
                    }
                }
            }

        echo '</div>';
        echo '</div>'; // .row

        if (!empty($info['section'])) {
            $tab_title = [];
            foreach ($info['section'] as $page_section) {
                $tab_title['title'][$page_section['id']] = $page_section['name'];
                $tab_title['id'][$page_section['id']] = $page_section['id'];
                $tab_title['icon'][$page_section['id']] = $page_section['icon'];
            }

            $tab_active = tab_active($tab_title, $_GET['section']);

            echo '<div class="profile-section">';
                echo opentab($tab_title, $_GET['section'], 'profile_tab', TRUE, 'nav-tabs m-b-20', 'section', ['section']);
                    echo opentabbody($tab_title['title'][$_GET['section']], $tab_title['id'][$_GET['section']], $tab_active, TRUE);

                    if ($tab_title['id'][$_GET['section']] == $tab_title['id'][1]) {
                        echo '<div class="row cat-field">';
                            echo '<div class="col-xs-12 col-sm-3"><strong>'.$locale['u057'].'</strong></div>';
                            echo '<div class="col-xs-12 col-sm-9">';
                                if (!empty($user_groups) && is_array($user_groups)) {
                                    $i = 0;
                                    foreach ($user_groups as $id => $group) {
                                        echo $i > 0 ? ', ' : '';
                                        echo '<a href="'.$group['group_url'].'">'.$group['group_name'].'</a>';
                                        $i++;
                                    }
                                } else {
                                    echo !empty($locale['u117']) ? $locale['u117'] : $locale['na'];
                                }
                            echo '</div>';
                        echo '</div>';

                        if (!empty($info['group_admin'])) {
                            $group = $info['group_admin'];

                            echo '<div class="m-t-10">';
                                echo $group['ug_openform'];
                                echo '<div>'.$group['ug_title'].'</div>';
                                echo '<div class="spacer-xs">'.$group['ug_dropdown_input'].'</div>';
                                echo '<div>'.$group['ug_button'].'</div>';
                                echo $group['ug_closeform'];
                            echo '</div>';
                        }
                    }

                    if (!empty($info['user_field'])) {
                            foreach ($info['user_field'] as $cat_id => $category_data) {
                                if (!empty($category_data['fields'])) {
                                    if (isset($category_data['fields'])) {
                                        foreach ($category_data['fields'] as $field_id => $field_data) {
                                            if (isset($field_data['type']) && $field_data['type'] == 'social') {
                                                // Hide Social UF
                                            } else {
                                                $fields[] = $field_data;
                                            }
                                        }
                                    }

                                    if (!empty($fields)) {
                                        echo '<h4 class="cat-title text-uppercase">'.$category_data['title'].'</h4>';

                                        if (isset($category_data['fields'])) {
                                            foreach ($category_data['fields'] as $field_id => $field_data) {
                                                if (isset($field_data['type']) && $field_data['type'] == 'social') {
                                                    // Hide Social UF
                                                } else {
                                                    echo '<div id="field-'.$field_id.'" class="row cat-field">';
                                                        echo '<div class="col-xs-12 col-sm-3"><strong>'.(!empty($field_data['icon']) ? $field_data['icon'] : '').' '.$field_data['title'].'</strong></div>';
                                                        echo '<div class="col-xs-12 col-sm-9">'.$field_data['value'].'</div>';
                                                    echo '</div>';
                                                }
                                            }
                                        }

                                        echo '<hr>';
                                    }
                                }
                            }
                    } else {
                        echo '<div class="text-center well">'.$locale['uf_108'].'</div>';
                    }

                    echo closetabbody();
                echo closetab();
            echo '</div>';
        }

        echo '</section>';
        closetable();
    }
}
