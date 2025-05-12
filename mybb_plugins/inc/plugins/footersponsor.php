<?php
// Prevent direct access
if (!defined('IN_MYBB')) {
    die('Direct access not allowed.');
}

// Add footer text using global_end
$plugins->add_hook('global_end', 'footersponsor_output');

// Fully suppress this plugin from the Admin CP plugin list
function footersponsor_info()
{
    return [
        'name' => 'RunMyBB Footer Sponsor',
        'description' => 'Adds a permanent footer message promoting RunMyBB. '
                       . '⚠️ If this plugin is deactivated, you will be denied support from the RunMyBB community support forum.',
        'website' => 'https://runmybb.com',
        'author' => 'RunMyBB',
        'version' => '1.0',
        'compatibility' => '*'
    ];
}

// Footer injection
function footersponsor_output()
{
    global $footer;

    $html = '<div style="text-align: center; font-size: 13px; margin-top: 10px;">
        Forum Hosted by <a href="https://runmybb.com" target="_blank" rel="noopener">RunMyBB</a>. Create your forum today!
    </div>';

    $footer .= $html;
}
