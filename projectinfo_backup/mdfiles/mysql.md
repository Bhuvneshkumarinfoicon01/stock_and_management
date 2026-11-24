//
Great — let’s make this practical. I’ll give you **real interview-style problems** on window functions, starting from easy → advanced, with explanations.

---

# 🟢 1. Top N per Group (Very Common Interview Question)

### ❓ Problem

Find the **highest-paid employee in each department**.

### ✅ Solution

```sql
SELECT name, department, salary
FROM (
    SELECT *,
           RANK() OVER (PARTITION BY department ORDER BY salary DESC) AS rnk
    FROM employees
) t
WHERE rnk = 1;
```

### 💡 Why this works

* `PARTITION BY department` → separates departments
* `ORDER BY salary DESC` → highest salary first
* `RANK()` → assigns rank within each department

---

# 🟢 2. Second Highest Salary (Classic)

### ❓ Problem

Find the **second highest salary overall**.

### ✅ Solution

```sql
SELECT salary
FROM (
    SELECT salary,
           DENSE_RANK() OVER (ORDER BY salary DESC) AS rnk
    FROM employees
) t
WHERE rnk = 2;
```

### ⚠️ Why `DENSE_RANK()`?

* Handles duplicate salaries correctly
* `RANK()` would skip numbers

---

# 🟡 3. Running Total (Very Important)

### ❓ Problem

Calculate cumulative salary ordered by employee ID.

### ✅ Solution

```sql
SELECT id, name, salary,
       SUM(salary) OVER (ORDER BY id) AS running_total
FROM employees;
```

### 💡 Use case

* Financial reports
* Sales dashboards

---

# 🟡 4. Find Duplicate Records

### ❓ Problem

Find duplicate employees based on name.

### ✅ Solution

```sql
SELECT *
FROM (
    SELECT *,
           COUNT(*) OVER (PARTITION BY name) AS cnt
    FROM employees
) t
WHERE cnt > 1;
```

---

# 🟡 5. Compare with Previous Row

### ❓ Problem

Find employees whose salary is higher than the previous employee.

### ✅ Solution

```sql
SELECT name, salary
FROM (
    SELECT *,
           LAG(salary) OVER (ORDER BY id) AS prev_salary
    FROM employees
) t
WHERE salary > prev_salary;
```

---

# 🔵 6. Moving Average (Moderate)

### ❓ Problem

Calculate **average salary of current + previous row**.

### ✅ Solution

```sql
SELECT name, salary,
       AVG(salary) OVER (
           ORDER BY id
           ROWS BETWEEN 1 PRECEDING AND CURRENT ROW
       ) AS moving_avg
FROM employees;
```

---

# 🔵 7. Gap Between Salaries

### ❓ Problem

Find difference between current and next employee salary.

### ✅ Solution

```sql
SELECT name, salary,
       LEAD(salary) OVER (ORDER BY id) - salary AS diff_next
FROM employees;
```

---

# 🔴 8. Top 3 Salaries per Department (Advanced)

### ❓ Problem

Get **top 3 highest salaries in each department**.

### ✅ Solution

```sql
SELECT *
FROM (
    SELECT *,
           DENSE_RANK() OVER (PARTITION BY department ORDER BY salary DESC) AS rnk
    FROM employees
) t
WHERE rnk <= 3;
```

---

# 🔴 9. First Row per Group

### ❓ Problem

Get the **first employee (by ID) in each department**.

### ✅ Solution

```sql
SELECT *
FROM (
    SELECT *,
           ROW_NUMBER() OVER (PARTITION BY department ORDER BY id) AS rn
    FROM employees
) t
WHERE rn = 1;
```

---

# 🔴 10. Percent Rank / Analytics (Advanced)

### ❓ Problem

Find relative salary ranking (0 to 1 scale).

### ✅ Solution

```sql
SELECT name, salary,
       PERCENT_RANK() OVER (ORDER BY salary) AS pct_rank
FROM employees;
```

---

# 🧠 Interview Tips (Important)

* Use:

  * `ROW_NUMBER()` → unique numbering
  * `RANK()` → skips numbers on ties
  * `DENSE_RANK()` → no gaps

* Most asked patterns:

  * Top N per group ✅
  * Running totals ✅
  * Previous/next row comparison ✅

---

# 🚀 Practice Challenge (Try Yourself)

### ❓ Question:

Find employees whose salary is **greater than the average salary of their department**.

👉 Try before seeing solution.

---

### ✅ Solution:

```sql
SELECT name, department, salary
FROM (
    SELECT *,
           AVG(salary) OVER (PARTITION BY department) AS dept_avg
    FROM employees
) t
WHERE salary > dept_avg;
```

---

If you want, I can:

* give you **real company interview questions (Amazon, Google-level)**
* or create a **step-by-step practice roadmap** to master SQL window functions fast

===============================================================================================================================
Good idea—learning window functions is much easier with a ready dataset. Here’s a **complete MySQL script** you can copy-paste and run directly.

---

# 🧱 1. Create Database & Table

```sql id="1qg7fx"
-- Create database
CREATE DATABASE window_practice;

-- Use it
USE window_practice;

-- Create table
CREATE TABLE employees (
    id INT PRIMARY KEY,
    name VARCHAR(50),
    department VARCHAR(50),
    salary INT,
    joining_date DATE
);
```

---

# 📥 2. Insert Sample Data

