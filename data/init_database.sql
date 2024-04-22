CREATE DATABASE bd_course;
USE bd_course;

CREATE TABLE department
(
    department_id INT UNSIGNED AUTO_INCREMENT,
    city          VARCHAR(100) NOT NULL,
    address       VARCHAR(100) NOT NULL,
    PRIMARY KEY (department_id)
)
;

CREATE TABLE worker
(
    worker_id       INT UNSIGNED AUTO_INCREMENT,
    full_name     VARCHAR(255) NOT NULL,
    job_title     VARCHAR(100) NOT NULL,
    phone         VARCHAR(30)  NOT NULL,
    email         VARCHAR(255) NOT NULL,
    gender        TINYINT      NOT NULL,
    birth_date    DATETIME     NOT NULL,
    hire_date     DATETIME     NOT NULL,
    description   VARCHAR(500),
    department_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (worker_id),
    CONSTRAINT worker_department_fkey
        FOREIGN KEY (department_id)
            REFERENCES department (department_id)
)
;