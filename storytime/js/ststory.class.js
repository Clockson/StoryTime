/*
*   Inherits from location class which would be an interface if js supported such things.
*   The location class provides a div and get Dom method. Best to add new HTML to this
*   div instead of overwriting it to make remove child work better in the engine.
*/

class STStory extends STLocation {
    constructor(data, stepLoader) {
        super(stepLoader);
        this.template = "<h2></h2><div id='st-storytext'></div><div id='st-option-group'></div>";
        this.optionTemplate = "<button class='st-option button button-primary' value='{optionValue}'>{optionText}</button>";
        this.element.insertAdjacentHTML('beforeend', this.template);

        this.currentStep = data;
        // build the class here
        this.element.querySelector('h2').textContent = data.locationdata.chaptertitle;
        this.element.querySelector('#st-storytext').textContent = data.locationdata.storytext;

        let optionGroup = this.element.querySelector('#st-option-group');
        data.step.options.forEach((item) => {
            let optionHtml = this.optionTemplate.replace('{optionValue}', item.action);
            optionHtml = optionHtml.replace('{optionText}', item.displaytext);
            optionGroup.insertAdjacentHTML('beforeend', optionHtml);
        });
        this.element.querySelectorAll('.st-option').forEach((item) => { item.addEventListener('click', this.startStory.bind(this)); });
    }

    startStory(event) {
        this.stepLoader(event.target.value, () => {});
    }
}