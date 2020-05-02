<?php

/*
    Plugin Name: Story Teller 3000
    Description: A simple COA story builder
    Version: 1.0.0
    Author: Mark Danforth
    Text Domain: md-st-text
*/

function activate_story_time() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/storytime-activator.class.php';
    StoryTimeActivator::activate();
}

register_activation_hook(__FILE__, 'activate_story_time');

require_once plugin_dir_path( __FILE__ ) . 'includes/storytime.class.php';

$storytime = new StoryTime();