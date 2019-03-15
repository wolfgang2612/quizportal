/*
drop database if exists quiz;
create database quiz;
use quiz;

drop table if exists student;
create table student(
id varchar(10) not null primary key,
name varchar(20),
email varchar(50),
password varchar(128)
);

drop table if exists faculty;
create table faculty(
id varchar(10) not null primary key,
name varchar(20),
email varchar(50),
password varchar(128)
);

drop table if exists course;
create table course(
id varchar(10) not null primary key,
faculty_id varchar(10),
name varchar(20),
foreign key (faculty_id) references faculty(id)
);

drop table if exists enrolled;
create table enrolled(
student_id varchar(10),
course_id varchar(10),
foreign key (student_id) references student(id),
foreign key (course_id) references course(id)
);

drop table if exists quizzes;
create table quizzes(
id integer auto_increment primary key not null,
course_id varchar(10),
quiztime datetime,
venue text,
duration integer,
foreign key (course_id) references course(id)
);

drop table if exists questions;
create table questions(
id integer auto_increment primary key,
quiz_id integer,
body text,
foreign key (quiz_id) references quizzes(id)
);

drop table if exists options;
create table options(
id integer primary key not null,
question_id integer,
body text,
foreign key (question_id) references questions(id)
);

drop table if exists submitted;
create table submitted(
student_id varchar(10),
question_id integer,
foreign key (student_id) references student(id),
foreign key (question_id) references questions(id)
);

drop table if exists qsubmit;
create table qsubmit(
student_id varchar(10),
quiz_id integer,
foreign key (student_id) references student(id),
foreign key (quiz_id) references quizzes(id)
);
*/