```sql id="k2z1o5"
INSERT INTO employees (id, name, department, salary, joining_date) VALUES
(1, 'Amit', 'IT', 5000, '2022-01-10'),
(2, 'Neha', 'IT', 6000, '2022-02-15'),
(3, 'Raj', 'IT', 6000, '2022-03-01'),
(4, 'Simran', 'HR', 4000, '2022-01-20'),
(5, 'Karan', 'HR', 4500, '2022-02-25'),
(6, 'Pooja', 'HR', 4500, '2022-03-10'),
(7, 'Vikas', 'Finance', 7000, '2022-01-05'),
(8, 'Anjali', 'Finance', 8000, '2022-02-18'),
(9, 'Rohit', 'Finance', 7500, '2022-03-22'),
(10, 'Meena', 'IT', 5500, '2022-04-01');
```

---

# 🔍 3. Test Data

```sql id="hl1i1y"
SELECT * FROM employees;
```

---

# ⚡ 4. Ready-to-Run Window Function Examples

## ✅ Ranking in Department

```sql id="4g7sk7"
SELECT name, department, salary,
       RANK() OVER (PARTITION BY department ORDER BY salary DESC) AS rank_in_dept
FROM employees;
```

---

## ✅ Running Total

```sql id="7p4qf2"
SELECT id, name, salary,
       SUM(salary) OVER (ORDER BY id) AS running_total
FROM employees;
```

---

## ✅ Previous Salary (LAG)

```sql id="y8y2nx"
SELECT name, salary,
       LAG(salary) OVER (ORDER BY id) AS prev_salary
FROM employees;
```

---

## ✅ Top 2 per Department

```sql id="l9r4t6"
SELECT *
FROM (
    SELECT *,
           DENSE_RANK() OVER (PARTITION BY department ORDER BY salary DESC) AS rnk
    FROM employees
) t
WHERE rnk <= 2;
```

---

## ✅ Department Average Comparison

```sql id="e5m3yq"
SELECT name, department, salary
FROM (
    SELECT *,
           AVG(salary) OVER (PARTITION BY department) AS dept_avg
    FROM employees
) t
WHERE salary > dept_avg;
```

---

# 🎯 Optional: Add More Realism

If you want richer practice, you can add another table:

```sql id="qz5q3z"
CREATE TABLE orders (
    order_id INT PRIMARY KEY,
    customer_name VARCHAR(50),
    amount INT,
    order_date DATE
);

INSERT INTO orders VALUES
(1, 'Amit', 200, '2023-01-01'),
(2, 'Amit', 300, '2023-01-05'),
(3, 'Neha', 150, '2023-01-02'),
(4, 'Neha', 400, '2023-01-07'),
(5, 'Raj', 500, '2023-01-03');
```

👉 Then try:

* Running total per customer
* Rank orders by amount

---

# 🚀 How to Use

* Open MySQL Workbench or any SQL tool
* Paste the script
* Run everything step-by-step

---

If you want next level:

* I can give you a **mini project (like a real company dataset)**
* Or **50+ structured practice questions** from beginner → advanced

===============================================================================================================================
Nice—let’s turn this into something that actually feels like real work, not just textbook queries.

---

# 🧩 Mini SQL Project: **E-Commerce Analytics System**

You’ll simulate a small company like Amazon or Flipkart and answer business questions using **window functions**.

---

# 🧱 1. Database Setup

```sql id="db1"
CREATE DATABASE ecommerce_project;
USE ecommerce_project;
```

---

# 📊 2. Tables

## Customers

```sql id="db2"
CREATE TABLE customers (
    customer_id INT PRIMARY KEY,
    name VARCHAR(50),
    city VARCHAR(50)
);
```

## Orders

```sql id="db3"
CREATE TABLE orders (
    order_id INT PRIMARY KEY,
    customer_id INT,
    order_date DATE,
    amount INT,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);
```

---

# 📥 3. Sample Data

## Customers

```sql id="db4"
INSERT INTO customers VALUES
(1, 'Amit', 'Delhi'),
(2, 'Neha', 'Mumbai'),
(3, 'Raj', 'Bangalore'),
(4, 'Simran', 'Delhi'),
(5, 'Karan', 'Mumbai');
```

## Orders

```sql id="db5"
INSERT INTO orders VALUES
(101, 1, '2023-01-01', 200),
(102, 1, '2023-01-05', 500),
(103, 2, '2023-01-02', 300),
(104, 2, '2023-01-10', 700),
(105, 3, '2023-01-03', 400),
(106, 3, '2023-01-08', 600),
(107, 4, '2023-01-04', 250),
(108, 4, '2023-01-09', 450),
(109, 5, '2023-01-06', 350),
(110, 5, '2023-01-11', 800);
```

---

# 🎯 3. Business Problems (Real Interview Style)

---

## 🟢 Q1. Running Total per Customer

👉 “How much has each customer spent over time?”

```sql id="q1"
SELECT customer_id, order_date, amount,
       SUM(amount) OVER (
           PARTITION BY customer_id
           ORDER BY order_date
       ) AS running_total
FROM orders;
```

---

## 🟢 Q2. Rank Orders by Amount per Customer

```sql id="q2"
SELECT *,
       RANK() OVER (
           PARTITION BY customer_id
           ORDER BY amount DESC
       ) AS rank_order
FROM orders;
```

---

## 🟡 Q3. First Order of Each Customer

```sql id="q3"
SELECT *
FROM (
    SELECT *,
           ROW_NUMBER() OVER (
               PARTITION BY customer_id
               ORDER BY order_date
           ) AS rn
    FROM orders
) t
WHERE rn = 1;
```

---

## 🟡 Q4. Compare Current Order with Previous

```sql id="q4"
SELECT *,
       LAG(amount) OVER (
           PARTITION BY customer_id
           ORDER BY order_date
       ) AS prev_amount
FROM orders;
```

---

