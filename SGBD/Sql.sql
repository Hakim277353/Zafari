CREATE TABLE User (
  UserID NUMBER(10) PRIMARY KEY,
  FirstName VARCHAR2(50),
  LastName VARCHAR2(50),
  Email VARCHAR2(50),
  Password VARCHAR2(50),
  PhoneNumber NUMBER(8)
);



CREATE TABLE Comment (
  CommentID NUMBER(10) PRIMARY KEY,
  UserID NUMBER(10) REFERENCES User(UserID),
  CommentText VARCHAR2(4000),
  CommentTime TIMESTAMP
);