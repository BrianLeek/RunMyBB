<?php
/**
 * MyBB 1.8
 * Copyright 2014 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybb.com
 * License: http://www.mybb.com/about/license
 *
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->add_breadcrumb_item($lang->board_settings, "index.php?module=config-settings");

$plugins->run_hooks("admin_config_settings_begin");

// Creating a new setting group
if($mybb->input['action'] == "addgroup")
{
	$plugins->run_hooks("admin_config_settings_addgroup");

	if($mybb->request_method == "post")
	{
		// Validate title
		if(!trim($mybb->input['title']))
		{
			$errors[] = $lang->error_missing_group_title;
		}

		// Validate identifier
		if(!trim($mybb->input['name']))
		{
			$errors[] = $lang->error_missing_group_name;
		}
		$query = $db->simple_select("settinggroups", "title", "name='".$db->escape_string($mybb->input['name'])."'");
		if($db->num_rows($query) > 0)
		{
			$dup_group_title = $db->fetch_field($query, 'title');
			$errors[] = $lang->sprintf($lang->error_duplicate_group_name, $dup_group_title);
		}

		if(!$errors)
		{
			$new_setting_group = array(
				"name" => $db->escape_string($mybb->input['name']),
				"title" => $db->escape_string($mybb->input['title']),
				"description" => $db->escape_string($mybb->input['description']),
				"disporder" => $mybb->get_input('disporder', MyBB::INPUT_INT),
				"isdefault" => 0
			);
			$gid = $db->insert_query("settinggroups", $new_setting_group);

			$plugins->run_hooks("admin_config_settings_addgroup_commit");

			// Log admin action
			log_admin_action($gid, $mybb->input['name']);

			flash_message($lang->success_setting_group_added, 'success');
			admin_redirect("index.php?module=config-settings&action=manage");
		}
	}

	$page->add_breadcrumb_item($lang->add_new_setting_group);
	$page->output_header($lang->board_settings." - ".$lang->add_new_setting_group);

	$sub_tabs['change_settings'] = array(
		'title' => $lang->change_settings,
		'link' => "index.php?module=config-settings"
	);

	$sub_tabs['add_setting'] = array(
		'title' => $lang->add_new_setting,
		'link' => "index.php?module=config-settings&amp;action=add"
	);

	$sub_tabs['add_setting_group'] = array(
		'title' => $lang->add_new_setting_group,
		'link' => "index.php?module=config-settings&amp;action=addgroup",
		'description' => $lang->add_new_setting_group_desc
	);

	$sub_tabs['modify_setting'] = array(
		'title' => $lang->modify_existing_settings,
		'link' => "index.php?module=config-settings&amp;action=manage"
	);

	$page->output_nav_tabs($sub_tabs, 'add_setting_group');

	$form = new Form("index.php?module=config-settings&amp;action=addgroup", "post", "add");

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$form_container = new FormContainer($lang->add_new_setting_group);
	$form_container->output_row($lang->title." <em>*</em>", "", $form->generate_text_box('title', $mybb->get_input('title'), array('id' => 'title')), 'title');
	$form_container->output_row($lang->description, "", $form->generate_text_area('description', $mybb->get_input('description'), array('id' => 'description')), 'description');
	$form_container->output_row($lang->display_order, "", $form->generate_numeric_field('disporder', $mybb->get_input('disporder'), array('id' => 'disporder', 'min' => 0)), 'disporder');
	$form_container->output_row($lang->name." <em>*</em>", $lang->group_name_desc, $form->generate_text_box('name', $mybb->get_input('name'), array('id' => 'name')), 'name');
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->insert_new_setting_group);
	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

// Edit setting group
if($mybb->input['action'] == "editgroup")
{
	$query = $db->simple_select("settinggroups", "*", "gid='".$mybb->get_input('gid', MyBB::INPUT_INT)."'");
	$group = $db->fetch_array($query);

	// Does the setting not exist?
	if(!$group)
	{
		flash_message($lang->error_invalid_gid2, 'error');
		admin_redirect("index.php?module=config-settings&action=manage");
	}
	// Prevent editing of default
	if($group['isdefault'] == 1)
	{
		flash_message($lang->error_cannot_edit_default, 'error');
		admin_redirect("index.php?module=config-settings&action=manage");
	}

	$plugins->run_hooks("admin_config_settings_editgroup");

	// Do edit?
	if($mybb->request_method == "post")
	{
		// Validate title
		if(!trim($mybb->input['title']))
		{
			$errors[] = $lang->error_missing_group_title;
		}

		// Validate identifier
		if(!trim($mybb->input['name']))
		{
			$errors[] = $lang->error_missing_group_name;
		}
		$query = $db->simple_select("settinggroups", "title", "name='".$db->escape_string($mybb->input['name'])."' AND gid != '{$group['gid']}'");
		if($db->num_rows($query) > 0)
		{
			$dup_group_title = $db->fetch_field($query, 'title');
			$errors[] = $lang->sprintf($lang->error_duplicate_group_name, $dup_group_title);
		}

		if(!$errors)
		{
			$update_setting_group = array(
				"name" => $db->escape_string($mybb->input['name']),
				"title" => $db->escape_string($mybb->input['title']),
				"description" => $db->escape_string($mybb->input['description']),
				"disporder" => $mybb->get_input('disporder', MyBB::INPUT_INT),
			);

			$plugins->run_hooks("admin_config_settings_editgroup_commit");

			$db->update_query("settinggroups", $update_setting_group, "gid='{$group['gid']}'");

			// Log admin action
			log_admin_action($group['gid'], $mybb->input['name']);

			flash_message($lang->success_setting_group_updated, 'success');
			admin_redirect("index.php?module=config-settings&action=manage");
		}
	}

	$page->add_breadcrumb_item($lang->edit_setting_group);
	$page->output_header($lang->board_settings." - ".$lang->edit_setting_group);

	$sub_tabs['edit_setting_group'] = array(
		'title' => $lang->edit_setting_group,
		'link' => "index.php?module=config-settings&amp;action=editgroup&amp;gid={$group['gid']}",
		'description' => $lang->edit_setting_group_desc
	);

	$page->output_nav_tabs($sub_tabs, 'edit_setting_group');

	$form = new Form("index.php?module=config-settings&amp;action=editgroup", "post", "editgroup");

	echo $form->generate_hidden_field("gid", $group['gid']);

	if($errors)
	{
		$group_data = $mybb->input;
		$page->output_inline_error($errors);
	}
	else
	{
		$group_data = $group;
	}

	$form_container = new FormContainer($lang->edit_setting_group);
	$form_container->output_row($lang->title." <em>*</em>", "", $form->generate_text_box('title', $group_data['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->description, "", $form->generate_text_area('description', $group_data['description'], array('id' => 'description')), 'description');
	$form_container->output_row($lang->display_order, "", $form->generate_numeric_field('disporder', $group_data['disporder'], array('id' => 'disporder', 'min' => 0)), 'disporder');
	$form_container->output_row($lang->name." <em>*</em>", $lang->group_name_desc, $form->generate_text_box('name', $group_data['name'], array('id' => 'name')), 'name');
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->update_setting_group);
	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

// Delete Setting Group
if($mybb->input['action'] == "deletegroup")
{
	$query = $db->simple_select("settinggroups", "*", "gid='".$mybb->get_input('gid', MyBB::INPUT_INT)."'");
	$group = $db->fetch_array($query);

	// Does the setting group not exist?
	if(!$group)
	{
		flash_message($lang->error_invalid_gid2, 'error');
		admin_redirect("index.php?module=config-settings&action=manage");
	}
	// Prevent deletion of default
	if($group['isdefault'] == 1)
	{
		flash_message($lang->error_cannot_edit_default, 'error');
		admin_redirect("index.php?module=config-settings&action=manage");
	}

	// User clicked no
	if($mybb->get_input('no'))
	{
		admin_redirect("index.php?module=config-settings&action=manage");
	}

	$plugins->run_hooks("admin_config_settings_deletegroup");

	if($mybb->request_method == "post")
	{
		// Delete the setting group and its settings
		$db->delete_query("settinggroups", "gid='{$group['gid']}'");
		$db->delete_query("settings", "gid='{$group['gid']}'");

		rebuild_settings();

		$plugins->run_hooks("admin_config_settings_deletegroup_commit");

		// Log admin action
		log_admin_action($group['gid'], $group['name']);

		flash_message($lang->success_setting_group_deleted, 'success');
		admin_redirect("index.php?module=config-settings&action=manage");
	}
	else
	{
		$page->output_confirm_action("index.php?module=config-settings&amp;action=deletegroup&amp;gid={$group['gid']}", $lang->confirm_setting_group_deletion);
	}
}

// Creating a new setting
if($mybb->input['action'] == "add")
{
	$plugins->run_hooks("admin_config_settings_add");

	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['title']))
		{
			$errors[] = $lang->error_missing_title;
		}

		$query = $db->simple_select("settinggroups", "gid", "gid='".$mybb->get_input('gid', MyBB::INPUT_INT)."'");
		$gid = $db->fetch_field($query, 'gid');
		if(!$gid)
		{
			$errors[] = $lang->error_invalid_gid;
		}

		if(!trim($mybb->input['name']))
		{
			$errors[] = $lang->error_missing_name;
		}
		$query = $db->simple_select("settings", "title", "name='".$db->escape_string($mybb->input['name'])."'");
		if($db->num_rows($query) > 0)
		{
			$dup_setting_title = $db->fetch_field($query, 'title');
			$errors[] = $lang->sprintf($lang->error_duplicate_name, $dup_setting_title);
		}

		// do some type filtering
		$mybb->input['type'] = $mybb->get_input('type');
		if(!ctype_alnum($mybb->input['type']) || strtolower($mybb->input['type']) == "php")
		{
			$mybb->input['type'] = "";
		}

		if(!$mybb->input['type'])
		{
			$errors[] = $lang->error_invalid_type;
		}

		if(!$errors)
		{
			if($mybb->input['extra'])
			{
				$options_code = "{$mybb->input['type']}\n{$mybb->input['extra']}";
			}
			else
			{
				$options_code = $mybb->input['type'];
			}

			$mybb->input['name'] = str_replace("\\", '', $mybb->input['name']);
			$mybb->input['name'] = str_replace('$', '', $mybb->input['name']);
			$mybb->input['name'] = str_replace("'", '', $mybb->input['name']);

			if($options_code == "numeric")
			{
				$value = $mybb->get_input('value', MyBB::INPUT_INT);
			}
			else
			{
				$value = $db->escape_string($mybb->input['value']);
			}

			$new_setting = array(
				"name" => $db->escape_string($mybb->input['name']),
				"title" => $db->escape_string($mybb->input['title']),
				"description" => $db->escape_string($mybb->input['description']),
				"optionscode" => $db->escape_string($options_code),
				"value" => $value,
				"disporder" => $mybb->get_input('disporder', MyBB::INPUT_INT),
				"gid" => $mybb->get_input('gid', MyBB::INPUT_INT)
			);

			$sid = $db->insert_query("settings", $new_setting);
			rebuild_settings();

			$plugins->run_hooks("admin_config_settings_add_commit");

			// Log admin action
			log_admin_action($sid, $mybb->input['title']);

			flash_message($lang->success_setting_added, 'success');
			admin_redirect("index.php?module=config-settings&action=manage");
		}
	}

	$page->add_breadcrumb_item($lang->add_new_setting);
	$page->output_header($lang->board_settings." - ".$lang->add_new_setting);

	$sub_tabs['change_settings'] = array(
		'title' => $lang->change_settings,
		'link' => "index.php?module=config-settings"
	);

	$sub_tabs['add_setting'] = array(
		'title' => $lang->add_new_setting,
		'link' => "index.php?module=config-settings&amp;action=add",
		'description' => $lang->add_new_setting_desc
	);

	$sub_tabs['add_setting_group'] = array(
		'title' => $lang->add_new_setting_group,
		'link' => "index.php?module=config-settings&amp;action=addgroup"
	);

	$sub_tabs['modify_setting'] = array(
		'title' => $lang->modify_existing_settings,
		'link' => "index.php?module=config-settings&amp;action=manage"
	);

	$page->output_nav_tabs($sub_tabs, 'add_setting');

	$form = new Form("index.php?module=config-settings&amp;action=add", "post", "add");

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$form_container = new FormContainer($lang->add_new_setting);
	$form_container->output_row($lang->title." <em>*</em>", "", $form->generate_text_box('title', $mybb->get_input('title'), array('id' => 'title')), 'title');
	$form_container->output_row($lang->description, "", $form->generate_text_area('description', $mybb->get_input('description'), array('id' => 'description')), 'description');

	$query = $db->simple_select("settinggroups", "*", "", array('order_by' => 'disporder'));
	while($group = $db->fetch_array($query))
	{
		$group_lang_var = "setting_group_{$group['name']}";
		if(!empty($lang->$group_lang_var))
		{
			$options[$group['gid']] = htmlspecialchars_uni($lang->$group_lang_var);
		}
		else
		{
			$options[$group['gid']] = htmlspecialchars_uni($group['title']);
		}
	}
	$form_container->output_row($lang->group." <em>*</em>", "", $form->generate_select_box("gid", $options, $mybb->get_input('gid'), array('id' => 'gid')), 'gid');
	$form_container->output_row($lang->display_order, "", $form->generate_numeric_field('disporder', $mybb->get_input('disporder'), array('id' => 'disporder', 'min' => 0)), 'disporder');

	$form_container->output_row($lang->name." <em>*</em>", $lang->name_desc, $form->generate_text_box('name', $mybb->get_input('name'), array('id' => 'name')), 'name');

	$setting_types = array(
		"text" => $lang->text,
		"numeric" => $lang->numeric_text,
		"textarea" => $lang->textarea,
		"yesno" => $lang->yesno,
		"onoff" => $lang->onoff,
		"select" => $lang->select,
		"forumselect" => $lang->forum_selection_box,
		"forumselectsingle" => $lang->forum_selection_single,
		"groupselect" => $lang->group_selection_box,
		"groupselectsingle" => $lang->group_selection_single,
		"radio" => $lang->radio,
		"checkbox" => $lang->checkbox,
		"language" => $lang->language_selection_box,
		"adminlanguage" => $lang->adminlanguage,
		"cpstyle" => $lang->cpstyle,
		"prefixselect" => $lang->prefix_selection_box
		//"php" => $lang->php // Internal Use Only
	);

	$form_container->output_row($lang->type." <em>*</em>", "", $form->generate_select_box("type", $setting_types, $mybb->get_input('type'), array('id' => 'type')), 'type');
	$form_container->output_row($lang->extra, $lang->extra_desc, $form->generate_text_area('extra', $mybb->get_input('extra'), array('id' => 'extra')), 'extra', array(), array('id' => 'row_extra'));
	$form_container->output_row($lang->value, "", $form->generate_text_area('value', $mybb->get_input('value'), array('id' => 'value')), 'value');
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->insert_new_setting);
	$form->output_submit_wrapper($buttons);
	$form->end();

	echo '<script type="text/javascript" src="./jscripts/peeker.js?ver=1821"></script>
	<script type="text/javascript">
		$(function() {
			new Peeker($("#type"), $("#row_extra"), /^(select|radio|checkbox|php)$/, false);
		});
		// Add a star to the extra row since the "extra" is required if the box is shown
		add_star("row_extra");
	</script>';

	$page->output_footer();
}

// Editing a particular setting
if($mybb->input['action'] == "edit")
{
	$query = $db->simple_select("settings", "*", "sid='".$mybb->get_input('sid', MyBB::INPUT_INT)."'");
	$setting = $db->fetch_array($query);

	// Does the setting not exist?
	if(!$setting)
	{
		flash_message($lang->error_invalid_sid, 'error');
		admin_redirect("index.php?module=config-settings");
	}

	// Prevent editing of default
	if($setting['isdefault'] == 1)
	{
		flash_message($lang->error_cannot_edit_default, 'error');
		admin_redirect("index.php?module=config-settings&action=manage");
	}

	$plugins->run_hooks("admin_config_settings_edit");

	$type = explode("\n", $setting['optionscode'], 2);
	$type = trim($type[0]);
	if($type == "php")
	{
		flash_message($lang->error_cannot_edit_php, 'error');
		admin_redirect("index.php?module=config-settings&action=manage");
	}

	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['title']))
		{
			$errors[] = $lang->error_missing_title;
		}

		if(!trim($mybb->input['name']))
		{
			$errors[] = $lang->error_missing_name;
		}
		$query = $db->simple_select("settings", "title", "name='".$db->escape_string($mybb->input['name'])."' AND sid != '{$setting['sid']}'");
		if($db->num_rows($query) > 0)
		{
			$dup_setting_title = $db->fetch_field($query, 'title');
			$errors[] = $lang->sprintf($lang->error_duplicate_name, $dup_setting_title);
		}

		// do some type filtering
		$mybb->input['type'] = $mybb->get_input('type');
		if(!ctype_alnum($mybb->input['type']) || strtolower($mybb->input['type']) == "php")
		{
			$mybb->input['type'] = "";
		}

		if(!$mybb->input['type'])
		{
			$errors[] = $lang->error_invalid_type;
		}

		if(!$errors)
		{
			if($mybb->input['extra'])
			{
				$options_code = "{$mybb->input['type']}\n{$mybb->input['extra']}";
			}
			else
			{
				$options_code = $mybb->input['type'];
			}

			$mybb->input['name'] = str_replace("\\", '', $mybb->input['name']);
			$mybb->input['name'] = str_replace('$', '', $mybb->input['name']);
			$mybb->input['name'] = str_replace("'", '', $mybb->input['name']);

			if($options_code == "numeric")
			{
				$value = $mybb->get_input('value', MyBB::INPUT_INT);
			}
			else
			{
				$value = $db->escape_string($mybb->input['value']);
			}

			$updated_setting = array(
				"name" => $db->escape_string($mybb->input['name']),
				"title" => $db->escape_string($mybb->input['title']),
				"description" => $db->escape_string($mybb->input['description']),
				"optionscode" => $db->escape_string($options_code),
				"value" => $value,
				"disporder" => $mybb->get_input('disporder', MyBB::INPUT_INT),
				"gid" => $mybb->get_input('gid', MyBB::INPUT_INT)
			);

			$plugins->run_hooks("admin_config_settings_edit_commit");

			$db->update_query("settings", $updated_setting, "sid='{$setting['sid']}'");
			rebuild_settings();

			// Log admin action
			log_admin_action($setting['sid'], $mybb->input['title']);

			flash_message($lang->success_setting_updated, 'success');
			admin_redirect("index.php?module=config-settings&action=manage");
		}
	}

	$page->add_breadcrumb_item($lang->edit_setting);
	$page->output_header($lang->board_settings." - ".$lang->edit_setting);

	$sub_tabs['change_settings'] = array(
		'title' => $lang->change_settings,
		'link' => "index.php?module=config-settings",
	);

	$sub_tabs['add_setting'] = array(
		'title' => $lang->add_new_setting,
		'link' => "index.php?module=config-settings&amp;action=add"
	);

	$sub_tabs['add_setting_group'] = array(
		'title' => $lang->add_new_setting_group,
		'link' => "index.php?module=config-settings&amp;action=addgroup"
	);

	$sub_tabs['modify_setting'] = array(
		'title' => $lang->modify_existing_settings,
		'link' => "index.php?module=config-settings&amp;action=manage",
		'description' => $lang->modify_existing_settings_desc
	);

	$page->output_nav_tabs($sub_tabs, 'modify_setting');

	$form = new Form("index.php?module=config-settings&amp;action=edit", "post", "edit");

	echo $form->generate_hidden_field("sid", $setting['sid']);

	if($errors)
	{
		$setting_data = $mybb->input;
		$page->output_inline_error($errors);
	}
	else
	{
		$setting_data = $setting;
		$type = explode("\n", $setting['optionscode'], 2);
		$setting_data['type'] = trim($type[0]);

		if(isset($type[1]))
		{
			$setting_data['extra'] = trim($type[1]);
		}
	}

	$form_container = new FormContainer($lang->modify_setting);
	$form_container->output_row($lang->title." <em>*</em>", "", $form->generate_text_box('title', $setting_data['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->description, "", $form->generate_text_area('description', $setting_data['description'], array('id' => 'description')), 'description');

	$query = $db->simple_select("settinggroups", "*", "", array('order_by' => 'disporder'));
	while($group = $db->fetch_array($query))
	{
		$group_lang_var = "setting_group_{$group['name']}";
		if(!empty($lang->$group_lang_var))
		{
			$options[$group['gid']] = htmlspecialchars_uni($lang->$group_lang_var);
		}
		else
		{
			$options[$group['gid']] = htmlspecialchars_uni($group['title']);
		}
	}
	$form_container->output_row($lang->group." <em>*</em>", "", $form->generate_select_box("gid", $options, $setting_data['gid'], array('id' => 'gid')), 'gid');
	$form_container->output_row($lang->display_order, "", $form->generate_numeric_field('disporder', $setting_data['disporder'], array('id' => 'disporder', 'min' => 0)), 'disporder');
	$form_container->end();

	$form_container = new FormContainer($lang->setting_configuration, 1);
	$form_container->output_row($lang->name." <em>*</em>", $lang->name_desc, $form->generate_text_box('name', $setting_data['name'], array('id' => 'name')), 'name');

	$setting_types = array(
		"text" => $lang->text,
		"numeric" => $lang->numeric_text,
		"textarea" => $lang->textarea,
		"yesno" => $lang->yesno,
		"onoff" => $lang->onoff,
		"select" => $lang->select,
		"forumselect" => $lang->forum_selection_box,
		"forumselectsingle" => $lang->forum_selection_single,
		"groupselect" => $lang->group_selection_box,
		"groupselectsingle" => $lang->group_selection_single,
		"radio" => $lang->radio,
		"checkbox" => $lang->checkbox,
		"language" => $lang->language_selection_box,
		"adminlanguage" => $lang->adminlanguage,
		"cpstyle" => $lang->cpstyle,
		"prefixselect" => $lang->prefix_selection_box
		//"php" => $lang->php // Internal Use Only
	);

	$form_container->output_row($lang->type." <em>*</em>", "", $form->generate_select_box("type", $setting_types, $setting_data['type'], array('id' => 'type')), 'type');
	$form_container->output_row($lang->extra, $lang->extra_desc, $form->generate_text_area('extra', !empty($setting_data['extra']) ? $setting_data['extra'] : null, array('id' => 'extra')), 'extra', array(), array('id' => 'row_extra'));
	$form_container->output_row($lang->value, '', $form->generate_text_area('value', $setting_data['value'], array('id' => 'value')), 'value');
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->update_setting);
	$form->output_submit_wrapper($buttons);
	$form->end();

	echo '<script type="text/javascript" src="./jscripts/peeker.js?ver=1821"></script>
	<script type="text/javascript">
		$(function() {
			new Peeker($("#type"), $("#row_extra"), /^(select|radio|checkbox|php)$/, false);
		});
		// Add a star to the extra row since the "extra" is required if the box is shown
		add_star("row_extra");
	</script>';

	$page->output_footer();
}

// Delete Setting
if($mybb->input['action'] == "delete")
{
	$query = $db->simple_select("settings", "*", "sid='".$mybb->get_input('sid', MyBB::INPUT_INT)."'");
	$setting = $db->fetch_array($query);

	// Does the setting not exist?
	if(!$setting)
	{
		flash_message($lang->error_invalid_sid, 'error');
		admin_redirect("index.php?module=config-settings&action=manage");
	}

	// Prevent editing of default
	if($setting['isdefault'] == 1)
	{
		flash_message($lang->error_cannot_edit_default, 'error');
		admin_redirect("index.php?module=config-settings&action=manage");
	}

	// User clicked no
	if($mybb->get_input('no'))
	{
		admin_redirect("index.php?module=config-settings&action=manage");
	}

	$plugins->run_hooks("admin_config_settings_delete");

	if($mybb->request_method == "post")
	{
		// Delete the setting
		$db->delete_query("settings", "sid='{$setting['sid']}'");

		rebuild_settings();

		$plugins->run_hooks("admin_config_settings_delete_commit");

		// Log admin action
		log_admin_action($setting['sid'], $setting['title']);

		flash_message($lang->success_setting_deleted, 'success');
		admin_redirect("index.php?module=config-settings&action=manage");
	}
	else
	{
		$page->output_confirm_action("index.php?module=config-settings&amp;action=delete&amp;sid={$setting['sid']}", $lang->confirm_setting_deletion);
	}
}

// Modify Existing Settings
if($mybb->input['action'] == "manage")
{
	$plugins->run_hooks("admin_config_settings_manage");

	// Update orders
	if($mybb->request_method == "post")
	{
		if(is_array($mybb->input['group_disporder']))
		{
			foreach($mybb->input['group_disporder'] as $gid => $new_order)
			{
				$gid = (int)$gid;
				$update_group = array('disporder' => (int)$new_order);
				$db->update_query("settinggroups", $update_group, "gid={$gid}");
			}
		}

		if(is_array($mybb->input['setting_disporder']))
		{
			foreach($mybb->input['setting_disporder'] as $sid => $new_order)
			{
				$sid = (int)$sid;
				$update_setting = array('disporder' => (int)$new_order);
				$db->update_query("settings", $update_setting, "sid={$sid}");
			}
		}

		$plugins->run_hooks("admin_config_settings_manage_commit");

		// Log admin action
		log_admin_action();

		flash_message($lang->success_display_orders_updated, 'success');
		admin_redirect("index.php?module=config-settings&action=manage");
	}

	$page->add_breadcrumb_item($lang->modify_existing_settings);
	$page->output_header($lang->board_settings." - ".$lang->modify_existing_settings);

	$sub_tabs['change_settings'] = array(
		'title' => $lang->change_settings,
		'link' => "index.php?module=config-settings",
	);

	$sub_tabs['add_setting'] = array(
		'title' => $lang->add_new_setting,
		'link' => "index.php?module=config-settings&amp;action=add"
	);

	$sub_tabs['add_setting_group'] = array(
		'title' => $lang->add_new_setting_group,
		'link' => "index.php?module=config-settings&amp;action=addgroup"
	);

	$sub_tabs['modify_setting'] = array(
		'title' => $lang->modify_existing_settings,
		'link' => "index.php?module=config-settings&amp;action=manage",
		'description' => $lang->modify_existing_settings_desc
	);

	$page->output_nav_tabs($sub_tabs, 'modify_setting');

	// Cache settings
	$settings_cache = array();
	$query = $db->simple_select("settings", "sid, name, title, disporder, gid, isdefault", "", array('order_by' => 'disporder', 'order_dir' => 'asc'));
	while($setting = $db->fetch_array($query))
	{
		$settings_cache[$setting['gid']][] = $setting;
	}

	$form = new Form("index.php?module=config-settings&amp;action=manage", "post", "edit");

	$table = new Table;

	$table->construct_header($lang->setting_group_setting);
	$table->construct_header($lang->order, array('class' => 'align_center', 'style' => 'width: 5%'));
	$table->construct_header($lang->controls, array('class' => 'align_center', 'style' => 'width: 200px'));

	// Generate table
	$query = $db->simple_select("settinggroups", "*", "", array('order_by' => 'disporder', 'order_dir' => 'asc'));
	while($group = $db->fetch_array($query))
	{
		// Make setting group row
		// Translated?
		$group_lang_var = "setting_group_{$group['name']}";
		if(!empty($lang->$group_lang_var))
		{
			$group_title = htmlspecialchars_uni($lang->$group_lang_var);
		}
		else
		{
			$group_title = htmlspecialchars_uni($group['title']);
		}
		$table->construct_cell("<strong>{$group_title}</strong>", array('id' => "group{$group['gid']}"));
		$table->construct_cell($form->generate_numeric_field("group_disporder[{$group['gid']}]", $group['disporder'], array('style' => 'width: 80%; font-weight: bold', 'class' => 'align_center', 'min' => 0)));
		// Only show options if not a default setting group
		if($group['isdefault'] != 1)
		{
			$popup = new PopupMenu("group_{$group['gid']}", $lang->options);
			$popup->add_item($lang->edit_setting_group, "index.php?module=config-settings&amp;action=editgroup&amp;gid={$group['gid']}");
			$popup->add_item($lang->delete_setting_group, "index.php?module=config-settings&amp;action=deletegroup&amp;gid={$group['gid']}&amp;my_post_key={$mybb->post_code}", "return AdminCP.deleteConfirmation(this, '{$lang->confirm_setting_group_deletion}')");
			$table->construct_cell($popup->fetch(), array('class' => 'align_center'));
		}
		else
		{
			$table->construct_cell('');
		}
		$table->construct_row(array('class' => 'alt_row', 'no_alt_row' => 1));

		// Make rows for each setting in the group
		if(isset($settings_cache[$group['gid']]) && is_array($settings_cache[$group['gid']]))
		{
			foreach($settings_cache[$group['gid']] as $setting)
			{
				$setting_lang_var = "setting_{$setting['name']}";
				if(!empty($lang->$setting_lang_var))
				{
					$setting_title = htmlspecialchars_uni($lang->$setting_lang_var);
				}
				else
				{
					$setting_title = htmlspecialchars_uni($setting['title']);
				}
				$table->construct_cell($setting_title, array('style' => 'padding-left: 40px;'));
				$table->construct_cell($form->generate_numeric_field("setting_disporder[{$setting['sid']}]", $setting['disporder'], array('style' => 'width: 80%', 'class' => 'align_center', 'min' => 0)));
				// Only show options if not a default setting group or is a custom setting
				if($group['isdefault'] != 1 || $setting['isdefault'] != 1)
				{
					$popup = new PopupMenu("setting_{$setting['sid']}", $lang->options);
					$popup->add_item($lang->edit_setting, "index.php?module=config-settings&amp;action=edit&amp;sid={$setting['sid']}");
					$popup->add_item($lang->delete_setting, "index.php?module=config-settings&amp;action=delete&amp;sid={$setting['sid']}&amp;my_post_key={$mybb->post_code}", "return AdminCP.deleteConfirmation(this, '{$lang->confirm_setting_deletion}')");
					$table->construct_cell($popup->fetch(), array('class' => 'align_center'));
				}
				else
				{
					$table->construct_cell('');
				}
				$table->construct_row(array('no_alt_row' => 1, 'class' => "group{$group['gid']}"));
			}
		}
	}

	$table->output($lang->modify_existing_settings);

	$buttons[] = $form->generate_submit_button($lang->save_display_orders);
	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

// Change settings for a specified group.
if($mybb->input['action'] == "change")
{
	$plugins->run_hooks("admin_config_settings_change");

	if($mybb->request_method == "post")
	{
		if(!is_writable(MYBB_ROOT.'inc/settings.php'))
		{
			flash_message($lang->error_chmod_settings_file, 'error');
			admin_redirect("index.php?module=config-settings");
		}

		// Not allowed to be hidden captcha fields
		$disallowed_fields = array(
			'username',
			'password',
			'password2',
			'email',
			'email2',
			'imagestring',
			'imagehash',
			'answer',
			'question_id',
			'allownotices',
			'hideemail',
			'receivepms',
			'pmnotice',
			'emailpmnotify',
			'invisible',
			'subscriptionmethod',
			'timezoneoffset',
			'dstcorrection',
			'language',
			'step',
			'action',
			'agree',
			'regtime',
			'regcheck1',
			'regcheck2',
			'regsubmit'
		);

		$is_current_hiddencaptcha_wrong = in_array($mybb->settings['hiddencaptchaimagefield'], $disallowed_fields);
		if(
			(isset($mybb->input['upsetting']['hiddencaptchaimagefield']) && in_array($mybb->input['upsetting']['hiddencaptchaimagefield'], $disallowed_fields)) ||
			$is_current_hiddencaptcha_wrong
		)
		{
			if(isset($mybb->input['upsetting']['hiddencaptchaimagefield']) && $mybb->input['upsetting']['hiddencaptchaimagefield'] != $mybb->settings['hiddencaptchaimagefield'] && !$is_current_hiddencaptcha_wrong)
			{
				$wrong_value = $mybb->input['upsetting']['hiddencaptchaimagefield'];
				$mybb->input['upsetting']['hiddencaptchaimagefield'] = $mybb->settings['hiddencaptchaimagefield'];
			}
			else
			{
				$wrong_value = $mybb->settings['hiddencaptchaimagefield'];
				$mybb->input['upsetting']['hiddencaptchaimagefield'] = 'email3';
			}

			$lang->success_settings_updated .= $lang->sprintf($lang->success_settings_updated_hiddencaptchaimage, htmlspecialchars_uni($mybb->input['upsetting']['hiddencaptchaimagefield']), htmlspecialchars_uni($wrong_value));
		}

		// Validate avatar dimension inputs
		$gid = (int)$mybb->input['gid'];
		$dimfields = array(
			8 => array('postmaxavatarsize'),
			10 => array('useravatardims', 'maxavatardims'),
			13 => array('memberlistmaxavatarsize')
		);
		if(in_array($gid, array_keys($dimfields)))
		{
			foreach($dimfields[$gid] as $field)
			{
				if(isset($mybb->input['upsetting'][$field]))
				{
					if(preg_match("/\b\d+[|x]{1}\d+\b/i", $mybb->input['upsetting'][$field]) || ($field == 'maxavatardims' && trim($mybb->input['upsetting'][$field]) == ""))
					{
						// If pipe (|) is used normalize to 'x'
						$mybb->input['upsetting'][$field] = str_replace('|', 'x', my_strtolower($mybb->input['upsetting'][$field]));
					}
					else
					{
						flash_message($lang->sprintf($lang->error_format_dimension, $lang->{'error_field_'.$field}), 'error');
						admin_redirect("index.php?module=config-settings&action=change&gid=".$gid);
					}
				}
			}
		}

		// Validate minnamelength, maxnamelength, minpasswordlength (complex and regular) and maxpasswordlength
		if ($gid == 9)
		{
			if (
				isset($mybb->input['upsetting']['minnamelength'], $mybb->input['upsetting']['maxnamelength']) &&
				$mybb->input['upsetting']['minnamelength'] > 0 && $mybb->input['upsetting']['maxnamelength'] > 0 &&
				$mybb->input['upsetting']['minnamelength'] > $mybb->input['upsetting']['maxnamelength'])
			{
				flash_message($lang->error_field_minnamelength, 'error');
				admin_redirect("index.php?module=config-settings&action=change&gid=".$gid);
			}

			if (
				isset($mybb->input['upsetting']['minpasswordlength'], $mybb->input['upsetting']['maxpasswordlength']) &&
				$mybb->input['upsetting']['minpasswordlength'] > 0 && $mybb->input['upsetting']['maxpasswordlength'] > 0 &&
				$mybb->input['upsetting']['minpasswordlength'] > $mybb->input['upsetting']['maxpasswordlength']
			)
			{
				flash_message($lang->error_field_minpasswordlength, 'error');
				admin_redirect("index.php?module=config-settings&action=change&gid=".$gid);
			}

			if (
				isset($mybb->input['upsetting']['minpasswordlength'], $mybb->input['upsetting']['requirecomplexpasswords']) &&
				$mybb->input['upsetting']['requirecomplexpasswords'] && $mybb->input['upsetting']['minpasswordlength'] < 3
			)
			{
				flash_message($lang->error_field_minpasswordlength_complex, 'error');
				admin_redirect("index.php?module=config-settings&action=change&gid=".$gid);
			}
		}
		
		require_once MYBB_ROOT.'inc/class_captcha.php';
		
		// Have we opted for a reCAPTCHA or hCaptcha and not set a public/private key in input?
		$set_captcha_image = false;
		if(isset(
			$mybb->input['upsetting']['captchaimage'],
			$mybb->input['upsetting']['recaptchaprivatekey'],
			$mybb->input['upsetting']['recaptchapublickey'],
			$mybb->input['upsetting']['recaptchascore'],
			$mybb->input['upsetting']['hcaptchaprivatekey'],
			$mybb->input['upsetting']['hcaptchapublickey']
		))
		{
			$captchaimage = $mybb->input['upsetting']['captchaimage'];
			$recaptchaprivatekey = $mybb->input['upsetting']['recaptchaprivatekey'];
			$recaptchapublickey = $mybb->input['upsetting']['recaptchapublickey'];
			$recaptchascore = $mybb->input['upsetting']['recaptchascore'];
			$hcaptchaprivatekey = $mybb->input['upsetting']['hcaptchaprivatekey'];
			$hcaptchapublickey = $mybb->input['upsetting']['hcaptchapublickey'];

			if(in_array($captchaimage, array(captcha::NOCAPTCHA_RECAPTCHA, captcha::RECAPTCHA_INVISIBLE)) && (!$recaptchaprivatekey || !$recaptchapublickey))
			{
				$set_captcha_image = true;
			}
			else if(in_array($captchaimage, array(captcha::RECAPTCHA_V3)) && (!$recaptchaprivatekey || !$recaptchapublickey || !$recaptchascore))
			{
				$set_captcha_image = true;
			}
			else if(in_array($captchaimage, array(captcha::HCAPTCHA, captcha::HCAPTCHA_INVISIBLE)) && (!$hcaptchaprivatekey || !$hcaptchapublickey))
			{
				$set_captcha_image = true;
			}
		}

		//Checking settings for reCAPTCHA or hCaptcha and public/private key not set?
		$captchaimage = $mybb->settings['captchaimage'];
		$recaptchaprivatekey = $mybb->settings['recaptchaprivatekey'];
		$recaptchapublickey = $mybb->settings['recaptchapublickey'];
		$recaptchascore = $mybb->settings['recaptchascore'];
		$hcaptchaprivatekey = $mybb->settings['hcaptchaprivatekey'];
		$hcaptchapublickey = $mybb->settings['hcaptchapublickey'];

		if(in_array($captchaimage, array(captcha::NOCAPTCHA_RECAPTCHA, captcha::RECAPTCHA_INVISIBLE)) && (!$recaptchaprivatekey || !$recaptchapublickey))
		{
			$set_captcha_image = true;
		}
		else if(in_array($captchaimage, array(captcha::RECAPTCHA_V3)) && (!$recaptchaprivatekey || !$recaptchapublickey || !$recaptchascore))
		{
			$set_captcha_image = true;
		}
		else if(in_array($captchaimage, array(captcha::HCAPTCHA, captcha::HCAPTCHA_INVISIBLE)) && (!$hcaptchaprivatekey || !$hcaptchapublickey))
		{
			$set_captcha_image = true;
		}
		if($set_captcha_image){
			$mybb->input['upsetting']['captchaimage'] = captcha::DEFAULT_CAPTCHA;
			$lang->success_settings_updated .= $lang->success_settings_updated_captchaimage;
		}

		// If using fulltext then enforce minimum word length given by database
		if(isset($mybb->input['upsetting']['minsearchword']) && $mybb->input['upsetting']['minsearchword'] > 0 && $mybb->input['upsetting']['searchtype'] == "fulltext" && $db->supports_fulltext_boolean("posts") && $db->supports_fulltext("threads"))
		{
			// Attempt to determine minimum word length from MySQL for fulltext searches
			$query = $db->query("SHOW VARIABLES LIKE 'ft_min_word_len';");
			$min_length = $db->fetch_field($query, 'Value');
			if(is_numeric($min_length) && $mybb->input['upsetting']['minsearchword'] < $min_length)
			{
				$mybb->input['upsetting']['minsearchword'] = $min_length;
				$lang->success_settings_updated .= $lang->success_settings_updated_minsearchword;
			}
		}

		// Get settings which optionscode is a forum/group select, checkbox or numeric
		// We cannot rely on user input to decide this
		$checkbox_settings = $forum_group_select = $prefix_select = array();
		$query = $db->simple_select('settings', 'name, optionscode', "optionscode IN('forumselect', 'groupselect', 'prefixselect') OR optionscode LIKE 'checkbox%' OR optionscode LIKE 'numeric%'");

		while($multisetting = $db->fetch_array($query))
		{
			$options = array();

			if(substr($multisetting['optionscode'], 0, 8) == 'checkbox')
			{
				$checkbox_settings[] = $multisetting['name'];

				// All checkboxes deselected = no $mybb->input['upsetting'] for them, we need to initialize it manually then, but only on pages where the setting is shown
				if(empty($mybb->input['upsetting'][$multisetting['name']]) && isset($mybb->input["isvisible_{$multisetting['name']}"]))
				{
					$mybb->input['upsetting'][$multisetting['name']] = array();
				}
			}
			elseif(substr($multisetting['optionscode'], 0, 7) == 'numeric')
			{
				if(isset($mybb->input['upsetting'][$multisetting['name']]))
				{
					$type = explode("\n", $multisetting['optionscode']);
					for($i=1; $i < count($type); $i++)
					{
						$optionsexp = explode("=", $type[$i]);
						$opt = array_map('trim', $optionsexp);
						if(in_array($opt[0], array('min', 'max', 'step')))
						{
							if($opt[0] != 'step' || $opt[1] != 'any')
							{
								$opt[1] = (float)$opt[1];
							}
							$options[$opt[0]] = $opt[1];
						}
					}

					$value = (float)$mybb->input['upsetting'][$multisetting['name']];

					if(isset($options['min']))
					{
						$value = max($value, $options['min']);
					}

					if(isset($options['max']))
					{
						$value = min($value, $options['max']);
					}

					$mybb->input['upsetting'][$multisetting['name']] = $value;
				}
			}
			else
			{
				$forum_group_select[] = $multisetting['name'];
			}
		}

		// Verify for admin email that can't be empty
		if(isset($mybb->input['upsetting']['adminemail']) && !validate_email_format($mybb->input['upsetting']['adminemail']))
		{
			unset($mybb->input['upsetting']['adminemail']);
			$lang->success_settings_updated .= $lang->error_admin_email_settings_empty;
		}

		// Administrator is changing the login method.
		if(isset($mybb->input['upsetting']['username_method']) && (int)$mybb->input['upsetting']['username_method'] > 0)
		{
			if((int)$mybb->settings['allowmultipleemails'] == 1)
			{
				$mybb->input['upsetting']['username_method'] = 0;
				$lang->success_settings_updated .= $lang->success_settings_updated_username_method_conflict;
			}
			else
			{
				$query = $db->simple_select('users', 'email', "email != ''", array('group_by' => 'email HAVING COUNT(email)>1'));
				if($db->num_rows($query))
				{
					$mybb->input['upsetting']['username_method'] = 0;
					$lang->success_settings_updated .= $lang->success_settings_updated_username_method;
				}
			}
		}

		if(isset($mybb->input['upsetting']['username_method'], $mybb->input['upsetting']['allowmultipleemails']))
		{
			// Administrator is changing registration email allowance
			if((int)$mybb->settings['username_method'] > 0 && (int)$mybb->input['upsetting']['allowmultipleemails'] !== 0)
			{
				$mybb->input['upsetting']['allowmultipleemails'] = 0;
				$lang->success_settings_updated .= $lang->success_settings_updated_allowmultipleemails;
			}

			// Reset conflict silently, if by chance
			if((int)$mybb->settings['username_method'] > 0 && (int)$mybb->settings['allowmultipleemails'] == 1)
			{
				$mybb->input['upsetting']['allowmultipleemails'] = 0;
			}
		}

		// reject dangerous/unsupported upload paths
		$fields = array(
			'uploadspath',
			'cdnpath',
			'avataruploadpath',
		);

		$dynamic_include_directories = array(
			MYBB_ROOT.'cache/',
			MYBB_ROOT.'inc/plugins/',
			MYBB_ROOT.'inc/languages/',
			MYBB_ROOT.'inc/tasks/',
		);
		$dynamic_include_directories_realpath = array_map('realpath', $dynamic_include_directories);

		foreach($fields as $field)
		{
			if(isset($mybb->input['upsetting'][$field]))
			{
				if(
					is_string($mybb->input['upsetting'][$field]) &&
					strpos($mybb->input['upsetting'][$field], '://') !== false)
				{
					unset($mybb->input['upsetting'][$field]);
					continue;
				}

				$realpath = realpath(mk_path_abs($mybb->input['upsetting'][$field]));

				if ($realpath === false)
				{
					unset($mybb->input['upsetting'][$field]);
					continue;
				}

				foreach ($dynamic_include_directories_realpath as $forbidden_realpath)
				{
					if ($realpath === $forbidden_realpath || strpos($realpath, $forbidden_realpath.DIRECTORY_SEPARATOR) === 0)
					{
						unset($mybb->input['upsetting'][$field]);
						continue 2;
					}
				}
			}
		}

		// reject dangerous/unsupported file paths
		$field = 'errorloglocation';

		if(isset($mybb->input['upsetting'][$field]) && is_string($mybb->input['upsetting'][$field]))
		{
			if(
				strpos($mybb->input['upsetting'][$field], '://') !== false ||
				substr($mybb->input['upsetting'][$field], -4) === '.php'
			)
			{
				unset($mybb->input['upsetting'][$field]);
			}
		}

		if(is_array($mybb->input['upsetting']))
		{
			foreach($mybb->input['upsetting'] as $name => $value)
			{
				if($forum_group_select && in_array($name, $forum_group_select))
				{
					if($value == 'all')
					{
						$value = -1;
					}
					elseif($value == 'custom')
					{
						if(isset($mybb->input['select'][$name]) && is_array($mybb->input['select'][$name]))
						{
							foreach($mybb->input['select'][$name] as &$val)
							{
								$val = (int)$val;
							}
							unset($val);

							$value = implode(',', $mybb->input['select'][$name]);
						}
						else
						{
							$value = '';
						}
					}
					else
					{
						$value = '';
					}
				}
				elseif($checkbox_settings && in_array($name, $checkbox_settings))
				{
					$value = '';

					if(is_array($mybb->input['upsetting'][$name]))
					{
						$value = implode(',', $mybb->input['upsetting'][$name]);
					}
				}

				$db->update_query("settings", array('value' => $db->escape_string($value)), "name='".$db->escape_string($name)."'");
			}
		}

		// Check if we need to create our fulltext index after changing the search mode
		if(
			isset($mybb->input['upsetting']['searchtype']) &&
			$mybb->settings['searchtype'] != $mybb->input['upsetting']['searchtype'] &&
			$mybb->input['upsetting']['searchtype'] == "fulltext"
		)
		{
			if(!$db->is_fulltext("posts") && $db->supports_fulltext_boolean("posts"))
			{
				$db->create_fulltext_index("posts", "message");
			}
			if(!$db->is_fulltext("threads") && $db->supports_fulltext("threads"))
			{
				$db->create_fulltext_index("threads", "subject");
			}
		}

		// If the delayedthreadviews setting was changed, enable or disable the tasks for it.
		if(isset($mybb->input['upsetting']['delayedthreadviews']) && $mybb->settings['delayedthreadviews'] != $mybb->input['upsetting']['delayedthreadviews'])
		{
			$db->update_query("tasks", array('enabled' => (int)$mybb->input['upsetting']['delayedthreadviews']), "file='threadviews'");
		}

		// Have we changed our cookie prefix? If so, update our adminsid so we're not logged out
		if(isset($mybb->input['upsetting']['cookieprefix']) && $mybb->input['upsetting']['cookieprefix'] != $mybb->settings['cookieprefix'])
		{
			my_unsetcookie("adminsid");
			$mybb->settings['cookieprefix'] = $mybb->input['upsetting']['cookieprefix'];
			my_setcookie("adminsid", $admin_session['sid'], '', true, "strict");
		}

		if(isset($mybb->input['upsetting']['statstopreferrer']) && $mybb->input['upsetting']['statstopreferrer'] != $mybb->settings['statstopreferrer'])
		{
			$cache->update_statistics();
		}

		$statslimit = $mybb->settings['statslimit'];

		rebuild_settings();

		if(isset($mybb->input['upsetting']['statslimit']) && $mybb->input['upsetting']['statslimit'] != $statslimit)
		{
			$cache->update_most_replied_threads();
			$cache->update_most_viewed_threads();
		}

		$plugins->run_hooks("admin_config_settings_change_commit");

		// Log admin action
		log_admin_action();

		flash_message($lang->success_settings_updated, 'success');
		admin_redirect("index.php?module=config-settings");
	}

	// What type of page
	$cache_groups = $cache_settings = array();
	if(isset($mybb->input['search']))
	{
		// Search

		// Search for settings
		$search = trim($mybb->input['search']);
		if(!empty($search))
		{
			$query = $db->query("
				SELECT s.* , g.name as gname, g.title as gtitle, g.description as gdescription
				FROM ".TABLE_PREFIX."settings s
				LEFT JOIN ".TABLE_PREFIX."settinggroups g ON(s.gid=g.gid)
				ORDER BY s.disporder
			");
			while($setting = $db->fetch_array($query))
			{
				$search_in = $setting['name'] . ' ' . $setting['title'] . ' ' . $setting['description'] . ' ' . $setting['gname'] . ' ' . $setting['gtitle'] . ' ' . $setting['gdescription'];
				foreach(array("setting_{$setting['name']}", "setting_{$setting['name']}_desc", "setting_group_{$setting['gname']}", "setting_group_{$setting['gname']}_desc") as $search_in_lang_key)
				{
					if(!empty($lang->$search_in_lang_key))
					{
						$search_in .= ' ' . $lang->$search_in_lang_key;
					}
				}
				if(my_stripos($search_in, $search) !== false)
				{
					$cache_settings[$setting['gid']][$setting['sid']] = $setting;
				}
			}
		}
		if(!count($cache_settings))
		{
			if(isset($mybb->input['ajax_search']))
			{
				echo json_encode(array("errors" => array($lang->error_no_settings_found)));
				exit;
			}
			else
			{
				flash_message($lang->error_no_settings_found, 'error');
				admin_redirect("index.php?module=config-settings");
			}
		}

		// Cache groups
		$groups = array_keys($cache_settings);
		$groups = implode(',', $groups);
		$query = $db->simple_select("settinggroups", "*", "gid IN ({$groups})", array('order_by' => 'disporder'));
		while($group = $db->fetch_array($query))
		{
			$cache_groups[$group['gid']] = $group;
		}

		// Page header only if not AJAX
		if(!isset($mybb->input['ajax_search']))
		{
			$page->add_breadcrumb_item($lang->settings_search);
			$page->output_header($lang->board_settings." - {$lang->settings_search}");
		}
	}
	elseif(($mybb->get_input('gid')))
	{
		// Group listing
		// Cache groups
		$query = $db->simple_select("settinggroups", "*", "gid = '".$mybb->get_input('gid', MyBB::INPUT_INT)."'");
		$groupinfo = $db->fetch_array($query);
		$cache_groups[$groupinfo['gid']] = $groupinfo;

		if(!$db->num_rows($query))
		{
			$page->output_error($lang->error_invalid_gid2);
		}

		// Cache settings
		$query = $db->simple_select("settings", "*", "gid='".$mybb->get_input('gid', MyBB::INPUT_INT)."'", array('order_by' => 'disporder'));
		while($setting = $db->fetch_array($query))
		{
			$cache_settings[$setting['gid']][$setting['sid']] = $setting;
		}

		if(!$db->num_rows($query))
		{
			flash_message($lang->error_no_settings_found, 'error');
			admin_redirect("index.php?module=config-settings");
		}

		$group_lang_var = "setting_group_{$groupinfo['name']}";
		if(isset($lang->$group_lang_var))
		{
			$groupinfo['title'] = $lang->$group_lang_var;
		}

		$groupinfo['title'] = htmlspecialchars_uni($groupinfo['title']);

		// Page header
		$page->add_breadcrumb_item($groupinfo['title']);
		$page->output_header($lang->board_settings." - {$groupinfo['title']}");
	}
	else
	{
		// All settings list
		// Cache groups
		$query = $db->simple_select("settinggroups", "*", "", array('order_by' => 'disporder'));
		while($group = $db->fetch_array($query))
		{
			$cache_groups[$group['gid']] = $group;
		}

		if(!$db->num_rows($query))
		{
			$page->output_error($lang->error_invalid_gid2);
		}

		// Cache settings
		$query = $db->simple_select("settings", "*", "", array('order_by' => 'disporder'));
		while($setting = $db->fetch_array($query))
		{
			$cache_settings[$setting['gid']][$setting['sid']] = $setting;
		}

		// Page header
		$page->add_breadcrumb_item($lang->show_all_settings);
		$page->output_header($lang->board_settings." - {$lang->show_all_settings}");
	}

	// Build individual forms as per settings group
	foreach($cache_groups as $groupinfo)
	{
		$form = new Form("index.php?module=config-settings&amp;action=change", "post", "change");
		echo $form->generate_hidden_field("gid", $groupinfo['gid']);
		$buttons = array($form->generate_submit_button($lang->save_settings));
		$group_lang_var = "setting_group_{$groupinfo['name']}";
		if(isset($lang->$group_lang_var))
		{
			$groupinfo['title'] = $lang->$group_lang_var;
		}

		$groupinfo['title'] = htmlspecialchars_uni($groupinfo['title']);

		$form_container = new FormContainer($groupinfo['title']);

		if(empty($cache_settings[$groupinfo['gid']]))
		{
			$form_container->output_cell($lang->error_no_settings_found);
			$form_container->construct_row();

			$form_container->end();
			echo '<br />';

			continue;
		}

		foreach($cache_settings[$groupinfo['gid']] as $setting)
		{
						if($setting['name'] == "cookiepath" || $setting['name'] == "cookiedomain" || $setting['name'] == "cookiedomain" || $setting['name'] == "cookieprefix")
			{
				continue; // Skip these settings in the Admin CP
			}
			$setting['name'] = htmlspecialchars_uni($setting['name']);

			$options = "";
			$type = explode("\n", $setting['optionscode']);
			$type[0] = trim($type[0]);
			$element_name = "upsetting[{$setting['name']}]";
			$element_id = "setting_{$setting['name']}";
			if($type[0] == "text" || $type[0] == "")
			{
				$setting_code = $form->generate_text_box($element_name, $setting['value'], array('id' => $element_id));
			}
			else if($type[0] == "numeric")
			{
				$field_options = array('id' => $element_id);
				if(count($type) > 1)
				{
					for($i=1; $i < count($type); $i++)
					{
						$optionsexp = explode("=", $type[$i]);
						$opt = array_map('trim', $optionsexp);
						if(in_array($opt[0], array('min', 'max', 'step')))
						{
							if($opt[0] != 'step' || $opt[1] != 'any')
							{
								$opt[1] = (float)$opt[1];
							}
							$field_options[$opt[0]] = $opt[1];
						}
					}
				}
				$setting_code = $form->generate_numeric_field($element_name, $setting['value'], $field_options);
			}
			else if($type[0] == "textarea")
			{
				$setting_code = $form->generate_text_area($element_name, $setting['value'], array('id' => $element_id));
			}
			else if($type[0] == "yesno")
			{
				$setting_code = $form->generate_yes_no_radio($element_name, $setting['value'], true, array('id' => $element_id.'_yes', 'class' => $element_id), array('id' => $element_id.'_no', 'class' => $element_id));
			}
			else if($type[0] == "onoff")
			{
				$setting_code = $form->generate_on_off_radio($element_name, $setting['value'], true, array('id' => $element_id.'_on', 'class' => $element_id), array('id' => $element_id.'_off', 'class' => $element_id));
			}
			else if($type[0] == "cpstyle")
			{
				$dir = @opendir(MYBB_ROOT.$config['admin_dir']."/styles");

				$folders = array();
				while($folder = readdir($dir))
				{
					if($folder != "." && $folder != ".." && @file_exists(MYBB_ROOT.$config['admin_dir']."/styles/$folder/main.css"))
					{
						$folders[$folder] = ucfirst($folder);
					}
				}
				closedir($dir);
				ksort($folders);
				$setting_code = $form->generate_select_box($element_name, $folders, $setting['value'], array('id' => $element_id));
			}
			else if($type[0] == "language")
			{
				$languages = $lang->get_languages();
				$setting_code = $form->generate_select_box($element_name, $languages, $setting['value'], array('id' => $element_id));
			}
			else if($type[0] == "adminlanguage")
			{
				$languages = $lang->get_languages(1);
				$setting_code = $form->generate_select_box($element_name, $languages, $setting['value'], array('id' => $element_id));
			}
			else if($type[0] == "passwordbox")
			{
				$setting_code = $form->generate_password_box($element_name, $setting['value'], array('id' => $element_id));
			}
			else if($type[0] == "php")
			{
				$setting['optionscode'] = substr($setting['optionscode'], 3);
				eval("\$setting_code = \"".$setting['optionscode']."\";");
			}
			else if($type[0] == "forumselect")
			{
				$selected_values = '';
				if($setting['value'] != '' && $setting['value'] != -1)
				{
					$selected_values = explode(',', (string)$setting['value']);

					foreach($selected_values as &$value)
					{
						$value = (int)$value;
					}
					unset($value);
				}

				$forum_checked = array('all' => '', 'custom' => '', 'none' => '');
				if($setting['value'] == -1)
				{
					$forum_checked['all'] = 'checked="checked"';
				}
				elseif($setting['value'] != '')
				{
					$forum_checked['custom'] = 'checked="checked"';
				}
				else
				{
					$forum_checked['none'] = 'checked="checked"';
				}

				print_selection_javascript();

				$setting_code = "
				<dl style=\"margin-top: 0; margin-bottom: 0; width: 100%\">
					<dt><label style=\"display: block;\"><input type=\"radio\" name=\"{$element_name}\" value=\"all\" {$forum_checked['all']} class=\"{$element_id}_forums_groups_check\" onclick=\"checkAction('{$element_id}');\" style=\"vertical-align: middle;\" /> <strong>{$lang->all_forums}</strong></label></dt>
					<dt><label style=\"display: block;\"><input type=\"radio\" name=\"{$element_name}\" value=\"custom\" {$forum_checked['custom']} class=\"{$element_id}_forums_groups_check\" onclick=\"checkAction('{$element_id}');\" style=\"vertical-align: middle;\" /> <strong>{$lang->select_forums}</strong></label></dt>
					<dd style=\"margin-top: 4px;\" id=\"{$element_id}_forums_groups_custom\" class=\"{$element_id}_forums_groups\">
						<table cellpadding=\"4\">
							<tr>
								<td valign=\"top\"><small>{$lang->forums_colon}</small></td>
								<td>".$form->generate_forum_select('select['.$setting['name'].'][]', $selected_values, array('id' => $element_id, 'multiple' => true, 'size' => 5))."</td>
							</tr>
						</table>
					</dd>
					<dt><label style=\"display: block;\"><input type=\"radio\" name=\"{$element_name}\" value=\"none\" {$forum_checked['none']} class=\"{$element_id}_forums_groups_check\" onclick=\"checkAction('{$element_id}');\" style=\"vertical-align: middle;\" /> <strong>{$lang->none}</strong></label></dt>
				</dl>
				<script type=\"text/javascript\">
					checkAction('{$element_id}');
				</script>";
			}
			else if($type[0] == "forumselectsingle")
			{
				$selected_value = (int)$setting['value']; // No need to check if empty, int will give 0
				$setting_code = $form->generate_forum_select($element_name, $selected_value, array('id' => $element_id, 'main_option' => $lang->none));
			}
			else if($type[0] == "groupselect")
			{
				$selected_values = '';
				if($setting['value'] != '' && $setting['value'] != -1)
				{
					$selected_values = explode(',', (string)$setting['value']);

					foreach($selected_values as &$value)
					{
						$value = (int)$value;
					}
					unset($value);
				}

				$group_checked = array('all' => '', 'custom' => '', 'none' => '');
				if($setting['value'] == -1)
				{
					$group_checked['all'] = 'checked="checked"';
				}
				elseif($setting['value'] != '')
				{
					$group_checked['custom'] = 'checked="checked"';
				}
				else
				{
					$group_checked['none'] = 'checked="checked"';
				}

				print_selection_javascript();

				$setting_code = "
				<dl style=\"margin-top: 0; margin-bottom: 0; width: 100%\">
					<dt><label style=\"display: block;\"><input type=\"radio\" name=\"{$element_name}\" value=\"all\" {$group_checked['all']} class=\"{$element_id}_forums_groups_check\" onclick=\"checkAction('{$element_id}');\" style=\"vertical-align: middle;\" /> <strong>{$lang->all_groups}</strong></label></dt>
					<dt><label style=\"display: block;\"><input type=\"radio\" name=\"{$element_name}\" value=\"custom\" {$group_checked['custom']} class=\"{$element_id}_forums_groups_check\" onclick=\"checkAction('{$element_id}');\" style=\"vertical-align: middle;\" /> <strong>{$lang->select_groups}</strong></label></dt>
					<dd style=\"margin-top: 4px;\" id=\"{$element_id}_forums_groups_custom\" class=\"{$element_id}_forums_groups\">
						<table cellpadding=\"4\">
							<tr>
								<td valign=\"top\"><small>{$lang->groups_colon}</small></td>
								<td>".$form->generate_group_select('select['.$setting['name'].'][]', $selected_values, array('id' => $element_id, 'multiple' => true, 'size' => 5))."</td>
							</tr>
						</table>
					</dd>
					<dt><label style=\"display: block;\"><input type=\"radio\" name=\"{$element_name}\" value=\"none\" {$group_checked['none']} class=\"{$element_id}_forums_groups_check\" onclick=\"checkAction('{$element_id}');\" style=\"vertical-align: middle;\" /> <strong>{$lang->none}</strong></label></dt>
				</dl>
				<script type=\"text/javascript\">
					checkAction('{$element_id}');
				</script>";
			}
			else if($type[0] == "groupselectsingle")
			{
				$selected_value = (int)$setting['value']; // No need to check if empty, int will give 0
				$setting_code = $form->generate_group_select($element_name, $selected_value, array('id' => $element_id, 'main_option' => $lang->none));
			}
			else if($type[0] == "prefixselect")
			{
				$selected_values = '';
				if($setting['value'] != '' && $setting['value'] != -1)
				{
					$selected_values = explode(',', (string)$setting['value']);
					foreach($selected_values as &$value)
					{
						$value = (int)$value;
					}
					unset($value);
				}
				$prefix_checked = array('all' => '', 'custom' => '', 'none' => '');
				if($setting['value'] == -1)
				{
					$prefix_checked['all'] = 'checked="checked"';
				}
				elseif($setting['value'] != '')
				{
					$prefix_checked['custom'] = 'checked="checked"';
				}
				else
				{
					$prefix_checked['none'] = 'checked="checked"';
				}
				print_selection_javascript();
				$setting_code = "
				<dl style=\"margin-top: 0; margin-bottom: 0; width: 100%\">
					<dt><label style=\"display: block;\"><input type=\"radio\" name=\"{$element_name}\" value=\"all\" {$prefix_checked['all']} class=\"{$element_id}_forums_groups_check\" onclick=\"checkAction('{$element_id}');\" style=\"vertical-align: middle;\" /> <strong>{$lang->all_prefix}</strong></label></dt>
					<dt><label style=\"display: block;\"><input type=\"radio\" name=\"{$element_name}\" value=\"custom\" {$prefix_checked['custom']} class=\"{$element_id}_forums_groups_check\" onclick=\"checkAction('{$element_id}');\" style=\"vertical-align: middle;\" /> <strong>{$lang->select_prefix}</strong></label></dt>
					<dd style=\"margin-top: 4px;\" id=\"{$element_id}_forums_groups_custom\" class=\"{$element_id}_forums_groups\">
						<table cellpadding=\"4\">
							<tr>
								<td valign=\"top\"><small>{$lang->prefix_colon}</small></td>
								<td>".$form->generate_prefix_select('select['.$setting['name'].'][]', $selected_values, array('id' => $element_id, 'multiple' => true, 'size' => 5))."</td>
							</tr>
						</table>
					</dd>
					<dt><label style=\"display: block;\"><input type=\"radio\" name=\"{$element_name}\" value=\"none\" {$prefix_checked['none']} class=\"{$element_id}_forums_groups_check\" onclick=\"checkAction('{$element_id}');\" style=\"vertical-align: middle;\" /> <strong>{$lang->none}</strong></label></dt>
				</dl>
				<script type=\"text/javascript\">
					checkAction('{$element_id}');
				</script>";
			}
			else
			{
				$typecount = count($type);

				if($type[0] == 'checkbox')
				{
					$multivalue = explode(',', $setting['value']);
				}

				$option_list = array();
				for($i = 0; $i < $typecount; $i++)
				{
					$optionsexp = explode("=", $type[$i]);
					if(!isset($optionsexp[1]))
					{
						continue;
					}
					$title_lang = "setting_{$setting['name']}_{$optionsexp[0]}";
					if(isset($lang->$title_lang))
					{
						$optionsexp[1] = $lang->$title_lang;
					}

					if($type[0] == "select")
					{
						$option_list[$optionsexp[0]] = htmlspecialchars_uni($optionsexp[1]);
					}
					else if($type[0] == "radio")
					{
						if($setting['value'] == $optionsexp[0])
						{
							$option_list[$i] = $form->generate_radio_button($element_name, $optionsexp[0], htmlspecialchars_uni($optionsexp[1]), array('id' => $element_id.'_'.$i, "checked" => 1, 'class' => $element_id));
						}
						else
						{
							$option_list[$i] = $form->generate_radio_button($element_name, $optionsexp[0], htmlspecialchars_uni($optionsexp[1]), array('id' => $element_id.'_'.$i, 'class' => $element_id));
						}
					}
					else if($type[0] == "checkbox")
					{
						if(in_array($optionsexp[0], $multivalue))
						{
							$option_list[$i] = $form->generate_check_box("{$element_name}[]", $optionsexp[0], htmlspecialchars_uni($optionsexp[1]), array('id' => $element_id.'_'.$i, "checked" => 1, 'class' => $element_id));
						}
						else
						{
							$option_list[$i] = $form->generate_check_box("{$element_name}[]", $optionsexp[0], htmlspecialchars_uni($optionsexp[1]), array('id' => $element_id.'_'.$i, 'class' => $element_id));
						}
					}
				}

				if($type[0] == "select")
				{
					$setting_code = $form->generate_select_box($element_name, $option_list, $setting['value'], array('id' => $element_id));
				}
				else
				{
					$setting_code = implode("<br />", $option_list);

					if($type[0] == 'checkbox')
					{
						$setting_code .= $form->generate_hidden_field("isvisible_{$setting['name']}", 1);
					}
				}
			}

			// Do we have a custom language variable for this title or description?
			$title_lang = "setting_".$setting['name'];
			$desc_lang = $title_lang."_desc";
			if(isset($lang->$title_lang))
			{
				$setting['title'] = $lang->$title_lang;
			}
			if(isset($lang->$desc_lang))
			{
				$setting['description'] = $lang->$desc_lang;
			}
			$form_container->output_row(htmlspecialchars_uni($setting['title']), $setting['description'], $setting_code, '', array(), array('id' => 'row_'.$element_id));
		}
		$form_container->end();

		$form->output_submit_wrapper($buttons);
		$form->end();
		echo '<br />';
	}

	print_setting_peekers();

	if(!isset($mybb->input['ajax_search']))
	{
		$page->output_footer();
	}
}

if(!$mybb->input['action'])
{
	$plugins->run_hooks("admin_config_settings_start");

	$page->extra_header .= <<<EOF
	<script type="text/javascript">
	<!--
	lang.searching = "{$lang->searching}";
	lang.search_error = "{$lang->search_error}";
	lang.search_done = "{$lang->search_done}";
	// -->
	</script>
EOF;

	$page->output_header($lang->board_settings);
	if(isset($message))
	{
		$page->output_inline_message($message);
	}

	$sub_tabs['change_settings'] = array(
		'title' => $lang->change_settings,
		'link' => "index.php?module=config-settings",
		'description' => $lang->change_settings_desc
	);

	$sub_tabs['add_setting'] = array(
		'title' => $lang->add_new_setting,
		'link' => "index.php?module=config-settings&amp;action=add"
	);

	$sub_tabs['add_setting_group'] = array(
		'title' => $lang->add_new_setting_group,
		'link' => "index.php?module=config-settings&amp;action=addgroup"
	);

	$sub_tabs['modify_setting'] = array(
		'title' => $lang->modify_existing_settings,
		'link' => "index.php?module=config-settings&amp;action=manage",
	);

	$page->output_nav_tabs($sub_tabs, 'change_settings');

	// Search form
	echo "<div style=\"text-align: right; margin-bottom: 3px;\">";
	$search = new Form("index.php", 'get', 'settings_search', 0, 'settings_search');
	echo $search->generate_hidden_field('module', 'config/settings');
	echo $search->generate_hidden_field('action', 'change');
	echo $search->generate_text_box('search', $lang->settings_search, array('id' => 'search', 'class' => 'search_default field150 field_small'));
	echo "<input type=\"submit\" class=\"search_button\" value=\"{$lang->search}\" />";
	$search->end();
	echo "</div>\n";

	echo '<div id="search_results">&nbsp;</div><div id="group_list">';
	$table = new Table;
	$table->construct_header($lang->setting_groups);

	switch($db->type)
	{
		case "pgsql":
		$query = $db->query("
			SELECT g.*, COUNT(s.sid) AS settingcount
			FROM ".TABLE_PREFIX."settinggroups g
			LEFT JOIN ".TABLE_PREFIX."settings s ON (s.gid=g.gid)
			WHERE g.isdefault = 1
			GROUP BY ".$db->build_fields_string("settinggroups", "g.")."
			ORDER BY g.disporder
		");
		break;
		default:
		$query = $db->query("
			SELECT g.*, COUNT(s.sid) AS settingcount
			FROM ".TABLE_PREFIX."settinggroups g
			LEFT JOIN ".TABLE_PREFIX."settings s ON (s.gid=g.gid)
			WHERE g.isdefault = 1
			GROUP BY g.gid
			ORDER BY g.disporder
		");
	}
	while($group = $db->fetch_array($query))
	{
		$group_lang_var = "setting_group_{$group['name']}";
		if(isset($lang->$group_lang_var))
		{
			$group_title = htmlspecialchars_uni($lang->$group_lang_var);
		}
		else
		{
			$group_title = htmlspecialchars_uni($group['title']);
		}

		$group_desc_lang_var = "setting_group_{$group['name']}_desc";
		if(isset($lang->$group_desc_lang_var))
		{
			$group_desc = htmlspecialchars_uni($lang->$group_desc_lang_var);
		}
		else
		{
			$group_desc = htmlspecialchars_uni($group['description']);
		}

		$table->construct_cell("<strong><a href=\"index.php?module=config-settings&amp;action=change&amp;gid={$group['gid']}\">{$group_title}</a></strong> ({$group['settingcount']} {$lang->bbsettings})<br /><small>{$group_desc}</small>");
		$table->construct_row();
	}

	$table->output("<span style=\"float: right;\"><small><a href=\"index.php?module=config-settings&amp;action=change\">{$lang->show_all_settings}</a></small></span>{$lang->board_settings}");

	// Plugin Settings
	switch($db->type)
	{
		case "pgsql":
		$query = $db->query("
			SELECT g.*, COUNT(s.sid) AS settingcount
			FROM ".TABLE_PREFIX."settinggroups g
			LEFT JOIN ".TABLE_PREFIX."settings s ON (s.gid=g.gid)
			WHERE g.isdefault <> 1
			GROUP BY ".$db->build_fields_string("settinggroups", "g.")."
			ORDER BY g.disporder
		");
		break;
		default:
		$query = $db->query("
			SELECT g.*, COUNT(s.sid) AS settingcount
			FROM ".TABLE_PREFIX."settinggroups g
			LEFT JOIN ".TABLE_PREFIX."settings s ON (s.gid=g.gid)
			WHERE g.isdefault <> 1
			GROUP BY g.gid
			ORDER BY g.disporder
		");
	}

	if($db->num_rows($query))
	{
		$table = new Table;
		$table->construct_header($lang->setting_groups);

		while($group = $db->fetch_array($query))
		{
			$group_lang_var = "setting_group_{$group['name']}";
			if(isset($lang->$group_lang_var))
			{
				$group_title = htmlspecialchars_uni($lang->$group_lang_var);
			}
			else
			{
				$group_title = htmlspecialchars_uni($group['title']);
			}

			$group_desc_lang_var = "setting_group_{$group['name']}_desc";
			if(isset($lang->$group_desc_lang_var))
			{
				$group_desc = htmlspecialchars_uni($lang->$group_desc_lang_var);
			}
			else
			{
				$group_desc = htmlspecialchars_uni($group['description']);
			}

			$table->construct_cell("<strong><a href=\"index.php?module=config-settings&amp;action=change&amp;gid={$group['gid']}\">{$group_title}</a></strong> ({$group['settingcount']} {$lang->bbsettings})<br /><small>{$group_desc}</small>");
			$table->construct_row();
		}

		$table->output($lang->plugin_settings);
	}

	echo '</div>';

	echo '
<script type="text/javascript" src="./jscripts/search.js?ver=1821"></script>
<script type="text/javascript">
//<!--
$(function(){
	SettingSearch.init("'.$lang->settings_search.'","'.$lang->error_ajax_unknown.'");
});
//-->
</script>';

	print_setting_peekers();
	$page->output_footer();
}

/**
 * Print all the peekers for all of the default settings
 */
