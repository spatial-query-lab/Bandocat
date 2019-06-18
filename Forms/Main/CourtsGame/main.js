$(document).ready(() => {
    main();
});

/**************************************/
// Global Variables
let speed = 1;

let myGameArea = {
    canvas: document.createElement("canvas"),
    start: function () {
        this.canvas.width = $('#main').width();
        this.canvas.height = $('#main').height() - 100;
        this.canvas.classList.add("border");
        this.canvas.classList.add("border-dark");
        this.canvas.id = "gameCanvas";
        this.context = this.canvas.getContext("2d");
        $('#main').append(this.canvas);

        // Frames and intervals
        this.frameNo = 0;
        this.interval = setInterval(updateGameArea, 20);

        // Event Listeners
        window.addEventListener('keydown', function (e) {
            myGameArea.keys = (myGameArea.keys || []);
            myGameArea.keys[e.keyCode] = true;
        })
        window.addEventListener('keyup', function (e) {
            myGameArea.keys[e.keyCode] = false;
        })
    },
    clear: function () {
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.context.clear
    },
    stop: function () {
        clearInterval(this.interval);
    }
}

let player;
let bullets = [];

const playerWidth = 50,
    playerHeight = 50;

function obstacle(width, height, color, x, y, gravity, gravitySpeed, direction) {
    this.width = width;
    this.height = height;
    this.gravity = gravity;
    this.gravitySpeed = gravitySpeed;
    this.speedX = 0;
    this.speedY = 0;
    this.x = x;
    this.y = y;
    this.bounce = 1;
    this.direction = direction;
    this.update = function () {
        ctx = myGameArea.context;
        ctx.fillStyle = color;
        ctx.fillRect(this.x, this.y, this.width, this.height);
    }
    this.newPos = function () {
        this.gravitySpeed += this.gravity;

        // Right
        if (direction == 1) {
            this.x += this.speedX + randomNumber(1, 2);
        } else {
            this.x += this.speedX - randomNumber(1, 2);
        }

        this.y += this.speedY + this.gravitySpeed;
        this.hitBottom();
    }
    this.hitBottom = function () {
        let rockbottom = myGameArea.canvas.height - this.height;
        this.direction = randomNumber(0, 1);
        if (this.y > rockbottom) {
            this.y = rockbottom;
            this.gravitySpeed = -(this.gravitySpeed * this.bounce);
        }
    }
}

let obstacles = [];

function bullet(width, height, color, x, y, damage) {
    this.width = width;
    this.height = height;
    this.speedX = 0;
    this.speedY = 0;
    this.x = x;
    this.y = y;
    this.damage = damage;
    this.update = function () {
        ctx = myGameArea.context;
        ctx.fillStyle = color;
        ctx.fillRect(this.x, this.y, this.width, this.height);
    }
    this.delete = function()
    {
        myGameArea.context.clearRect(this.x, this.y, this.width, this.height);
    }
    this.newPos = function () {
        this.x += this.speedX;
        this.y += this.speedY;
    }
    this.movement = function () {
        this.speedX = 0;
        this.speedY = 0;

        this.speedY = -10;

        for(let i = 0; i < obstacles.length; i++)
        {
            if(this.hit(obstacles[i]))
            {
                obstacles[i].width -= this.damage;
                obstacles[i].height -= this.damage;
                this.delete();
                return false;
            }
        }

        this.newPos();
        this.update();
        return true;
    }
    this.shooting = function() {
        ctx = myGameArea.context;
        ctx.fillStyle = randomColor();
        ctx.fillRect(this.x, this.y - 30, 5, 10);
    }
    this.hit = function (otherobj) {
        var myleft = this.x;
        var myright = this.x + (this.width);
        var mytop = this.y;
        var mybottom = this.y + (this.height);
        var otherleft = otherobj.x;
        var otherright = otherobj.x + (otherobj.width);
        var othertop = otherobj.y;
        var otherbottom = otherobj.y + (otherobj.height);
        var crash = true;
        
        if ((mybottom < othertop) ||
            (mytop > otherbottom) ||
            (myright < otherleft) ||
            (myleft > otherright)) {
            crash = false;
        }
        return crash;
    }
}

function component(width, height, color, x, y, fireRate) {
    this.width = width;
    this.height = height;
    this.speedX = 0;
    this.speedY = 0;
    this.x = x;
    this.y = y;
    this.fireRate = fireRate;
    this.update = function () {
        ctx = myGameArea.context;
        ctx.fillStyle = color;
        ctx.fillRect(this.x, this.y, this.width, this.height);
    }
    this.newPos = function () {
        this.x += this.speedX;
        this.y += this.speedY;
    }
    this.movement = function () {
        this.speedX = 0;
        this.speedY = 0;

        // A key
        if (myGameArea.keys && myGameArea.keys[65]) {
            this.speedX = -10;
        }

        // D key
        if (myGameArea.keys && myGameArea.keys[68]) {
            this.speedX = 10;
        }

        // Spacebar, Shootings
        if (myGameArea.keys && myGameArea.keys[32]) {
            if(myGameArea.frameNo % this.fireRate === 0)
            {
                bullets.push(new bullet(5, 10, "DodgerBlue", this.x + this.width / 2, this.y - 30, 5));
            }
        }

        this.newPos();
        this.update();
    }
    this.shooting = function() {
        ctx = myGameArea.context;
        ctx.fillStyle = randomColor();
        ctx.fillRect(this.x, this.y - 30, 5, 10);
    }
    this.crashWith = function (otherobj) {
        var myleft = this.x;
        var myright = this.x + (this.width);
        var mytop = this.y;
        var mybottom = this.y + (this.height);
        var otherleft = otherobj.x;
        var otherright = otherobj.x + (otherobj.width);
        var othertop = otherobj.y;
        var otherbottom = otherobj.y + (otherobj.height);
        var crash = true;
        return false;
        if ((mybottom < othertop) ||
            (mytop > otherbottom) ||
            (myright < otherleft) ||
            (myleft > otherright)) {
            crash = false;
        }
        return crash;
    }
}
/**************************************/

function main() {
    // Initialize the canvas
    myGameArea.start();
    player = new component(playerWidth, playerHeight, "blue", $('#gameCanvas').width() / 2 - (playerHeight / 2), $('#gameCanvas').height() - playerWidth, 5);

    for (let i = 0; i < 10; i++) {
        obstacles.push(new obstacle(100, 100, randomColor(), (Math.random() * $('#gameCanvas').width() - 50) + 1, 0, 0.5, 0, randomNumber(0, 1)));
        obstacles[i].update();
    }


    player.update();
}

function updateGameArea() {
    for (let i = 0; i < obstacles.length; i++) {
        // Moving obstacles
        if(player.crashWith(obstacles[i]))
        {
            myGameArea.stop();
            return;
        }
    }

    myGameArea.clear();
    myGameArea.frameNo += 1;

    for (let i = 0; i < obstacles.length; i++) {
        // Moving obstacles
        obstacles[i].newPos();
        obstacles[i].update();
    }

    // Moving Bullets
    for(let i = 0; i < bullets.length; i++)
    {
        if(bullets[i].movement() === false)
        {
            bullets.remove(i);
        }
    }
    // Player Movement
    player.movement();
}

function randomNumber(min, max) {
    let result = Math.random() * (max - min) + min;

    if (result < 0.5) {
        return Math.floor(result);
    } else {
        return Math.ceil(result);
    }
}

function randomColor() {
    return '#' + (0x1000000 + (Math.random()) * 0xffffff).toString(16).substr(1, 6);
}