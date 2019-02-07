/**
 * Created by hreeves on 6/14/2018.
 *
 * NOTE: Before attempting to use this, make sure your
 *       google chrome settings are correct or the audio
 *       won't work correctly.
 *       1.) Open a new tab
 *       2.) Copy and paste this into the url: chrome://flags/#autoplay-policy
 *       3.) Click the drop down box under "Autoplay Policy"
 *       4.) Select the option: "No user gesture is required"
 *       5.) Then Relaunch chrome and the audio should start once reloading the website
 */
// Initializing global variables
var SCREEN_WIDTH = window.innerWidth;
var SCREEN_HEIGHT = window.innerHeight;
var mousePos = {x: 400, y: 300};
var launchFun, loopFun, timeoutLaunch, timeoutLoop;
var timeOut = 31000, numberRockets = 10, loopSpeed = 20, launchSpeed = 400, musicVol = .1,
    particleSizeModifier = 15, particleSizeBase = 15;
var stop = false;
var mySounds = ["firework_distant_explosion.mp3", "firework_explosion_fizz_001.mp3", "firework_explosion_fizz_002.mp3",
    "firework_explosion_fizz_003.mp3", "firework_explosion_fizz_004.mp3"];
var numberSounds = 0;
var song;
var hasRockets = false, done = false;
var countRockets = 0;


// Creating canvas
var canvas = document.createElement('canvas'),
    context = canvas.getContext('2d'),
    particles= [],
    rockets = [],
    MAX_PARTICLES = 400;

Rocket.prototype = new Particle();
Rocket.prototype.constructor = Rocket;

/**********************************************
 * Function: Initializer
 * Description: Defines canvas properties for the fireworks display
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function initializeFireworks() {
    //var div = document.getElementById("fireworkscroller");
    canvas.id = 'fireworksDisplay';
    canvas.className = "fireworks";
    canvas.height = SCREEN_HEIGHT;
    canvas.width = SCREEN_WIDTH;
    //div.appendChild(canvas);
    document.body.appendChild(canvas);
    //document.body.addEventListener("mousewheel", MouseWheelHandler, false);
    var clap = document.getElementById("clap");
    clap.volume = .075;

    // Try and catch statements so code doesn't fail
    song = new music();
    song.play();
    startFireWorks();
}

/**********************************************
 * Function: Sound
 * Description: An Object that is used for the firework sounds
 * Parameter(s):
 * var src
 * Return value(s): None
 ***********************************************/
function sound(src) {
    numberSounds++;
    this.sound = document.createElement("audio");
    this.sound.onended="destroyAudio()";
    this.sound.src = src;
    this.sound.id = "soundEffects" + numberSounds;
    this.sound.setAttribute("preload", "auto");
    this.sound.setAttribute("controls", "none");
    this.sound.style.display = "none";
    this.sound.volume = musicVol / 10;
    this.name = this.sound.id;
    document.body.appendChild(this.sound);

    // Adding an event listener for whenever the sound effects ends
    this.sound.addEventListener("ended", soundStop(this.sound.id), false);

    // Function used to activate play
    this.play = function() {
        this.sound.autoplay = true;
        this.sound.play();
    }

    // Function used to pause sound
    this.stop = function() {
        this.sound.pause();
    }
}

/**********************************************
 * Function: Sound Stop
 * Description: The event handler for whenever the firework's sound stops
 * Parameter(s):
 * string name - the id of the audio element, comes from the DOM object
 * Return value(s): None
 ***********************************************/
function soundStop(name) {
    removeElement(name);
}

