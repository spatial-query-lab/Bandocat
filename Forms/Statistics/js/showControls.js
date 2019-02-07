/**
 * Created by hreeves on 6/26/2018.
 */
// WARNING: Be very careful on naming your variables, if
// one of the files has a global variable of the same name,
// you will get errors!!!!
/**********************************************
 * Function: Document Ready
 * Description: Function runs whenever the document is opened
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
$(document).ready(function() {
    initializeFireworks();
    var displayTotal = document.getElementById("displayTotal");
    var tempCanvasConfetti = document.getElementById("confetti");
    displayTotal.style.animationIterationCount = 100;
    //document.getElementById("clap").currentTime = document.getElementById("clap").duration;

    // On mousedown, this includes left and right clicks
    document.body.onmousedown = function(e) {
        stopFireworks(false);
        clearConfettiCanvas();
    }

    document.body.addEventListener("mousewheel", MouseWheelHandler, false);

    document.body.onkeydown = function(e) {
        switch(e.keyCode){
            // Shift key
            case 16:
                displayTotal.style.animationIterationCount = 100;
                startFireWorks();
                canvas.style.zIndex = 2;
                song.play();
                document.getElementById("confetti").style.zIndex = 1;
                break;

            // Escape key
            case 27:
                stopFireworks(false);
                clearConfettiCanvas();
                song.stop();
                displayTotal.style.animationIterationCount = 0;
                break;

            // Space bar
            case 32:
                stopFireworks(false);
                clearConfettiCanvas();
                song.stop();
                displayTotal.style.animationIterationCount = 0;
                break;

            // 0 key, not on num pad
            case 48:
                launchMoreFireworks();
                break;

            // 1 key, not on the num pad
            case 49:
                playClapping();
                break;

            default:
                break;
        }
    };
});

/**********************************************
 * Function: Mouse Wheel Handler
 * Description: Function runs whenever the mouse wheel moves
 * Parameter(s):
 * Event e - When a mouse wheel event has occurred
 * Return value(s): None
 ***********************************************/
function MouseWheelHandler(e) {
    stopFireworks(false);
    clearConfettiCanvas();
    song.stop();
}

/**********************************************
 * Function: Play Clapping
 * Description: Plays clapping audio, but prevents spamming it
 * Parameter(s): None
 * Return value(s): None
 ***********************************************/
function playClapping() {
    var clapControl = document.getElementById("clap");

    // Prevents clap spamming
    if(clapControl.paused)
    {
        clapControl.currentTime = 0;
        clapControl.play();
    }
}