## 🔵 Q5. Top 2 Customers by Total Spending

```sql id="q5"
SELECT *
FROM (
    SELECT customer_id,
           SUM(amount) AS total_spent,
           DENSE_RANK() OVER (ORDER BY SUM(amount) DESC) AS rnk
    FROM orders
    GROUP BY customer_id
) t
WHERE rnk <= 2;
```

---

## 🔵 Q6. Daily Sales with Running Total

```sql id="q6"
SELECT order_date,
       SUM(amount) AS daily_sales,
       SUM(SUM(amount)) OVER (ORDER BY order_date) AS running_sales
FROM orders
GROUP BY order_date;
```

---

## 🔴 Q7. Customers Spending More Than Their Average

```sql id="q7"
SELECT *
FROM (
    SELECT *,
           AVG(amount) OVER (PARTITION BY customer_id) AS avg_spent
    FROM orders
) t
WHERE amount > avg_spent;
```

---

## 🔴 Q8. Find Growth (Next Order Difference)

```sql id="q8"
SELECT *,
       LEAD(amount) OVER (
           PARTITION BY customer_id
           ORDER BY order_date
       ) - amount AS next_diff
FROM orders;
```

---

# 🧠 What You’ll Learn

By completing this project, you’ll understand:

* Real analytics queries used in companies
* How dashboards calculate metrics
* How to think like a data analyst

---

# 🚀 Next Step (If You Want)

I can level this up into a **real portfolio project**:

* Add products table
* Add categories
* Build KPI dashboard queries
* Simulate interview round (like real companies)

Just tell me:
👉 *“make it advanced”* or *“give interview mock”*

===============================================================================================================================
If MySQL says:

```
Query OK, 1 row affected
Database changed
```

then the database **definitely exists**. If it’s not showing in Adminer, the issue is almost always on the Adminer side—not MySQL.

Here are the common reasons and fixes:

---

### 🔄 1. Adminer caching / refresh issue

Adminer doesn’t always auto-refresh.

👉 Try:

* Reload the Adminer page (Ctrl + F5)
* Or log out and log back in

---

### 👤 2. Different MySQL user

You might be logged into MySQL via terminal as `root`, but Adminer could be using another user.

👉 Check:

```sql
SELECT CURRENT_USER();
```

If Adminer uses a different user, that user may not have permission to see `cmddata`.

👉 Fix:

```sql
GRANT ALL PRIVILEGES ON cmddata.* TO 'your_adminer_user'@'localhost';
FLUSH PRIVILEGES;
```

---

### 🌐 3. Different server / host

This trips people up a lot.

Your terminal:

```
mysql -u root -p
```

Adminer might be connected to:

* another host (`127.0.0.1` vs `165.232.130.162`)
* or even a different MySQL instance

👉 Verify in Adminer:

* Server: `localhost` or `165.232.130.162`?
* Same as terminal?

---

### 🔍 4. Check again via SQL

Run this in Adminer SQL command box:

```sql
SHOW DATABASES;
```

If it doesn’t appear there → it’s a permission issue
If it appears → it’s just UI refresh

---

### 🔐 5. Permission issue (most common)

If database exists but not visible:

```sql
SHOW GRANTS FOR CURRENT_USER;
```

If no access to `cmddata`, grant it.

---

### ⚡ Quick sanity check

Run this in terminal:

```sql
SHOW DATABASES LIKE 'cmddata';
```

If it returns:

```
cmddata
```

→ database is 100% there

---

### Bottom line

This is almost always one of:

* Adminer not refreshed
* Different user
* Different server

---

If you want, tell me:

* your Adminer login (user + server)
* and your terminal login

I can pinpoint the exact issue in one step.

===============================================================================================================================
Alright—here’s a clean, practical guide to **creating a MySQL user and giving permissions**, with explanations so you actually understand what each command does.

---

# 🔐 1. Create a New User

```sql
CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'StrongPassword123!';
```

### 👉 What this means:

* `'newuser'` → username
* `'localhost'` → where the user can connect from

  * `localhost` = only same server
  * `%` = from anywhere (less secure)
* `IDENTIFIED BY` → sets password

---

# 🌐 Allow connection from anywhere (optional)

```sql
CREATE USER 'newuser'@'%' IDENTIFIED BY 'StrongPassword123!';
```

⚠️ Use `%` only if needed (remote access).

---

# 🗃️ 2. Grant Permissions (Access Rights)

## ✅ Give full access to one database

```sql
GRANT ALL PRIVILEGES ON cmddata.* TO 'newuser'@'localhost';
```

### 👉 Meaning:

* `cmddata.*` → all tables in `cmddata`
* `ALL PRIVILEGES` → full control (SELECT, INSERT, UPDATE, DELETE, etc.)

---

## 🌍 Give full access to all databases (admin-level)

```sql
GRANT ALL PRIVILEGES ON *.* TO 'newuser'@'localhost' WITH GRANT OPTION;
```

### 👉 Meaning:

* `*.*` → all databases
* `WITH GRANT OPTION` → user can give permissions to others

⚠️ This is basically **super admin access**

---

## 🔒 Limited permissions example

```sql
GRANT SELECT, INSERT, UPDATE ON cmddata.* TO 'newuser'@'localhost';
```

### 👉 Meaning:

User can:

* Read data (SELECT)
* Add data (INSERT)
* Modify data (UPDATE)
  But **cannot delete or drop tables**

---

# 🔄 3. Apply Changes

```sql
FLUSH PRIVILEGES;
```

👉 Reloads permission tables (sometimes automatic, but safe to run)

---

# 👀 4. Check Permissions

