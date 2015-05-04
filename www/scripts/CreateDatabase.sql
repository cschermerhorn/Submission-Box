drop table Student;
drop table Course;
drop table Enrollment;
drop table Assignment;
drop table Submission;

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
  primary key (AssignmentID),
  foreign key (CourseID) references Course (CourseID));

create table Submission(
  StudentID int not null,
  AssignmentID int not null,
  SubmissionDate DATETIME not null,
  Grade DECIMAL(5,2),
  Comments varchar(4000),
  PRIMARY KEY (StudentID, AssignmentID),
  FOREIGN KEY (StudentID) REFERENCES Student(StudentID),
  FOREIGN KEY (AssignmentID) REFERENCES Assignment(AssignmentID));

#Alter by SB3.  Add strict filetype restrictions to Assignment table
#exclude the . when adding -> i.e. txt,pdf
alter table Assignment add FileTypes VARCHAR(512);
