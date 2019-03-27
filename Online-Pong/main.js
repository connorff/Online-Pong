window.onload = function () {   
    setInterval(draw, 17);

    setInterval(function() {
        if (paddle2[1] !== storedY){
            getData(true);
        }
        else {
            storedY = paddle2[1];
            getData(false);
        }
    }, 100);
}

var xhr;

let canvas = document.getElementById("canvas")

let score = [0, 0];

canvas.width = window.innerWidth; //document.width is obsolete
canvas.height = document.body.clientHeight; //document.height is obsolete
let canvasW = canvas.width;
let canvasH = canvas.height;
let ctx = canvas.getContext("2d")

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

switch (traj){
    case 0:
        traj = [-10, -10];
        break;
    case 1:
        traj = [-10, 10];
        break;
    case 2:
        traj = [10, -10];
        break;
    case 3:
        traj = [10, 10];
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
    ctx.arc(ball[0],ball[1], 40, 0, 2 * Math.PI);
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
    if ((ball[0] + 40 >= paddle2[0] && ballOnPaddle2Y) || (ball[0] - 40 <= paddle1[0] + 40  && ballOnPaddle1Y)){
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
    if (ball[1] + 40 >= canvasH || ball[1] - 40 <= 0){
        traj[1] = -traj[1];
    }
}

function getData(send){
    if (!typeof send === 'boolean')
        send = false;
    
    if(send){
        xhr.open("GET", `data-parser.php?paddleY=${canvasH / paddle2[1]}`, true);
    }
    else {
        xhr.open("GET", `data-parser.php?game=${true}`, true);
    }

    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200){
            let json = JSON.parse(this.responseText);

            paddle1[1] = [Math.floor(canvasH / json[0].paddle2)][0];
        }
    }

    xhr.send();
}