/**********************************************
 * Function: Music
 * Description: An Object that is used for the music
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function music() {
    // Getting Audio
    this.music = document.getElementById("music");

    // Setting Attributes
    this.music.autoplay = true;
    this.music.src = "fireworkSounds/WeAreTheChampions.mp3";
    this.music.volume = musicVol;

    // Resetting the currrentTime to replay the song from the beginning
    this.play = function() {
        this.music.currentTime = 110;
        this.music.volume = musicVol;
        //this.music.currentTime = 145;
        this.music.play();
    }

    this.stop = function() {
        getSoundAndFadeAudio();
    }
}

/**********************************************
 * Function: Get Sound Audio
 * Description: Function runs whenever the song is called to stop
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function getSoundAndFadeAudio () {

    var fadePoint = song.music.currentTime - 4;
    var pass = false;
    var tempFireworks = document.getElementById("fireworksDispaly");
    var tempConfetti = document.getElementById("confetti");
    tempConfetti.style.animationIterationCount = 1;

    setTimeout(function() {
        tempConfetti.style.zIndex = -1;
		document.getElementById("displayTotal").style.animationIterationCount = 0;
    }, 1950);

    var fadeAudio = setInterval(function () {

        // Only fade if past the fade out point or not at zero already
        if ((song.music.currentTime >= fadePoint) && (song.music.volume != 0.0)) {
            try {
                song.music.volume -= 0.009
            } catch(e) {
                console.log("Cannot go below zero for volume.");
                pass = true;
            }
        }
        // When volume at zero stop all the intervaling
        if (song.music.volume === 0.0 || pass) {
            clearInterval(fadeAudio);
            song.music.volume = 0;
            song.music.pause();
        }
    }, 200);

}

/**********************************************
 * Function: Delete Elements
 * Description: Deletes Elements in the HTML DOM
 * Parameter(s):
 * var elementId - The id of the element wanted for removal
 * Return value(s): None
 ***********************************************/
function removeElement(elementId) {
    // Removes an element from the document
    var element = document.getElementById(elementId);
    element.parentNode.removeChild(element);
}


/**********************************************
 * Function: Start Fireworks
 * Description: Starts the firework display
 * Parameter(s): None
 * Return value(s):
 * Will return on error caught
 ***********************************************/
function startFireWorks() {
    // Try and catch statements so code doesn't fail
    document.getElementById("fireworksDisplay").style.zIndex = 1;
    numberRockets = 20;
    stop = false;
    hasRockets = false;
    done = false;
    // Clearing the loop interval before starting firework display
    try {
        clearInterval(loopFun);
    } catch(e) {
        console.error(e);
    }

    try {
        launchFun = setInterval(launch, launchSpeed);
    } catch(e) {
        console.log(e);
        console.log("Cannot start fireworks.");
        return;
    }

    try {
        loopFun = setInterval(loop, loopSpeed);

    } catch(e) {
        console.log(e);
        console.log("Cannot start fireworks.");
        return;
    }

    try {
        setTimers();
    } catch(e) {
        console.log(e);
        console.log("Cannot start fireworks.");
        return;
    }

    console.log("Starting fireworks....");
}

/**********************************************
 * Function: Stop Fireworks Display
 * Description: Stops the fire work display
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function stopFireworks(temp) {
    console.log("Stopping fireworks....");
    stop = true;
    clearLaunch();
    clearLoop(temp);
    clearTimers();
    context.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById("fireworksDisplay").style.zIndex = -1;
    song.stop();
    done = true;
}

/**********************************************
 * Function: Is Canvas Blank
 * Description: Creates a new empty canvas and checks if fireworks display is empty
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function isCanvasBlank() {
    var blank = document.createElement('canvas');
    blank.width = canvas.width;
    blank.height = canvas.height;

    return canvas.toDataURL() == blank.toDataURL();
}

/**********************************************
 * Function: Set Timers
 * Description: This sets and starts the timers for launching and looping
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function setTimers() {
    // Setting Timer
    console.log("Setting timers....");
    timeoutLaunch = setTimeout(clearLaunch, timeOut);
    timeoutLoop = setTimeout(clearLoop, timeOut);
}

/**********************************************
 * Function: Clear Timers
 * Description: Clears the timers
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function clearTimers() {
    clearTimeout(timeoutLaunch);
    clearTimeout(timeoutLoop);
}

/**********************************************
 * Function: Clear Launch
 * Description: Stops the time interval for the launch function
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function clearLaunch() {
    console.log("Clearing launch....");
    clearInterval(launchFun);
    stop = true;
    launchFun = 0;
}

/**********************************************
 * Function: Clear Loop
 * Description: Stops the loop function and sets numberRockets to 0
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function clearLoop(temp) {

    console.log("Stopping the loop of fireworks....");
    if(temp !== true){
        numberRockets = 0;
        console.log("Clearing rockets....");
    }

    else {
        context.clearRect(0, 0, canvas.width, canvas.height);
        console.log("Clearing canvas....");
    }
    done = true;
}

/**********************************************
 * Function: On Mouse Move
 * Description: Keeps record of the mouse position on the screen
 * Parameter(s):
 * Event e - When a mouse move event has occurred
 * Return value(s): None
 ***********************************************/