function print_setting_peekers()
{
	global $plugins;

	$peekers = array(
		'new Peeker($(".setting_boardclosed"), $("#row_setting_boardclosed_reason"), 1, true)',
		'new Peeker($(".setting_gzipoutput"), $("#row_setting_gziplevel"), 1, true)',
		'new Peeker($(".setting_useerrorhandling"), $("#row_setting_errorlogmedium, #row_setting_errorloglocation"), 1, true)',
		'new Peeker($("#setting_subforumsindex"), $("#row_setting_subforumsstatusicons"), /[^0+|]/, false)',
		'new Peeker($(".setting_showsimilarthreads"), $("#row_setting_similarityrating, #row_setting_similarlimit"), 1, true)',
		'new Peeker($(".setting_disableregs"), $("#row_setting_regtype, #row_setting_securityquestion, #row_setting_regtime, #row_setting_allowmultipleemails, #row_setting_hiddencaptchaimage, #row_setting_betweenregstime"), 0, true)',
		'new Peeker($(".setting_hiddencaptchaimage"), $("#row_setting_hiddencaptchaimagefield"), 1, true)',
		'new Peeker($("#setting_failedlogincount"), $("#row_setting_failedlogintime, #row_setting_failedlogintext"), /[^0+|]/, false)',
		'new Peeker($(".setting_postfloodcheck"), $("#row_setting_postfloodsecs"), 1, true)',
		'new Peeker($("#setting_postmergemins"), $("#row_setting_postmergefignore, #row_setting_postmergeuignore, #row_setting_postmergesep"), /[^0+|]/, false)',
		'new Peeker($(".setting_enablememberlist"), $("#row_setting_membersperpage, #row_setting_default_memberlist_sortby, #row_setting_default_memberlist_order, #row_setting_memberlistmaxavatarsize"), 1, true)',
		'new Peeker($(".setting_enablereputation"), $("#row_setting_repsperpage, #row_setting_posrep, #row_setting_neurep, #row_setting_negrep, #row_setting_postrep, #row_setting_multirep, #row_setting_maxreplength, #row_setting_minreplength"), 1, true)',
		'new Peeker($(".setting_enablewarningsystem"), $("#row_setting_allowcustomwarnings, #row_setting_canviewownwarning, #row_setting_maxwarningpoints, #row_setting_allowanonwarningpms"), 1, true)',
		'new Peeker($(".setting_enablepms"), $("#row_setting_pmsallowhtml, #row_setting_pmsallowmycode, #row_setting_pmsallowsmilies, #row_setting_pmsallowimgcode, #row_setting_pmsallowvideocode, #row_setting_pmquickreply, #row_setting_pmfloodsecs, #row_setting_showpmip, #row_setting_maxpmquotedepth"), 1, true)',
		'new Peeker($(".setting_smilieinserter"), $("#row_setting_smilieinsertertot, #row_setting_smilieinsertercols"), 1, true)',
		'new Peeker($("#setting_mail_handler"), $("#row_setting_smtp_host, #row_setting_smtp_port, #row_setting_smtp_user, #row_setting_smtp_pass, #row_setting_secure_smtp"), "smtp", false)',
		'new Peeker($("#setting_mail_handler"), $("#row_setting_mail_parameters"), "mail", false)',
		'new Peeker($("#setting_captchaimage"), $("#row_setting_recaptchapublickey, #row_setting_recaptchaprivatekey"), /(4|5|8)/, false)',
		'new Peeker($("#setting_captchaimage"), $("#row_setting_recaptchascore"), /(8)/, false)',
		'new Peeker($("#setting_captchaimage"), $("#row_setting_hcaptchapublickey, #row_setting_hcaptchaprivatekey"), /(6|7)/, false)',
		'new Peeker($("#setting_captchaimage"), $("#row_setting_hcaptchaprivatekey, #row_setting_hcaptchaprivatekey"), /(6|7)/, false)',
		'new Peeker($("#setting_captchaimage"), $("#row_setting_hcaptchatheme"), 6, false)',
		'new Peeker($("#setting_captchaimage"), $("#row_setting_hcaptchasize"), 6, false)',
		'new Peeker($(".setting_contact"), $("#row_setting_contact_guests, #row_setting_contact_badwords, #row_setting_contact_maxsubjectlength, #row_setting_contact_minmessagelength, #row_setting_contact_maxmessagelength"), 1, true)',
		'new Peeker($(".setting_enablepruning"), $("#row_setting_enableprunebyposts, #row_setting_pruneunactived, #row_setting_prunethreads"), 1, true)',
		'new Peeker($(".setting_enableprunebyposts"), $("#row_setting_prunepostcount, #row_setting_dayspruneregistered, #row_setting_prunepostcountall"), 1, true)',
		'new Peeker($(".setting_pruneunactived"), $("#row_setting_dayspruneunactivated"), 1, true)',
		'new Peeker($(".setting_statsenabled"), $("#row_setting_statscachetime, #row_setting_statslimit, #row_setting_statstopreferrer"), 1, true)',
		'new Peeker($(".setting_purgespammergroups_forums_groups_check"), $("#row_setting_purgespammerpostlimit, #row_setting_purgespammerbandelete, #row_setting_purgespammerapikey"), /^(?!none)/, true)',
		'new Peeker($(".setting_purgespammerbandelete"),$("#row_setting_purgespammerbangroup, #row_setting_purgespammerbanreason"), "ban", true)',
		'new Peeker($("#setting_maxloginattempts"), $("#row_setting_loginattemptstimeout"), /[^0+|]/, false)',
		'new Peeker($(".setting_bbcodeinserter"), $("#row_setting_partialmode, #row_setting_smilieinserter"), 1, true)',
		'new Peeker($(".setting_portal"), $("#row_setting_portal_announcementsfid, #row_setting_portal_showwelcome, #row_setting_portal_showpms, #row_setting_portal_showstats, #row_setting_portal_showwol, #row_setting_portal_showsearch, #row_setting_portal_showdiscussions"), 1, true)',
		'new Peeker($(".setting_portal_announcementsfid_forums_groups_check"), $("#row_setting_portal_numannouncements"), /^(?!none)/, true)',
		'new Peeker($(".setting_portal_showdiscussions"), $("#row_setting_portal_showdiscussionsnum, #row_setting_portal_excludediscussion"), 1, true)',
		'new Peeker($(".setting_enableattachments"), $("#row_setting_maxattachments, #row_setting_attachthumbnails"), 1, true)',
		'new Peeker($(".setting_attachthumbnails"), $("#row_setting_attachthumbh, #row_setting_attachthumbw"), "yes", true)',
		'new Peeker($(".setting_showbirthdays"), $("#row_setting_showbirthdayspostlimit"), 1, true)',
		'new Peeker($("#setting_betweenregstime"), $("#row_setting_maxregsbetweentime"), /[^0+|]/, false)',
		'new Peeker($(".setting_usecdn"), $("#row_setting_cdnurl, #row_setting_cdnpath"), 1, true)',
		'new Peeker($("#setting_errorlogmedium"), $("#row_setting_errorloglocation"), /^(log|both)/, false)',
		'new Peeker($(".setting_sigmycode"), $("#row_setting_sigcountmycode, #row_setting_sigimgcode"), 1, true)',
		'new Peeker($(".setting_pmsallowmycode"), $("#row_setting_pmsallowimgcode, #row_setting_pmsallowvideocode"), 1, true)',
		'new Peeker($(".setting_enableshowteam"), $("#row_setting_showaddlgroups, #row_setting_showgroupleaders"), 1, true)',
		'new Peeker($(".setting_usereferrals"), $("#row_setting_referralsperpage"), 1, true)',
	);

	$peekers = $plugins->run_hooks("admin_settings_print_peekers", $peekers);

	$setting_peekers = implode("\n			", $peekers);

	echo '<script type="text/javascript" src="./jscripts/peeker.js?ver=1821"></script>
	<script type="text/javascript">
		$(function() {
			' . $setting_peekers . '
		});
	</script>';
}
