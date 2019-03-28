CREATE TABLE games (
    gameID INT(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    player1 VARCHAR(35) NOT NULL,
    player2 VARCHAR(35) NOT NULL,
    paddle1 DECIMAL(18, 16) NOT NULL,
    paddle2 DECIMAL(18, 16) NOT NULL,
    score1 INT(2) NOT NULL,
    score2 INT(2) NOT NULL
)

CREATE TABLE users (
    id INT(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(35) NOT NULL,
    email VARCHAR(256) NOT NULL,
    hashedPassword VARCHAR(256) NOT NULL,
    wins INT(6) NOT NULL,
    lastOn INT(11) NOT NULL
)

CREATE TABLE gameReq (
    timeReq INT(11) NOT NULL,
    orig INT(6) NOT NULL,
    req INT(6) NOT NULL
)

INSERT INTO gameReq (timeReq, orig, req) VALUES (100, 1, 2)

CREATE TABLE followRel (
    user1 INT(10) NOT NULL,
    user2 INT(10) NOT NULL
    req INT(11) NOT NULL
);

CREATE TABLE gamelobby (
    orig INT(6) NOT NULL PRIMARY KEY,
    req INT(6) NOT NULL
)