$(document).mousemove(function(e) {
    e.preventDefault();
    mousePos = {
        x: e.clientX,
        y: e.clientY
    };
});

/**********************************************
 * Function: Launch More Fireworks
 * Description: Launch more rockets onto the screen
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function launchMoreFireworks() {
    for(var i = 0; i < numberRockets / 2; i++)
    {
        launch();
    }
}

/**********************************************
 * Function: Calculate Launch Position
 * Description: Determines the launch position of the rockets
 * Parameter(s): None
 * Return value(s):
 * A random integer value from 100 to screen width - 100
 ***********************************************/
function launchPosition()
{
    return Math.random() * (SCREEN_WIDTH - 100) + 100;
}

/**********************************************
 * Function: Launch
 * Description: Launchs the rockets
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function launch() {
    if(stop !== true){
        launchFrom(launchPosition());
    }
}

/**********************************************
 * Function: Launch From
 * Description: Launches the rockets from position x
 * Parameter(s):
 * int x - Launch Position
 * Return value(s): None
 ***********************************************/
function launchFrom(x) {
    if (rockets.length < numberRockets) {
        var rocket = new Rocket(x);
        rocket.explosionColor = Math.floor(Math.random() * 360 / 10) * 10;
        rocket.vel.y = Math.random() * -3 - 4;
        rocket.vel.x = Math.random() * 6 - 3;
        //rocket.vel.y = (Math.random() * 10) + 5;
        //rocket.vel.y = -5;
        //rocket.vel.x = 5;
        //rocket.vel.x = -5;
        //rocket.vel.x = 0;
        //rocket.vel.x = 2.5;
        //console.log("X: " + rocket.vel.x);
       // console.log("Y: " + rocket.vel.y);
        rocket.size = 8;
        rocket.shrink = 0.999;
        //rocket.angle = Math.atan2(Math.abs(rocket.vel.y), Math.abs(rocket.vel.x)) * 180 / Math.PI;
        rocket.angle = Math.atan2(rocket.vel.y, rocket.vel.x) * 180 / Math.PI;
        rocket.inverseAngle =Math.atan2(rocket.vel.y, -rocket.vel.x) * 180 / Math.PI;
        //console.log("Inverse Angle: " + rocket.inverseAngle);
        //console.log("Path Angle: " + rocket.angle);
        //console.log("Coordinates (" + rocket.pos.x + ", " + rocket.pos.y + ")");
        //context.rotate(rocket.angle);
        //context.drawImage(rocket.img, rocket.pos.x, rocket.pos.y, -rocket.img.width / 2, -rocket.img.height /2);
        //c.rotate(-this.angle);

        rockets.push(rocket);
    }
}

/**********************************************
 * Function: Check Music
 * Description: Used to check if the music needs to stop
 * Parameter(s):
 * var number - the current number of rockets
 * Return value(s): None
 ***********************************************/
function checkMusic(number) {
    if (number === 1 && hasRockets === true && done === true) {
        var tempCanvas = document.getElementById("confetti");
        var tempTotal = document.getElementById("displayTotal");
       // tempTotal.style.animationIterationCount = 0;
        //tempCanvas.style.zIndex = -1;
        song.stop();
    }

     else if(number > 1)
     {
        hasRockets = true;
     }
}

