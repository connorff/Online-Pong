CREATE TABLE games (
    gameID INT(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    player1 VARCHAR(35) NOT NULL,
    player2 VARCHAR(35) NOT NULL,
    paddle1X INT(4) NOT NULL,
    paddle1Y INT(4) NOT NULL,
    paddle2X INT(4) NOT NULL,
    paddle2Y INT(4) NOT NULL
    score1 INT(2) NOT NULL,
    score2 INT(2) NOT NULL
)

CREATE TABLE users (
    id INT(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(35) NOT NULL,
    email VARCHAR(256) NOT NULL,
    hashedPassword VARCHAR(256) NOT NULL
)

CREATE TABLE follow (
    username VARCHAR(35) NOT NULL,
    follow VARCHAR(35) NOT NULL
)

CREATE TABLE gameReq (
    timeReq INT(11) NOT NULL,
    orig INT(6) NOT NULL,
    req INT(6) NOT NULL
)

INSERT INTO gameReq (timeReq, orig, req) VALUES (100, 1, 2)