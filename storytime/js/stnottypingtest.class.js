/*
*   Inherits from location class which would be an interface if js supported such things.
*   The location class provides a div and get Dom method. Best to add new HTML to this
*   div instead of overwriting it to make remove child work better in the engine.
*/

class STNotTypingTest extends STLocation {
    constructor(data, stepLoader) {
        super(stepLoader);
        this.template = "<div>My Not Typing Test</div><button class='option'>Button</button>";
        this.element.insertAdjacentHTML('beforeend', this.template);

        // build the class here
        this.element.querySelector('button').addEventListener('click', this.startTest);
    }

    startTest() {
        console.log('test started');
    }
}