-- =====================================
-- BASIC SQL OPERATIONS
-- =====================================

-- Select all data
SELECT * FROM table_name;

-- Select specific column
SELECT column_name FROM table_name;

-- With condition
SELECT col1, col2 FROM table_name
WHERE condition;

-- Sorting
SELECT * FROM table_name
ORDER BY column_name ASC;

-- Grouping
SELECT column_name, COUNT(*)
FROM table_name
GROUP BY column_name;

-- =====================================
-- AGGREGATE FUNCTIONS
-- =====================================

SELECT COUNT(column_name) FROM table_name;
SELECT SUM(column_name) FROM table_name;
SELECT AVG(column_name) FROM table_name;
SELECT MIN(column_name) FROM table_name;
SELECT MAX(column_name) FROM table_name;

-- =====================================
-- JOINS & SET OPERATIONS
-- =====================================

-- INNER JOIN
SELECT *
FROM table1
INNER JOIN table2
ON table1.id = table2.id;

-- UNION
SELECT column FROM table1
UNION
SELECT column FROM table2;

-- =====================================
-- FILTERING DATA
-- =====================================

-- LIKE (pattern matching)
SELECT * FROM table_name
WHERE name LIKE 'A%';

-- BETWEEN
SELECT * FROM table_name
WHERE date BETWEEN '2020-01-01' AND '2020-12-31';

-- DISTINCT
SELECT DISTINCT column_name FROM table_name;

-- =====================================
-- TABLE & DATABASE MANAGEMENT
-- =====================================

-- Create database
CREATE DATABASE db_name;

-- Create table
CREATE TABLE table_name (
    id INT PRIMARY KEY,
    name VARCHAR(50)
);

-- Alter table
ALTER TABLE table_name
ADD column_name VARCHAR(50);

-- Drop table
DROP TABLE table_name;

-- =====================================
-- VIEWS
-- =====================================

-- Create view
CREATE VIEW view_name AS
SELECT column_name FROM table_name;

-- Select from view
SELECT * FROM view_name;

-- Drop view
DROP VIEW view_name;

-- =====================================
-- CONSTRAINTS
-- =====================================

-- Primary Key
CREATE TABLE example (
    id INT PRIMARY KEY
);

-- Foreign Key
CREATE TABLE orders (
    id INT,
    customer_id INT,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- =====================================
-- NULL HANDLING
-- =====================================

SELECT IFNULL(column_name, 0) FROM table_name;

-- =====================================
-- SUBQUERY
-- =====================================

SELECT name FROM customers
WHERE EXISTS (
    SELECT * FROM orders
    WHERE customers.id = orders.id
);
