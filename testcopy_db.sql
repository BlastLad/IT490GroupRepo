
CREATE DATEBASE testdb;
use testdb;

CREATE TABLE IF NOT EXISTS BattleRooms (
    RoomID int NOT NULL AUTO_INCREMENT,
    RoomName varchar(255) DEFAULT NULL,
    Player_One int NOT NULL,
    Player_Two int DEFAULT NULL,
    VersionID int DEFAULT NULL,
    Full int DEFAULT NULL,
    BattleWinner int DEFAULT (0),
    PRIMARY KEY (RoomID)

);


CREATE TABLE IF NOT EXISTS users (
    userID int NOT NULL AUTO_INCREMENT,
    username varchar(50) DEFAULT NULL,
    password varchar(255) DEFAULT NULL,
    userEmail varchar(255) DEFAULT NULL,
    activeTeamID int DEFAULT NULL,
    PRIMARY KEY (userID)
);

CREATE TABLE IF NOT EXISTS PokemonInfo (
    UserID int NOT NULL,
    TeamID int DEFAULT NULL,
    PokemonID int DEFAULT NULL,
    PokemonName varchar(255) DEFAULT NULL,
    Move_One varchar(255) DEFAULT NULL,
    Move_Two varchar(255) DEFAULT NULL,
    Move_Three varchar(255) DEFAULT NULL,
    Move_Four varchar(255) DEFAULT NULL,
    AbilityID varchar(255) DEFAULT NULL,
    UniquePokemonID int NOT NULL AUTO_INCREMENT,
    MaxHP int DEFAULT NULL,
    PRIMARY KEY (UniquePokemonID)
);

CREATE TABLE IF NOT EXISTS TeamInfo (
    UserID int NOT NULL,
    TeamID int NOT NULL AUTO_INCREMENT,
    TeamName varchar(255) DEFAULT NULL,
    VersionID int DEFAULT NULL,
    Wins int DEFAULT NULL,
    Loses int DEFAULT NULL,
    PRIMARY KEY (UserID, TeamID)

) ENGINE = MyISAM;


CREATE TABLE IF NOT EXISTS GameState (
    RoomID int DEFAULT NULL,
    UniquePokemonID int DEFAULT NULL,
    UserID int DEFAULT NULL,
    CurrentHP int DEFAULT NULL,
    MaxHP int DEFAULT NULL,
    Fainted int DEFAULT NULL,
    Active int DEFAULT NULL,
    ActionID int DEFAULT NULL
);