/**********************************************
 * Function: Loop
 * Description: Loops till there are no more rockets
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function loop() {
    // update screen size
    if (SCREEN_WIDTH != window.innerWidth) {
        canvas.width = SCREEN_WIDTH = window.innerWidth;
    }
    if (SCREEN_HEIGHT != window.innerHeight) {
        canvas.height = SCREEN_HEIGHT = window.innerHeight;
    }

    context.fillRect(0, 0, SCREEN_WIDTH, SCREEN_HEIGHT);
    context.clearRect(0,0, SCREEN_WIDTH, SCREEN_HEIGHT);

    var existingRockets = [];

    for (var i = 0; i < rockets.length; i++) {
        // update and render
        var object = HSVtoRGB(rockets[i].explosionColor / 360, 1, 1);
        context.fillStyle = "rgba("+ object.r + "," + object.g + ","  +object.b + "," + rockets[i].alpha +")";
        rockets[i].update();
        rockets[i].render(context);


        // calculate distance with Pythagoras
        var distance = Math.sqrt(Math.pow(mousePos.x - rockets[i].pos.x, 2) + Math.pow(mousePos.y - rockets[i].pos.y, 2));

        // random chance of 1% if rockets is above the middle
        var randomChance = rockets[i].pos.y < (SCREEN_HEIGHT * 2 / 3) ? (Math.random() * 100 <= 1) : false;

        /* Explosion rules
         - 80% of screen
         - going down
         - close to the mouse
         - 1% chance of random explosion
         */
        if (rockets[i].pos.y < SCREEN_HEIGHT / 5 || rockets[i].vel.y >= 0 || distance < 50 || randomChance) {
            rockets[i].explode();
            checkMusic(rockets.length); // Checking to see if music needs to stop
            //console.log("Explosion Coordinates (" + rockets[i].pos.x + ", " + rockets[i].pos.y + ")");
            //rockets[i].sound.play();
        } else {
            existingRockets.push(rockets[i]);
        }

    }

    rockets = existingRockets;

    var existingParticles = [];

    for (var i = 0; i < particles.length; i++) {
        particles[i].update();

        // render and save particles that can be rendered
        if (particles[i].exists()) {
            particles[i].render(context);
            existingParticles.push(particles[i]);
        }
    }

    // update array with existing particles - old particles should be garbage collected
    particles = existingParticles;

    while (particles.length > MAX_PARTICLES) {
        particles.shift();
    }
}

/**********************************************
 * Function: Particle
 * Description: Initializes the properties of the particles
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function Particle(pos) {
    this.pos = {
        x: pos ? pos.x : 0,
        y: pos ? pos.y : 0
    };
    this.vel = {
        x: 0,
        y: 0
    };
    this.shrink = .97;
    this.size = 2;

    this.resistance = 1;
    this.gravity = 0;

    this.flick = false;

    this.alpha = 1;
    this.fade = 0;
    this.color = 0;
}

/**********************************************
 * Function: Particle Update
 * Description: Changes how the particles are updated
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
Particle.prototype.update = function() {
    // apply resistance
    this.vel.x *= this.resistance;
    this.vel.y *= this.resistance;

    // gravity down
    this.vel.y += this.gravity;

    // update position based on speed
    this.pos.x += this.vel.x;
    this.pos.y += this.vel.y;

    // shrink
    this.size *= this.shrink;

    // fade out
    this.alpha -= this.fade;
};

/**********************************************
 * Function: HSV to RGB
 * Description: Converts rgb values to hsv
 * Parameter(s):
 * int h, int s, int v
 * Return value(s):
 * An object storing integer values of rgb
 ***********************************************/
function HSVtoRGB(h, s, v) {
    var r, g, b, i, f, p, q, t;
    if (arguments.length === 1) {
        s = h.s, v = h.v, h = h.h;
    }
    i = Math.floor(h * 6);
    f = h * 6 - i;
    p = v * (1 - s);
    q = v * (1 - f * s);
    t = v * (1 - (1 - f) * s);
    switch (i % 6) {
        case 0: r = v, g = t, b = p; break;
        case 1: r = q, g = v, b = p; break;
        case 2: r = p, g = v, b = t; break;
        case 3: r = p, g = q, b = v; break;
        case 4: r = t, g = p, b = v; break;
        case 5: r = v, g = p, b = q; break;
    }
    return {
        r: Math.round(r * 255),
        g: Math.round(g * 255),
        b: Math.round(b * 255)
    };
}

/**********************************************
 * Function: Particles Render
 * Description: Changes how the particles are rendered on the screen
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
Particle.prototype.render = function(c) {
    if (!this.exists()) {
        return;
    }

    c.save();

    c.globalCompositeOperation = 'lighter';

    var x = this.pos.x,
        y = this.pos.y,
        r = this.size / 2;

    var gradient = c.createRadialGradient(x, y, 0.1, x, y, r);
    gradient.addColorStop(0.1, "rgba(255,255,255," + this.alpha + ")");
    gradient.addColorStop(0.8, "hsla(" + this.color + ", 100%, 50%, " + this.alpha + ")");
    gradient.addColorStop(1, "hsla(" + this.color + ", 100%, 50%, 0.1)");

    c.fillStyle = gradient;

    c.beginPath();
    c.arc(this.pos.x, this.pos.y, this.flick ? Math.random() * this.size : this.size, 0, Math.PI * 2, true);
    c.closePath();
    c.fill();

    c.restore();
};

/**********************************************
 * Function: Particle Exists
 * Description: Decides if a particle exists or not
 * Parameter(s): None
 * Return value(s):
 * A boolean value
 ***********************************************/
