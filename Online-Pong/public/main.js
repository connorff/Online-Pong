//let canvas = document.getElementById("canvas")
let ctx = canvas.getContext("2d")
let ballRad = null;
let user;

sizeCanvas();
startGame();

function startGame() {
    setInterval(draw, 10);

    setInterval(function() {
        if (paddle2[1] !== storedY){
            storedY = paddle2[1];
            getData(true);
        }
        else {
            storedY = paddle2[1];
            getData(false);
        }
    }, 100);
}

var xhr;

let score = [0, 0];

//sizes the canvas to the appropriate dimensions (16:10 aspect ratio)
function sizeCanvas() {
    let width = window.innerWidth;
    let height = document.body.clientHeight;

    //code if width is too wide
    if ((width / height) > (8/5)){
        //gets a nice, even number for the height in order to make things universal
        canvas.height = Math.floor(height / 10) * 10;

        canvas.width = (8/5) * Math.floor(height / 10) * 10;
    }
    //code for if the height is too high
    else if ((width / height) > (8/5)){
        canvas.width = Math.floor(width / 10) * 10;

        canvas.height = (9/16) * Math.floor(width / 10) * 10;
    }
    //if all else fails, it must be perfectly 16:10
    else {
        canvas.width = width;
        canvas.height = height;
    }

    //sizes the ball to the perfect size
    ballRad = canvas.width / 25.2;
}

let canvasW = canvas.width;
let canvasH = canvas.height;

//sets position of the ball
let ball = [canvasW / 2, canvasH / 2];

//set size of paddle (height, width)
let paddleSize = [canvasH / 3, canvasW / 20]

//sets the position of the paddles (x, y)
let paddle1 = [canvasW / 50, canvasH / 3];
let paddle2 = [canvasW * (48/50), canvasH / 3];

//sets the trajectory of the ball
let traj = Math.floor(Math.random() * 3);

//creates a variable to store the y value from last refresh
var storedY = paddle2[1];

if (window.XMLHttpRequest){
    xhr = new XMLHttpRequest();
}
else {
    xhr = new ActiveXObject("Microsoft.XMLHTTP")
}

let optimalTraj = window.innerWidth / 220;

switch (traj){
    case 0:
        traj = [-optimalTraj, -optimalTraj];
        break;
    case 1:
        traj = [-optimalTraj, optimalTraj];
        break;
    case 2:
        traj = [optimalTraj, -optimalTraj];
        break;
    case 3:
        traj = [optimalTraj, optimalTraj];
        break;
}

//manages keyboard events
document.onkeydown = function (e) {
    switch(e.keyCode) {
        //when up arrow is pressed
        case 38:
            paddle2[1] -= 30;
            break;
        
        //when down arrow is pressed
        case 40:
            paddle2[1] += 30;
            break;
    }
}

function draw() {
    //fills screen black
    ctx.fillStyle = "black";
    ctx.fillRect(0, 0, canvasW, canvasH);
    
    //writes in score
    ctx.fillStyle = "white";
    ctx.font = "30px Arial";
    ctx.fillText(`${score[0]} - ${score[1]}`, canvasW / 2, 50);    
    
    //creates ball
    ctx.beginPath();
    ctx.arc(ball[0],ball[1], ballRad, 0, 2 * Math.PI);
    ctx.stroke();
    ctx.fill();
    
    //creates paddles
    ctx.fillRect(paddle1[0], paddle1[1], 20, paddleSize[0], paddleSize[1])
    ctx.fillRect(paddle2[0], paddle2[1], 20, paddleSize[0], paddleSize[1])
    
    //change values as needed
    ball[0] += traj[0];
    ball[1] += traj[1];
    
    //checks if ball is hitting paddles 1 or 2
    //checks if it is above the bottom and then below the top
    let ballOnPaddle2Y = (ball[1] >= paddle2[1]) && (ball[1] <= paddle2[1] + paddleSize[0]);
    let ballOnPaddle1Y = (ball[1] >= paddle1[1]) && (ball[1] <= paddle1[1] + paddleSize[0]);
    

    //checks if ball 
    if ((ball[0] + ballRad >= paddle2[0] && ballOnPaddle2Y) || (ball[0] - ballRad <= paddle1[0] + ballRad  && ballOnPaddle1Y)){
        traj[0] = -traj[0];
    }
    else {
        if (ball[0] > paddle2[0]){
            score[1]++;
            ball = [canvasW / 2, canvasH / 2];
        }
        else if (ball[0] < 0){
            score[0]++;
            ball = [canvasW / 2, canvasH / 2];
        }
    }
    if (ball[1] + ballRad >= canvasH || ball[1] - ballRad <= 0){
        traj[1] = -traj[1];
    }
}

function getData(send){
    if (!typeof send === 'boolean')
        send = false;
    
    if(send){
        xhr.open("GET", `data-parser.php?paddleY=${canvasH / paddle2[1]}&player=2`, true);
    }
    else {
        xhr.open("GET", `data-parser.php?game=${true}&player=2`, true);
    }

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200){
            let json = JSON.parse(this.responseText);

            paddle1[1] = [Math.floor(canvasH / json[0].paddle1)][0];
        }
    }

    xhr.send();
}
