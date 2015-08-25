drop table if exists Submission;
drop table if exists Assignment;
drop table if exists Enrollment;
drop table if exists Student;
drop table if exists Course;

create table Student(
  StudentID varchar(20) not null,
  Password varchar(32) not null,
  primary key (StudentID));

create table Course(
  CourseID varchar(20) not null,
  primary key (CourseID));

create table Enrollment(
  StudentID varchar(20) not null,
  CourseID varchar(20) not null,
  foreign key (StudentID) references Student (StudentID),
  foreign key (CourseID) references Course (CourseID));

create table Assignment(
  AssignmentID int not null auto_increment,
  gFlag char(1) default 0,
  Name varchar(30) not null,
  CourseID varchar(20) not null,
  DueDate date not null,
  FileTypes VARCHAR(512),
  primary key (AssignmentID),
  foreign key (CourseID) references Course (CourseID));

create table Submission(
  StudentID VARCHAR(20) not null,
  AssignmentID int not null,
  SubmissionDate DATETIME not null,
  Grade DECIMAL(5,2),
  Comments varchar(4000),
  PRIMARY KEY (StudentID, AssignmentID),
  FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
  FOREIGN KEY (AssignmentID) REFERENCES Assignment(AssignmentID));
