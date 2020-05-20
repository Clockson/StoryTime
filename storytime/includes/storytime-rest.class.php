<?php

class StoryTimeRest {
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        register_rest_route( 'storytime/v1', '/getscenario', array(
            'methods' => 'GET',
            'callback' => array( $this, 'load_scenario' )
        ));

        register_rest_route( 'storytime/v1', '/fetchstep', array(
            'methods' => 'GET',
            'callback' => array( $this, 'fetch_step' )
        ));
    }

    public function load_scenario() {
        // find a scenario by name (in future)
        // load in the sceario data
        $scenario = json_decode(file_get_contents(plugin_dir_path(__DIR__).'assets/test.json'));
        // get scenario data and first step
        $response = new stdClass();
        $response->type = 'scenario';
        $response->name = $scenario->scenario;
        $response->title = $scenario->title;
        $response->step = $scenario->scripts->title;

        return new WP_REST_Response(json_encode($response), 200);
    }

    public function fetch_step() {
        if (isset($_GET['scenarioname']) && isset($_GET['steproute'])) {
            // parse the data
            $scenario_name = $_GET['scenarioname'];
            $step_route = explode(':', $_GET['steproute']);

            // find a scenario by name (in future)
            // load in the scenario data
            $scenario = json_decode(file_get_contents(plugin_dir_path(__DIR__).'assets/test.json'));

            // use the step route to find the correct next step
            $step = $scenario->scripts->{$step_route[0]}->actions->{$step_route[1]};

            $response = new stdClass();
            $response->data = new stdClass();
            if ($step_route[1] == 'root') {
                // we're changing location. Load the new location and the step from the action
                $response->type = 'location';
                $response->data->locationdata = $scenario->scripts->{$step_route[0]}->locationdata;
                $response->data->step = $scenario->scripts->{$step_route[0]}->actions->{$step_route[1]};

                // expand dialog
                if (isset($response->data->step->dialog)) {
                    $response->data->step->dialog = $this->expand_dialog($response->data->step->dialog, $scenario->scripts->{$step_route[0]}->npcs);
                }
            } else {
                $response = new stdClass();
                $response->type = 'step';
                $response->data = $scenario->scripts->{$step_route[0]}->actions->{$step_route[1]};

                // expand dialog
                if (isset($response->data->dialog)) {
                    $response->data->dialog = $this->expand_dialog($response->data->dialog, $scenario->scripts->{$step_route[0]}->npcs);
                }
            }

            return new WP_REST_Response(json_encode($response), 200);
        }

        return new WP_REST_Response('', 400);
    }

    private function expand_dialog($dialog, $npcs) {
        $expanded_dialog = [];
        foreach($dialog as $line) {
            $words = new stdClass();
            $words->npc = $npcs->{$line->npc}->name;
            $words->text = $npcs->{$line->npc}->lines->{$line->line}->text;
            array_push($expanded_dialog, $words);
        }

        return $expanded_dialog;
    }
}
