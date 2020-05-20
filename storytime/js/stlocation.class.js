class STLocation {
    constructor(stepLoader) {
        this.element = document.createElement('div');
        this.stepLoader = stepLoader;
    }
    
    getDom() {
        return this.element;
    }
}