Particle.prototype.exists = function() {
    return this.alpha >= 0.1 && this.size >= 1;
};

/**********************************************
 * Function: Rockets
 * Description: Initializing the rockets
 * Parameter(s): None
 * Return value(s): x
 ***********************************************/
function Rocket(x) {
    Particle.apply(this, [{
        x: x,
        y: SCREEN_HEIGHT}]);
    var temp = Math.floor(Math.random() * mySounds.length);
    this.angle = 0;
    this.color = 0;
    this.sound = new sound("fireworkSounds/" + mySounds[temp]);
    this.img = document.createElement("IMG");
    this.img.id = "img";
    this.img.setAttribute("src", "rocket.jpg");
    this.img.setAttribute("width", "75");
    this.img.setAttribute("height", "75");
    this.once = false;
    this.id = countRockets++;
    this.inverseAngle = 0;
}

/**********************************************
 * Function: Rocket Explodes
 * Description: Determines how the rocket explodes
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
Rocket.prototype.explode = function() {
    var count = Math.floor(Math.random() * 20) + 80;
    var explosionSize = Math.random() * (.98 - .92) + .92;

    for (var i = 0; i < count; i++) {
        var particle = new Particle(this.pos);
        var angle = Math.random() * Math.PI * 2;

        // emulate 3D effect by using cosine and put more particles in the middle
        var speed = Math.cos(Math.random() * Math.PI / 2) * 15;

        particle.vel.x = Math.cos(angle) * speed;
        particle.vel.y = Math.sin(angle) * speed;

        //particle.size = 10;

        // Randomizes particle size
        particle.size = Math.floor((Math.random() * particleSizeModifier) + particleSizeBase);

        particle.gravity = .2;
        //particle.resistance = 0.92;
        particle.resistance = explosionSize; // Effects the size of the explosion
        particle.shrink = Math.random() * 0.05 + 0.93;

        particle.flick = true;
        particle.color = this.explosionColor;

        particles.push(particle);
    }
};

/**********************************************
 * Function: Rocket Render
 * Description: Determine how the rocket is rendered on the screen
 * Parameter(s): c
 * Return value(s): None
 ***********************************************/
Rocket.prototype.render = function(c) {
    if (!this.exists()) {
        return;
    }
    //console.log(this.img.getAttribute("width"));
    //c.save();

    c.globalCompositeOperation = 'lighter';

    var x = this.pos.x,
        y = this.pos.y,
        r = this.size / 2;

    var gradient = c.createRadialGradient(x, y, 0.1, x, y, r);
    gradient.addColorStop(0.1, "rgba(255, 255, 255 ," + this.alpha + ")");
    gradient.addColorStop(1, "rgba(000, 0, 0, " + this.alpha + ")");

    c.fillStyle = this.explosionColor;
    c.beginPath();
    c.arc(this.pos.x, this.pos.y, this.flick ? Math.random() * this.size / 2 + this.size / 2 : this.size, 0, Math.PI * 2, true);
    c.closePath();
    c.fill();
    c.restore();

    // Step 1: Save the current context to the stack
    // Step 2: Reset the transformation matrix to identity


    // Rotate image
    //c.rotate(angle);
    //c.drawImage(this.img, this.pos.x, this.pos.y, -this.img.width / 2, -this.img.height /2);
    //c.rotate(angle);
    c.save();
    c.fillSytle = this.explosionColor;
    c.beginPath();
    c.translate(this.pos.x, this.pos.y);
    c.id = this.id;

    // The rocket angle is getting changed when a new rocket spawns on the map. Try assigning an ID to each path, so that we
    // can compare the rocket and the path to ensure we are using the same angle for the same rocket.
    // each "Rocket" should have an ID: 1, 2, 3,4. We need to compare the context here to that id. I.E c.id = this.id then we rotate c
    //good luck
    //console.log("ID: " + c.id);
    //console.log(this.angle);
    //console.log("Rocket Angle: " + this.angle);
    //console.log("Rocket " + this.id);
    //console.log("C " + c.id);

    if(c.id === this.id) {
        //c.rotate(- (this.angle + (2 * this.inverseAngle)));
        //c.rotate(-this.inverseAngle);
    }



    //console.log(this.angle);
    //c.drawImage(this.img, - this.img.width / 2, - this.img.height / 2, this.img.width, this.img.height);
    c.closePath();
    c.restore();
};