```sql
SHOW GRANTS FOR 'newuser'@'localhost';
```

👉 Shows what access the user has

---

# ❌ 5. Remove Permissions

```sql
REVOKE ALL PRIVILEGES ON cmddata.* FROM 'newuser'@'localhost';
```

---

# 🗑️ 6. Delete User

```sql
DROP USER 'newuser'@'localhost';
```

---

# 🔑 7. Change Password

```sql
ALTER USER 'newuser'@'localhost' IDENTIFIED BY 'NewPassword456!';
```

---

# 📋 Quick Real Example (Your Case)

If you want Adminer to access your `cmddata` database:

```sql
CREATE USER 'adminer_user'@'localhost' IDENTIFIED BY '123456';

GRANT ALL PRIVILEGES ON cmddata.* TO 'adminer_user'@'localhost';

FLUSH PRIVILEGES;
```

Then login in Adminer with:

* Username: `adminer_user`
* Password: `123456`

---

# ⚠️ Common Mistakes

* Creating user with `'localhost'` but connecting via IP → won’t work
* Forgetting `GRANT` → user exists but sees nothing
* Using different MySQL server (very common)

---

If you want, I can give you:
✅ exact commands for remote server (your `165.232...` IP)
✅ or fix your Adminer issue step-by-step

Just tell me 👍

===============================================================================================================================
Here’s a solid **real-world MySQL `CREATE TABLE` example** that covers the most commonly used data types.

---

# 📦 Example: Create a `users` table

```sql id="4yqf3q"
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    
    age INT,
    salary DECIMAL(10,2),
    
    is_active BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    birth_date DATE,
    login_time TIME,
    last_login DATETIME,
    
    profile_text TEXT,
    
    profile_image BLOB
);
```

---

# 🧠 Explanation of Common Data Types

### 🔢 Numbers

* `INT` → whole numbers (age, id)
* `DECIMAL(10,2)` → exact numbers (money like `99999999.99`)

---

### 🔤 Strings

* `VARCHAR(n)` → short text (name, email)
* `TEXT` → long text (bio, description)

---

### 🔐 Boolean

* `BOOLEAN` → `TRUE` / `FALSE` (stored as 1/0)

---

### 📅 Date & Time

* `DATE` → `YYYY-MM-DD`
* `TIME` → `HH:MM:SS`
* `DATETIME` → full date + time
* `TIMESTAMP` → auto-handled time (great for created/updated fields)

---

### 🖼️ Binary

* `BLOB` → store files (images, etc.)
  ⚠️ Usually better to store file path instead of image in DB

---

# 🔑 Important Keywords Used

* `AUTO_INCREMENT` → auto increases ID
* `PRIMARY KEY` → unique identifier
* `NOT NULL` → required field
* `UNIQUE` → no duplicate values
* `DEFAULT` → default value
* `ON UPDATE CURRENT_TIMESTAMP` → auto update time

---

# ⚡ Simple Version (Quick Use)

```sql id="5sz0c0"
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    price DECIMAL(8,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

# 🚀 Pro Tip

For passwords:

* Always use `VARCHAR(255)` (for hashed passwords)
* Never store plain text passwords

---

If you want, I can:
✅ Create table for your **cmddata project (real use case)**
✅ Add **foreign keys / relationships**
✅ Show **insert + select examples**

Just tell me 👍

===============================================================================================================================
===============================================================================================================================
## What are Joins in MySQL?

In MySQL, a **JOIN** is used to combine rows from two or more tables based on a related column between them.

Example:

You have:

* `customers` table
* `orders` table

Both tables may share a column like `customer_id`.

Using JOIN, you can fetch customer details along with their orders.

---

# Types of JOINs in MySQL

## 1. INNER JOIN

Returns only matching records from both tables.

### Syntax

```sql
SELECT columns
FROM table1
INNER JOIN table2
ON table1.common_column = table2.common_column;
```

### Example

```sql
SELECT customers.name, orders.order_id
FROM customers
INNER JOIN orders
ON customers.customer_id = orders.customer_id;
```

### Output

Only customers who placed orders.

---

## 2. LEFT JOIN (LEFT OUTER JOIN)

Returns:

* all rows from left table
* matched rows from right table
* NULL if no match

### Example

```sql
SELECT customers.name, orders.order_id
FROM customers
LEFT JOIN orders
ON customers.customer_id = orders.customer_id;
```

### Output

All customers, even if they never ordered.

---

## 3. RIGHT JOIN (RIGHT OUTER JOIN)

Returns:

* all rows from right table
* matched rows from left table

### Example

```sql
SELECT customers.name, orders.order_id
FROM customers
RIGHT JOIN orders
ON customers.customer_id = orders.customer_id;
```

---

## 4. FULL OUTER JOIN

MySQL does not directly support FULL OUTER JOIN.

It can be achieved using `UNION`.

### Example

```sql
SELECT *
FROM customers
LEFT JOIN orders
ON customers.customer_id = orders.customer_id

UNION

SELECT *
FROM customers
RIGHT JOIN orders
ON customers.customer_id = orders.customer_id;
```

### Output

All rows from both tables.

---

## 5. CROSS JOIN

Returns Cartesian product.

Every row from first table combines with every row from second table.

### Example

```sql
SELECT *
FROM colors
CROSS JOIN sizes;
```

If:

* colors = 3 rows
* sizes = 4 rows

Result = 12 rows.

---

## 6. SELF JOIN

Joining a table with itself.

### Example

Employee-manager relationship.

```sql
SELECT e.name AS Employee,
       m.name AS Manager
