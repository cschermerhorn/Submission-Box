-- Riam
alter table assignment modify DueDate DATETIME;

create table Submission(
StudentId VARCHAR(20) not null,
AssignmentID int not null,
SubmissionDate DATETIME not null,
Grade DECIMAL(5,2),
Comments varchar(4000),

  PRIMARY KEY (StudentId, AssignmentID),
  FOREIGN KEY (StudentId) REFERENCES student(StudentID),
  FOREIGN KEY  (AssignmentID) REFERENCES assignment(AssignmentID)
);

-- Reem
alter table assignment add FileTypes VARCHAR(512);
