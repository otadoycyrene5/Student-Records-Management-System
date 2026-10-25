# Student Records Management System

A simple web-based Student Records Management System developed using **PHP and MySQL**. This project demonstrates CRUD (Create, Read, Update, Delete) operations and basic relational database management.

Developed as an academic project to practice database integration and web development fundamentals.

## Project Overview

The system allows users to add, view, update, delete, and search student records through a simple web interface connected to a MySQL database.

## Features

- **Create** — Add new student records.
- **Read** — Display stored student information.
- **Update** — Edit existing student details.
- **Delete** — Remove student records.
- **Search** — Find specific student records.

## Technologies Used

| Category                | Technologies          |
| ----------------------- | --------------------- |
| Frontend                | HTML, CSS, JavaScript |
| Backend                 | PHP                   |
| Database                | MySQL                 |
| Development Environment | XAMPP                 |
| Database Management     | phpMyAdmin            |

## Database Concepts

- SQL Queries
- CRUD Operations
- Primary Keys
- Data Manipulation
- Database Connectivity
- Data Management

## Project Structure

```arduino
student-records/
├── backend/
│   ├── config.php
│   ├── create.php
│   ├── read.php
│   ├── update.php
│   └── delete.php
├── database/
│   └── database.sql
├── frontend/
│   ├── css/
│   ├── js/
│   └── *.html
├── index.php
└── README.md
```

*The frontend tree is illustrative; keep your actual filenames and folders.*

## Installation

1. Install [XAMPP](https://www.apachefriends.org/).
2. Download or clone this repository.
3. Place the `student-records` folder inside `C:\xampp\htdocs`.
4. Start **Apache** and **MySQL** in XAMPP.
5. Open `http://localhost/phpmyadmin`.
6. Create the database and import the SQL file from the `database` folder.
7. Configure your database connection in `backend/config.php`.
8. Open the application:

   `http://localhost/student-records/`

## Learning Outcomes

Through this project, I gained hands-on experience in writing SQL queries, performing CRUD operations, connecting PHP applications to MySQL, and managing student information using a database-driven system.

## Disclaimer

This project was developed for educational purposes and is intended to demonstrate basic database management and web development concepts.