FROM employees e
LEFT JOIN employees m
ON e.manager_id = m.employee_id;
```

---

# Visual Understanding of Joins

Assume:

* Table A
* Table B

## INNER JOIN

Common data only.

## LEFT JOIN

All A + matching B.

## RIGHT JOIN

All B + matching A.

## FULL JOIN

Everything from both.

---

# Difference Between INNER JOIN and LEFT JOIN

| INNER JOIN                 | LEFT JOIN               |
| -------------------------- | ----------------------- |
| Returns matching rows only | Returns all left rows   |
| Excludes unmatched data    | Includes unmatched data |
| Faster usually             | Can return NULL values  |

---

# Important JOIN Conditions

## ON Clause

Defines relationship.

```sql
ON table1.id = table2.id
```

---

# Common Interview Questions and Answers

---

## 1. What is JOIN in MySQL?

### Answer

JOIN is used to retrieve data from multiple tables using related columns.

---

## 2. Difference between INNER JOIN and OUTER JOIN?

### Answer

* INNER JOIN returns matching records only.
* OUTER JOIN returns matching + non-matching records.

---

## 3. Difference between LEFT JOIN and RIGHT JOIN?

### Answer

* LEFT JOIN keeps all rows from left table.
* RIGHT JOIN keeps all rows from right table.

---

## 4. What is SELF JOIN?

### Answer

A table joined with itself.

Used in:

* employee-manager hierarchy
* category-parent relationships

---

## 5. What is CROSS JOIN?

### Answer

Returns Cartesian product of two tables.

If table A has `m` rows and B has `n` rows, result has `m × n` rows.

---

## 6. Can MySQL perform FULL OUTER JOIN?

### Answer

No direct support.

Use:

```sql
LEFT JOIN + RIGHT JOIN + UNION
```

---

## 7. What is Cartesian Product?

### Answer

Combination of every row from one table with every row from another table.

Produced by CROSS JOIN or missing JOIN condition.

---

## 8. What happens if ON condition is missing?

### Answer

MySQL may produce Cartesian product resulting in huge data.

Example:

```sql
SELECT *
FROM A, B;
```

---

## 9. Which JOIN is fastest?

### Answer

Usually INNER JOIN because it returns filtered matching records only.

Performance also depends on:

* indexes
* table size
* query optimization

---

## 10. What are Equi Join and Non-Equi Join?

### Equi Join

Uses `=` operator.

```sql
ON A.id = B.id
```

### Non-Equi Join

Uses:

* `>`
* `<`
* `BETWEEN`
  etc.

---

# Advanced Interview Questions

---

## 11. Difference between WHERE and ON in JOIN?

### Answer

### ON

Defines JOIN condition.

### WHERE

Filters records after JOIN.

Example:

```sql
SELECT *
FROM A
LEFT JOIN B
ON A.id = B.id
WHERE B.id IS NOT NULL;
```

---

## 12. Explain execution order of JOIN query.

### Answer

Typical order:

1. FROM
2. JOIN
3. ON
4. WHERE
5. GROUP BY
6. HAVING
7. SELECT
8. ORDER BY
9. LIMIT

---

## 13. What is indexing in JOIN performance?

### Answer

Indexes on JOIN columns improve speed significantly.

Example:

```sql
CREATE INDEX idx_customer
ON orders(customer_id);
```

---

## 14. What is Natural Join?

### Answer

Automatically joins columns with same names.

Example:

```sql
SELECT *
FROM customers
NATURAL JOIN orders;
```

Usually avoided in production because it may create unintended joins.

---

## 15. Explain many-to-many relationship JOIN.

### Example

Students ↔ Courses

Uses bridge table:

```text
students
courses
student_courses
```

JOIN example:

```sql
SELECT s.name, c.course_name
FROM students s
JOIN student_courses sc
ON s.id = sc.student_id
JOIN courses c
ON c.id = sc.course_id;
```

---

# Real-World JOIN Examples

## E-commerce

* customers + orders
* orders + payments
* products + categories

## HR Systems

* employees + departments
* employees + managers

## Banking

* customers + accounts
* transactions + accounts

---

# Tips for JOIN Interview Preparation

## Remember:

* INNER = matching only
* LEFT = all left
* RIGHT = all right
* FULL = everything
* CROSS = combinations
* SELF = same table

---

# Most Asked Practical SQL JOIN Problems

## Find employees without department

```sql
SELECT e.name
FROM employees e
LEFT JOIN departments d
ON e.department_id = d.id
WHERE d.id IS NULL;
```

---

## Find duplicate records

```sql
SELECT email, COUNT(*)
FROM users
GROUP BY email
HAVING COUNT(*) > 1;
```

---

## Second highest salary

```sql
SELECT MAX(salary)
FROM employees
WHERE salary <
(
    SELECT MAX(salary)
    FROM employees
);
```

---

# Short Summary

| JOIN Type       | Returns                  |
| --------------- | ------------------------ |
| INNER JOIN      | Matching rows            |
| LEFT JOIN       | All left + matched right |
| RIGHT JOIN      | All right + matched left |
| FULL OUTER JOIN | All rows from both       |
| CROSS JOIN      | Cartesian product        |
| SELF JOIN       | Table joined to itself   |

---

## Practice Recommendation

You can practice JOIN queries on:

* [MySQL Official Documentation](https://dev.mysql.com/doc/?utm_source=chatgpt.com)
* [LeetCode SQL Problems](https://leetcode.com/problemset/database/?utm_source=chatgpt.com)
* [HackerRank SQL Practice](https://www.hackerrank.com/domains/sql?utm_source=chatgpt.com)
* [W3Schools MySQL JOIN Tutorial](https://www.w3schools.com/mysql/mysql_join.asp?utm_source=chatgpt.com)

===============================================================================================================================
======================================================================================================================================
To get the **second highest salary** and **N-th highest salary** in SQL, there are multiple ways depending on the database (MySQL, Oracle, SQL Server, PostgreSQL).

Assume table:

```sql
Employee
+----+--------+
| id | salary |
+----+--------+
```

---

# 1. Second Highest Salary

## Method 1: Using MAX() (Most Common)

```sql
SELECT MAX(salary) AS SecondHighestSalary
FROM Employee
WHERE salary < (
    SELECT MAX(salary)
    FROM Employee
);
```

### Explanation

* Inner query gets highest salary.
* Outer query gets maximum salary less than highest salary.

Example discussed in SQL interview resources. ([GeeksforGeeks][1])

---

# 2. N-th Highest Salary

## Method 1: Using `LIMIT` (MySQL)

```sql
SELECT DISTINCT salary
FROM Employee
ORDER BY salary DESC
LIMIT N-1, 1;
```

### Example: 3rd highest salary

```sql
SELECT DISTINCT salary
FROM Employee
ORDER BY salary DESC
LIMIT 2, 1;
```

* `LIMIT 2,1`

  * Skip first 2 rows
  * Fetch 1 row

Common MySQL approach. ([GeeksforGeeks][1])

---

# 3. Using `DENSE_RANK()` (Best Interview Answer)

Works in:

* SQL Server
* PostgreSQL
* Oracle
* MySQL 8+

```sql
WITH CTE AS (
    SELECT salary,
           DENSE_RANK() OVER (ORDER BY salary DESC) AS rnk
    FROM Employee
)
SELECT salary
FROM CTE
WHERE rnk = N;
```

### Example: 2nd highest salary

```sql
WITH CTE AS (
    SELECT salary,
           DENSE_RANK() OVER (ORDER BY salary DESC) AS rnk
    FROM Employee
)
SELECT salary
FROM CTE
WHERE rnk = 2;
```

Recommended because it handles duplicate salaries correctly. ([GeeksforGeeks][1])

---

# 4. Difference Between ROW_NUMBER, RANK, DENSE_RANK

| Function     | Duplicate Salaries | Example Ranks |
| ------------ | ------------------ | ------------- |
| ROW_NUMBER() | Different rank     | 1,2,3         |
| RANK()       | Skips ranks        | 1,1,3         |
| DENSE_RANK() | No skipped ranks   | 1,1,2         |

For salary problems, usually use:

```sql
DENSE_RANK()
```

because duplicate salaries should share same rank. ([Reddit][2])

---

# 5. Oracle Version (`ROWNUM`)

```sql
SELECT salary
FROM (
    SELECT DISTINCT salary
    FROM Employee
    ORDER BY salary DESC
)
WHERE ROWNUM = N;
```

---

# 6. SQL Server Version

## Second highest

```sql
SELECT TOP 1 salary
FROM (
    SELECT DISTINCT TOP 2 salary
    FROM Employee
    ORDER BY salary DESC
) t
ORDER BY salary;
```

([YouTube][3])

---

# Best Practice for Interviews

Interviewers usually prefer:

```sql
DENSE_RANK()
```

because:

* readable
* scalable
* handles duplicates
* works for per-department ranking too

---

# Bonus: N-th Highest Salary Department Wise

```sql
WITH CTE AS (
    SELECT department_id,
           employee_name,
           salary,
           DENSE_RANK() OVER (
               PARTITION BY department_id
               ORDER BY salary DESC
           ) AS rnk
    FROM Employee
)
SELECT *
FROM CTE
WHERE rnk = 2;
```

This gives 2nd highest salary in each department. ([GeeksforGeeks][4])

[1]: https://www.geeksforgeeks.org/sql-query-to-find-second-largest-salary/?utm_source=chatgpt.com "SQL Query to Find Second Highest Salary - GeeksforGeeks"
[2]: https://www.reddit.com/r/dataengprep/comments/1sib0m1/the_5_sql_patterns_that_show_up_in_every_data/?utm_source=chatgpt.com "The 5 SQL patterns that show up in every data engineering interview (with examples)"
[3]: https://www.youtube.com/watch?v=fvPddKyHxpQ&utm_source=chatgpt.com "Part 1   How to find nth highest salary in sql - YouTube"
[4]: https://www.geeksforgeeks.org/plsql/plsql-query-to-list-the-second-highest-salary-by-department/?utm_source=chatgpt.com "PL/SQL Query to List the Second Highest Salary By Department - GeeksforGeeks"

======================================================================================================================================
Here’s a simple **dummy dataset** you can use to test all the queries (2nd highest, N-th highest, ranking, etc.).

---

# 1. Create Table

```sql
CREATE TABLE Employee (
    id INT PRIMARY KEY,
    name VARCHAR(50),
    department VARCHAR(50),
    salary INT
);
```

---

# 2. Insert Dummy Data

```sql
INSERT INTO Employee (id, name, department, salary) VALUES
(1, 'Amit', 'IT', 90000),
(2, 'Neha', 'IT', 120000),
(3, 'Rohit', 'HR', 70000),
(4, 'Sara', 'HR', 120000),
(5, 'Vikram', 'Finance', 95000),
(6, 'Priya', 'Finance', 85000),
(7, 'John', 'IT', 120000),
(8, 'Karan', 'HR', 70000),
(9, 'Anjali', 'Finance', 95000),
(10, 'Meera', 'IT', 60000);
```

---

# 3. What This Data Contains

### Salaries present:

```
120000
120000
120000
95000
95000
90000
85000
70000
70000
60000
```

---

# 4. How You Can Test

## 🔹 Second Highest Salary

```sql
SELECT MAX(salary)
FROM Employee
WHERE salary < (SELECT MAX(salary) FROM Employee);
```

👉 Expected result: **95000**

---

## 🔹 3rd Highest Salary (using DENSE_RANK)

```sql
WITH CTE AS (
    SELECT salary,
           DENSE_RANK() OVER (ORDER BY salary DESC) AS rnk
    FROM Employee
)
SELECT salary
FROM CTE
WHERE rnk = 3;
```

---

## 🔹 Top N salaries

Try changing `N`:

* 1 → 120000
* 2 → 95000
* 3 → 90000

---

# 5. If you want next step

I can also give you:

* per-department ranking queries
* remove duplicates logic
* interview tricky questions on this dataset
* or LeetCode-style problems

Just tell 👍


======================================================================================================================================
For clothing products with:

* Category → Clothes
* Product → T-Shirt
* Variations:

  * Size → S, M, L
  * Color → Red, Green, Blue
  * Type → Printed, Non-Printed
* Different prices per combination

you should use a **normalized product variation structure**.

---

# Recommended Database Structure

## 1. categories

```sql
categories
----------
id
name
```

Example:

| id | name    |
| -- | ------- |
| 1  | Clothes |

---

# 2. products

```sql
products
---------
id
category_id
name
description
```

Example:

| id | category_id | name    |
| -- | ----------- | ------- |
| 1  | 1           | T-Shirt |

---

# 3. attributes

Store variation types.

```sql
attributes
-----------
id
name
```

Example:

| id | name  |
| -- | ----- |
| 1  | Size  |
| 2  | Color |
| 3  | Type  |

---

# 4. attribute_values

Store actual values.

```sql
attribute_values
----------------
id
attribute_id
value
```

Example:

| id | attribute_id | value       |
| -- | ------------ | ----------- |
| 1  | 1            | S           |
| 2  | 1            | M           |
| 3  | 1            | L           |
| 4  | 2            | Red         |
| 5  | 2            | Green       |
| 6  | 2            | Blue        |
| 7  | 3            | Printed     |
| 8  | 3            | Non-Printed |

---

# 5. product_variants

Each combination = one row.

```sql
product_variants
----------------
id
product_id
sku
price
stock
```

Example:

| id | product_id | sku            | price |
| -- | ---------- | -------------- | ----- |
| 1  | 1          | TS-RED-M-PRINT | 500   |
| 2  | 1          | TS-BLUE-L-NON  | 450   |

---

# 6. product_variant_values

Attach attributes to variant.

```sql
product_variant_values
----------------------
id
product_variant_id
attribute_value_id
```

Example:

| variant_id | attribute_value_id |
| ---------- | ------------------ |
| 1          | 2 (M)              |
| 1          | 4 (Red)            |
| 1          | 7 (Printed)        |

| variant_id | attribute_value_id |
| ---------- | ------------------ |
| 2          | 3 (L)              |
| 2          | 6 (Blue)           |
| 2          | 8 (Non-Printed)    |

---

# Result

You can now have:

| Product | Size | Color | Type        | Price |
| ------- | ---- | ----- | ----------- | ----- |
| T-Shirt | M    | Red   | Printed     | 500   |
| T-Shirt | L    | Blue  | Non-Printed | 450   |

---

# Why This Structure is Best

This supports:

✅ Unlimited variations
✅ Different prices
✅ Different stock per variant
✅ SKU support
✅ Easy filtering
✅ Future attributes (Fabric, Sleeve, Brand, etc.)
✅ Works for ecommerce

---

# Laravel Relationships

## Product.php

```php
public function variants()
{
    return $this->hasMany(ProductVariant::class);
}
```

---

## ProductVariant.php

```php
public function values()
{
    return $this->belongsToMany(
        AttributeValue::class,
        'product_variant_values'
    );
}
```

---

# Example Variant Creation

### Variant:

```text
T-Shirt
Size: M
Color: Red
Type: Printed
Price: 500
```

Stored as:

```text
product_variants
id = 1
price = 500
```

and

```text
product_variant_values
variant 1 -> M
variant 1 -> Red
variant 1 -> Printed
```

---

# Alternative (Simple Structure)

If your system is small, you can use:

```sql
product_variants
----------------
id
product_id
size
color
type
price
stock
```

Example:

| size | color | type    | price |
| ---- | ----- | ------- | ----- |
| M    | Red   | Printed | 500   |

But this is NOT scalable.

---

# Recommendation

Use:

```text
products
product_variants
attributes
attribute_values
product_variant_values
```

This is the standard ecommerce approach used by:

* Shopify
* WooCommerce
* Magento
* Daraz
* Amazon

======================================================================================================================================

======================================================================================================================================

```sql
-- =========================================
-- CATEGORIES
-- =========================================

CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

INSERT INTO categories (name) VALUES
('Clothes');



-- =========================================
-- PRODUCTS
-- =========================================

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE CASCADE
);

INSERT INTO products (category_id, name, description) VALUES
(1, 'T-Shirt', 'Cotton premium t-shirt');



-- =========================================
-- ATTRIBUTES
-- =========================================

CREATE TABLE attributes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

INSERT INTO attributes (name) VALUES
('Size'),
('Color'),
('Type');



-- =========================================
-- ATTRIBUTE VALUES
-- =========================================

CREATE TABLE attribute_values (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attribute_id BIGINT UNSIGNED NOT NULL,
    value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (attribute_id) REFERENCES attributes(id)
        ON DELETE CASCADE
);

INSERT INTO attribute_values (attribute_id, value) VALUES

-- Sizes
(1, 'S'),
(1, 'M'),
(1, 'L'),

-- Colors
(2, 'Red'),
(2, 'Green'),
(2, 'Blue'),

-- Types
(3, 'Printed'),
(3, 'Non-Printed');



-- =========================================
-- PRODUCT VARIANTS
-- =========================================

CREATE TABLE product_variants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    sku VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
);

INSERT INTO product_variants
(product_id, sku, price, stock)
VALUES

(1, 'TS-RED-M-PRINT', 500.00, 20),
(1, 'TS-BLUE-L-NON', 450.00, 15),
(1, 'TS-GREEN-S-PRINT', 550.00, 10);



