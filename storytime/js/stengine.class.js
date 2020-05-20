class STEngine {
    constructor() {
        this.baseContainer = document.getElementById('storytime-container');
        this.apiroute = document.getElementById('storytime-container').getAttribute('data-rest-baseapi');
        this.nonce = document.getElementById('storytime-container').getAttribute('data-rest-nonce');
        this.loadScenario();
        // this.location = null;
        // this.loadLocation('nottypingtest', {});
    }

    // method called by layout classes to fetch the next story step
    // if the next step is a location then we create the new location
    // otherwise everthing is passed to the calling class
    fetchStep(steproute, callback) {
        this.callApi(
            this.apiroute + '/fetchstep',
            { scenarioname: this.scenarioname, steproute },
            (res) => { 
                let nextStep = JSON.parse(res);
                console.log(nextStep.type);
                if (nextStep.type == 'location') {
                    this.loadLocation(nextStep.data)
                } else if (nextStep.type == 'step') {
                    callback(nextStep.data);
                }
             },
            () => { }
        );
    }

    // Load location takes a type and the location data
    loadLocation(data) {
        let newLocation = null;
        switch (data.locationdata.type) {
            case 'nottypingtest':
                newLocation = new STNotTypingTest(data, this.fetchStep.bind(this));
                break;
            case 'title':
                newLocation = new STTitle(data, this.fetchStep.bind(this));
                break;
            case 'story':
                newLocation = new STStory(data, this.fetchStep.bind(this));
                break;
            case 'npc':
                newLocation = new STNpcEncounter(data, this.fetchStep.bind(this));
                break;
        }

        if (this.location != null) {
            this.baseContainer.removeChild(this.location.getDom());
        }

        if (newLocation != null) {
            this.baseContainer.appendChild(newLocation.getDom());
            this.location = newLocation;
        }
    }

    // request scenario data from the server. It should return the scenario data and the first step
    // which is likely a title screen
    loadScenario() {
        this.callApi(
            this.apiroute + '/getscenario',
            { scenarioname: 'bobwilkins' },
            (res) => {
                console.log(res);
                let scenario = JSON.parse(res);
                this.scenarioname = scenario.name;
                this.loadLocation(scenario.step);
            },
            () => { }
        );
    }

    callApi(url, data, success, error) {
        jQuery.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            data: data,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', this.nonce);
            },
            success: success,
            error: error
        });
    }
}