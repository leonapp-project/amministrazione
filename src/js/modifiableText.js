class ModifiableTextInput {
    constructor(textId, buttonId, callback) {
      this.textElement = document.getElementById(textId);
      this.buttonElement = document.getElementById(buttonId);
      this.inputElement = null;
      this.callback = callback;
      this.setupEventListeners();
    }
  
    setElementText(text) {
      this.textElement.innerText = text;
    }
  
    createInputElement(text) {
      this.inputElement = document.createElement("input");
      this.inputElement.value = text;
      this.inputElement.className = "input is-size-9";
      this.inputElement.style.width = "200px";
      this.inputElement.style.height = "35px";
      this.inputElement.addEventListener("blur", () => this.onInputBlur());
      this.textElement.replaceWith(this.inputElement);
    }
  
    onButtonClick() {
      if (this.inputElement) {
        console.log("Input element already exists");
        return;
      }
      this.createInputElement(this.textElement.innerText);
      this.buttonElement.querySelector("i").classList.remove("fa-edit");
      this.buttonElement.querySelector("i").classList.add("fa-check");
      this.buttonElement.style.backgroundColor = "#00d1b2";
      this.inputElement.focus();
      this.inputElement.addEventListener("keydown", (event) => this.listenForKeyDown(event, this));
    }
  
    onSaveClick() {
      if (!this.inputElement) {
        return;
      }   
      console.log("save click");
      this.inputElement.replaceWith(this.textElement);
      this.setElementText(this.inputElement.value);
      this.buttonElement.querySelector("i").classList.remove("fa-check");
      this.buttonElement.querySelector("i").classList.add("fa-edit");
      this.buttonElement.style.backgroundColor = "#f5f5f5";
      this.inputElement.removeEventListener("click", this.listenForKeyDown);
      this.inputElement = null;
      
      if (this.callback) {
        this.callback(this.textElement.innerText);
      }
    }
  
    onInputBlur() {
      this.onSaveClick();
    }
  
    listenForKeyDown(event, thiss) {
      if (event.keyCode === 13) {
        if (!thiss.inputElement) {
          return;
        }   
        event.preventDefault();
        console.log(thiss.textElement.innerText);
        console.log(thiss.inputElement.value)
        var doCallback = thiss.textElement.innerText != thiss.inputElement.value;
        console.log(doCallback);
        console.log(this.inputElement)
        console.log(this.textElement)
        const newElement = this.textElement.cloneNode(true);
        const newElement2 = this.inputElement;
        newElement2.replaceWith(newElement); // will print "ob.p"
        console.log("dopo")
        thiss.setElementText(thiss.inputElement.value);
        thiss.buttonElement.querySelector("i").classList.remove("fa-check");
        thiss.buttonElement.querySelector("i").classList.add("fa-edit");
        thiss.buttonElement.style.backgroundColor = "#f5f5f5";
        console.log("removed listener");
        thiss.inputElement.removeEventListener("click", thiss.listenForKeyDown);
        thiss.inputElement = null;
        
        if (doCallback && thiss.callback) {
          console.log("callback")
          thiss.callback(thiss.textElement.innerText);
        }
      }
    }
    
  
    setupEventListeners() {
      this.textElement.addEventListener("dblclick", () => this.onButtonClick());
      this.buttonElement.addEventListener("click", () => {
        if (this.buttonElement.querySelector("i").classList.contains("fa-edit")) {
          this.onButtonClick();
        } else {
          this.onSaveClick();
        }
      });
    }
  }
  