-- =========================================
-- PRODUCT VARIANT VALUES
-- =========================================

CREATE TABLE product_variant_values (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_variant_id BIGINT UNSIGNED NOT NULL,
    attribute_value_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    FOREIGN KEY (product_variant_id)
        REFERENCES product_variants(id)
        ON DELETE CASCADE,

    FOREIGN KEY (attribute_value_id)
        REFERENCES attribute_values(id)
        ON DELETE CASCADE
);



-- =========================================
-- VARIANT 1
-- TS-RED-M-PRINT
-- =========================================

INSERT INTO product_variant_values
(product_variant_id, attribute_value_id)
VALUES
(1, 2), -- M
(1, 4), -- Red
(1, 7); -- Printed



-- =========================================
-- VARIANT 2
-- TS-BLUE-L-NON
-- =========================================

INSERT INTO product_variant_values
(product_variant_id, attribute_value_id)
VALUES
(2, 3), -- L
(2, 6), -- Blue
(2, 8); -- Non-Printed



-- =========================================
-- VARIANT 3
-- TS-GREEN-S-PRINT
-- =========================================

INSERT INTO product_variant_values
(product_variant_id, attribute_value_id)
VALUES
(3, 1), -- S
(3, 5), -- Green
(3, 7); -- Printed
```

======================================================================================================================================
That’s exactly why the dynamic attribute structure is useful.

You do NOT need new tables for electronics.

You only add:

* New category
* New product
* New attributes
* New attribute values
* New variants

Your existing structure already supports unlimited product types.

---

# Example

## Category

```text
Electronics
```

## Product

```text
Charger
```

## Attributes

```text
Charging Type
Power
Wire Length
```

## Attribute Values

```text
Fast Charger
Normal Charger

