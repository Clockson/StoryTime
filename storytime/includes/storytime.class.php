<?php


class StoryTime {
    public function __construct() {
        $this->load_dependencies();
        add_shortcode('md-story-time', array($this, 'set_up_short_code'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        $rest_manager = new StorytimeRest();
    }

    public function load_dependencies() {
        require_once plugin_dir_path( __FILE__ ) . 'storytime-rest.class.php';
    }

    public function enqueue_scripts() {
        wp_enqueue_script('st-location', plugins_url('js/stlocation.class.js', dirname(__FILE__)));
        wp_enqueue_script('st-nottypingtest', plugins_url('js/stnottypingtest.class.js', dirname(__FILE__)), array('st-location'));
        wp_enqueue_script('st-title', plugins_url('js/sttitle.class.js', dirname(__FILE__)), array('st-location'));
        wp_enqueue_script('st-story', plugins_url('js/ststory.class.js', dirname(__FILE__)), array('st-location'));
        wp_enqueue_script('st-engine', plugins_url('js/stengine.class.js', dirname(__FILE__)), array('st-nottypingtest', 'st-title', 'st-story'));
        wp_enqueue_script('st-loader', plugins_url('js/storytime.js', dirname(__FILE__)), array('st-nottypingtest', 'jquery'));
    }

    public function set_up_short_code() {
        $rest_nonce = esc_attr( wp_create_nonce( 'wp_rest' ) );
        $base_api_url = home_url('wp-json/storytime/v1');
        return "<div id='storytime-container' data-rest-nonce='$rest_nonce' data-rest-baseapi='$base_api_url'></div>";
    }

    public function process_game_state() {
        // get current location and action e.g location = road, action = enter-store
        $story_route = explode(':', isset($_GET['storyaction']) ? $_GET['storyaction'] : "title:root");
        $scenario = json_decode(file_get_contents(plugin_dir_path(__DIR__).'assets/test.json'));
        //var_dump($scenario->scripts->{$story_route[0]});

        // check to see if there is an action at the root of the storyaction $scenario->scripts->$story_route[0]->actions->$story_route[1]->action
        while (isset($scenario->scripts->{$story_route[0]}->actions->{$story_route[1]}->action)) {
            $story_route = explode(':', $scenario->scripts->{$story_route[0]}->actions->{$story_route[1]}->action);
        }

        switch ($scenario->scripts->{$story_route[0]}->type) {
            case 'story':
                $result = $this->load_story_template($scenario->scripts->{$story_route[0]}, $story_route[1]);
            break;
            case 'title':
                $result = $this->load_title_template($scenario->scripts->{$story_route[0]}, $story_route[1]);
            break;
            case 'npc':
                $result = $this->load_npc_template($scenario->scripts->{$story_route[0]}, $story_route[1]);
            break;
            default:
                $result = "Booooooo!";
            break;
        }

        // validate 
        // load our script from a json file

        //$title = $script->scripts[0];
        

        return $result;
    }

    private function load_title_template($title, $action) {
        $title_screen = "<div>";
        $title_screen.= "<div>$title->titletext</div>";

        global $wp;
        foreach ($title->actions->root->options as $option) {
            $link = home_url($wp->request)."/?storyaction=$option->action";
            $title_screen.= "<a href=$link class='button button-primary'>$option->displaytext</a>";
        }

        $title_screen.= "</div>";
        return $title_screen;
    }

    private function load_story_template($story, $action) {
        $title_screen = "<div>";
        $title_screen.= "<h3>$story->chaptertitle</h3>";
        $title_screen.= "<div>$story->storytext</div>";

        global $wp;
        foreach ($story->actions->$action->options as $option) {
            $link = home_url($wp->request)."/?storyaction=$option->action";
            $title_screen.= "<a href=$link class='button button-primary'>$option->displaytext</a>";
        }

        $title_screen.= "</div>";
        return $title_screen;
    }

    private function load_npc_template($npc, $action) {
        $title_screen = "<div>";
        $title_screen.= "<h3>$npc->locationname</h3>";

        // get the action
        $activity = $npc->actions->$action;

        // handle npc lines
        if (isset($activity->lines)) {
            $dialoglocation = explode(':', $activity->lines[0]);
            $text = $npc->npcs->{$dialoglocation[0]}->scripts->{$dialoglocation[1]};
            $title_screen.= "<div><b>{$npc->npcs->{$dialoglocation[0]}->name}:</b> $text->displaytext</div>";
        }

        global $wp;
        foreach ($activity->options as $option) {
            $link = home_url($wp->request)."/?storyaction=$option->action";
            $title_screen.= "<a href=$link class='button button-primary'>$option->displaytext</a>";
        }

        $title_screen.= "</div>";
        return $title_screen;
    }


    //private function load_dependencies
}