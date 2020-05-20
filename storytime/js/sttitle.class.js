/*
*   Inherits from location class which would be an interface if js supported such things.
*   The location class provides a div and get Dom method. Best to add new HTML to this
*   div instead of overwriting it to make remove child work better in the engine.
*/

class STTitle extends STLocation {
    constructor(data, stepLoader) {
        super(stepLoader);
        this.template = "<h2></h2><button class='option'>Button</button>";
        this.element.insertAdjacentHTML('beforeend', this.template);

        this.currentStep = data;
        // build the class here
        this.element.querySelector('h2').textContent = data.locationdata.titletext;
        this.element.querySelector('button').textContent = data.actions.root.options[0].displaytext;
        this.element.querySelector('button').value = data.actions.root.options[0].action;
        this.element.querySelector('button').addEventListener('click', this.startStory.bind(this));
    }

    startStory(event) {
        this.stepLoader(event.target.value, () => {});
    }
}