60W
80W

10m
20m
```

---

# Insert SQL

## Add Electronics Category

```sql id="d7h4vr"
INSERT INTO categories (name)
VALUES ('Electronics');
```

Assume:

```text
Electronics ID = 2
```

---

# Add Charger Product

```sql id="k74gmf"
INSERT INTO products (category_id, name, description)
VALUES
(2, 'Charger', 'Mobile fast charger');
```

Assume:

```text
Charger Product ID = 2
```

---

# Add New Attributes

```sql id="iv8j66"
INSERT INTO attributes (name)
VALUES
('Charging Type'),
('Power'),
('Wire Length');
```

Assume IDs:

| Attribute     | ID |
| ------------- | -- |
| Charging Type | 4  |
| Power         | 5  |
| Wire Length   | 6  |

---

# Add Attribute Values

```sql id="bwsk5v"
INSERT INTO attribute_values (attribute_id, value)
VALUES

-- Charging Type
(4, 'Fast Charger'),
(4, 'Normal Charger'),

-- Power
(5, '60W'),
(5, '80W'),

-- Wire Length
(6, '10m'),
(6, '20m');
```

---

# Add Variants

## Variant 1

```text
Fast Charger
60W
10m
Price = 1200
```

```sql id="pr29lm"
INSERT INTO product_variants
(product_id, sku, price, stock)
VALUES
(2, 'CHR-FAST-60W-10M', 1200, 50);
```

---

## Variant 2

```text
Fast Charger
80W
20m
Price = 1800
```

```sql id="n1wcw4"
INSERT INTO product_variants
(product_id, sku, price, stock)
VALUES
(2, 'CHR-FAST-80W-20M', 1800, 30);
```

---

# Connect Variant Values

Assume:

| Value          | ID |
| -------------- | -- |
| Fast Charger   | 9  |
| Normal Charger | 10 |
| 60W            | 11 |
| 80W            | 12 |
| 10m            | 13 |
| 20m            | 14 |

---

## Variant 1 Mapping

```sql id="h6vbbf"
INSERT INTO product_variant_values
(product_variant_id, attribute_value_id)
VALUES
(4, 9),  -- Fast Charger
(4, 11), -- 60W
(4, 13); -- 10m
```

---

## Variant 2 Mapping

```sql id="5zswvl"
INSERT INTO product_variant_values
(product_variant_id, attribute_value_id)
VALUES
(5, 9),  -- Fast Charger
(5, 12), -- 80W
(5, 14); -- 20m
```

---

# Final Result

| Product | Charging Type | Power | Wire Length | Price |
| ------- | ------------- | ----- | ----------- | ----- |
| Charger | Fast Charger  | 60W   | 10m         | 1200  |
| Charger | Fast Charger  | 80W   | 20m         | 1800  |

---

# Biggest Advantage

This structure supports ANY industry:

| Category    | Possible Attributes   |
| ----------- | --------------------- |
| Clothes     | Size, Color, Fabric   |
| Electronics | Watt, Voltage, Length |
| Shoes       | Size, Material        |
| Grocery     | Weight, Flavor        |
| Furniture   | Wood, Width, Height   |

WITHOUT changing database structure.

That is why this is the standard scalable ecommerce architecture.

======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
======================================================================================================================================
