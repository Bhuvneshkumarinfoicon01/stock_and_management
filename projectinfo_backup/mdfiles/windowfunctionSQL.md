//
In SQL, **window functions** perform calculations across a set of rows related to the current row, while still returning individual rows (unlike `GROUP BY`, which collapses rows).

They are extremely useful for:

* Ranking
* Running totals
* Moving averages
* Comparing rows
* Percentiles
* Time-series analysis

---

# Basic Syntax

```sql
FUNCTION_NAME(...) OVER (
    PARTITION BY column
    ORDER BY column
)
```

* `OVER()` → turns a normal function into a window function
* `PARTITION BY` → divides rows into groups
* `ORDER BY` → defines row sequence inside each group

---

# Example Table

## Employees

| id | name  | dept | salary |
| -- | ----- | ---- | ------ |
| 1  | Alice | HR   | 5000   |
| 2  | Bob   | HR   | 6000   |
| 3  | Carol | IT   | 7000   |
| 4  | David | IT   | 9000   |
| 5  | Eve   | IT   | 8500   |

---

# 1. ROW_NUMBER()

Assigns a unique sequential number.

```sql
SELECT
    name,
    dept,
    salary,
    ROW_NUMBER() OVER (
        PARTITION BY dept
        ORDER BY salary DESC
    ) AS row_num
FROM employees;
```

### Output

| name  | dept | salary | row_num |
| ----- | ---- | ------ | ------- |
| Bob   | HR   | 6000   | 1       |
| Alice | HR   | 5000   | 2       |
| David | IT   | 9000   | 1       |
| Eve   | IT   | 8500   | 2       |
| Carol | IT   | 7000   | 3       |

Explanation:

* Employees are grouped by department
* Ranked by salary descending
* Row numbers restart for each department

---

# 2. RANK()

Gives ranking with gaps for ties.

```sql
SELECT
    name,
    salary,
    RANK() OVER (ORDER BY salary DESC) AS rank_num
FROM employees;
```

If two employees have the same salary:

| salary | rank |
| ------ | ---- |
| 9000   | 1    |
| 8500   | 2    |
| 8500   | 2    |
| 7000   | 4    |

Notice rank `3` is skipped.

---

# 3. DENSE_RANK()

Like `RANK()`, but without gaps.

```sql
SELECT
    name,
    salary,
    DENSE_RANK() OVER (ORDER BY salary DESC) AS dense_rank
FROM employees;
```

Output:

| salary | dense_rank |
| ------ | ---------- |
| 9000   | 1          |
| 8500   | 2          |
| 8500   | 2          |
| 7000   | 3          |

---

# 4. Running Total using SUM()

```sql
SELECT
    name,
    salary,
    SUM(salary) OVER (
        ORDER BY id
    ) AS running_total
FROM employees;
```

### Output

| name  | salary | running_total |
| ----- | ------ | ------------- |
| Alice | 5000   | 5000          |
| Bob   | 6000   | 11000         |
| Carol | 7000   | 18000         |
| David | 9000   | 27000         |

This keeps accumulating values.

---

# 5. AVG() Window Function

Department-wise average salary:

```sql
SELECT
    name,
    dept,
    salary,
    AVG(salary) OVER (
        PARTITION BY dept
    ) AS avg_salary
FROM employees;
```

### Output

| name  | dept | salary | avg_salary |
| ----- | ---- | ------ | ---------- |
| Alice | HR   | 5000   | 5500       |
| Bob   | HR   | 6000   | 5500       |
| Carol | IT   | 7000   | 8166       |
| David | IT   | 9000   | 8166       |

Unlike `GROUP BY`, every row remains visible.

---

# 6. LAG()

Access previous row value.

```sql
SELECT
    name,
    salary,
    LAG(salary) OVER (
        ORDER BY salary
    ) AS prev_salary
FROM employees;
```

### Output

| name  | salary | prev_salary |
| ----- | ------ | ----------- |
| Alice | 5000   | NULL        |
| Bob   | 6000   | 5000        |
| Carol | 7000   | 6000        |

Useful for:

* Comparing current vs previous month
* Detecting trends

---

# 7. LEAD()

Access next row value.

```sql
SELECT
    name,
    salary,
    LEAD(salary) OVER (
        ORDER BY salary
    ) AS next_salary
FROM employees;
```

---

# Difference: GROUP BY vs Window Function

## GROUP BY

```sql
SELECT dept, AVG(salary)
FROM employees
GROUP BY dept;
```

Result:

* One row per department

---

## Window Function

```sql
SELECT
    name,
    dept,
    salary,
    AVG(salary) OVER(PARTITION BY dept)
FROM employees;
```

Result:

* Keeps all employee rows
* Adds computed value

---

# Real-World Uses

Window functions are heavily used in:

* Data analytics
* Reporting dashboards
* Finance systems
* Leaderboards
* Time-series analysis
* ETL pipelines

Examples:

* Top 3 products per category
* Month-over-month sales growth
* Detect duplicate records
* Running revenue totals
* Customer retention analysis

---

# Common Window Functions

| Function        | Purpose                  |
| --------------- | ------------------------ |
| `ROW_NUMBER()`  | Unique numbering         |
| `RANK()`        | Ranking with gaps        |
| `DENSE_RANK()`  | Ranking without gaps     |
| `SUM()`         | Running totals           |
| `AVG()`         | Moving averages          |
| `LAG()`         | Previous row             |
| `LEAD()`        | Next row                 |
| `FIRST_VALUE()` | First value in window    |
| `LAST_VALUE()`  | Last value in window     |
| `NTILE()`       | Divide rows into buckets |

---

# Important Concept: Window Frame

You can control how many rows are included:

```sql
SUM(salary) OVER (
    ORDER BY id
    ROWS BETWEEN UNBOUNDED PRECEDING
    AND CURRENT ROW
)
```

This means:

* Start from first row
* Include rows up to current row

That creates a cumulative sum.

---

# Interview Tip

A common interview question:

> “Why use window functions instead of GROUP BY?”

Answer:

* `GROUP BY` collapses rows
* Window functions preserve row-level detail while performing aggregate calculations
======================================================================================================================================
`OVER()` is the core part of a window function.
It defines **which rows are visible to the function** for each current row.

Think of it like:

> “For this row, what is the window of rows I should calculate on?”

---

# Basic Idea

Without `OVER()`, aggregate functions collapse rows:

```sql id="f2up0s"
SELECT AVG(salary)
FROM employees;
```

Result → single row.

---

With `OVER()`, SQL keeps all rows:

```sql id="k6nk76"
SELECT
    name,
    salary,
    AVG(salary) OVER()
FROM employees;
```

Now every row gets the average salary.

---

# What OVER() Does

`OVER()` changes:

* **Scope of calculation**
* **Grouping behavior**
* **Ordering behavior**
* **Frame of rows**

---

# 1. OVER() Empty

```sql id="0xyx4g"
SUM(salary) OVER()
```

Means:

> Use ALL rows as one window.

Example:

| name | salary | total |
| ---- | ------ | ----- |
| A    | 1000   | 15000 |
| B    | 2000   | 15000 |
| C    | 3000   | 15000 |

Entire table is visible to every row.

---

# 2. OVER(PARTITION BY ...)

Partitions divide rows into groups.

```sql id="8b4n0u"
AVG(salary) OVER(PARTITION BY dept)
```

Now each department becomes its own window.

---

## Example

| name | dept | salary |
| ---- | ---- | ------ |
| A    | HR   | 5000   |
| B    | HR   | 6000   |
| C    | IT   | 7000   |
| D    | IT   | 9000   |

Query:

```sql id="ztx6co"
SELECT
    name,
    dept,
    salary,
    AVG(salary) OVER(PARTITION BY dept) AS avg_sal
FROM employees;
```

Output:

| name | dept | salary | avg_sal |
| ---- | ---- | ------ | ------- |
| A    | HR   | 5000   | 5500    |
| B    | HR   | 6000   | 5500    |
| C    | IT   | 7000   | 8000    |
| D    | IT   | 9000   | 8000    |

---

# 3. OVER(ORDER BY ...)

Ordering changes the window row-by-row.

Example:

```sql id="j8m9z2"
SUM(salary) OVER(ORDER BY id)
```

This creates a running total.

---

## Step-by-step

Rows:

| id | salary |
| -- | ------ |
| 1  | 100    |
| 2  | 200    |
| 3  | 300    |

---

### Row 1 Window

Visible rows:

| salary |
| ------ |
| 100    |

SUM = 100

---

### Row 2 Window

Visible rows:

| salary |
| ------ |
| 100    |
| 200    |

SUM = 300

---

### Row 3 Window

Visible rows:

| salary |
| ------ |
| 100    |
| 200    |
| 300    |

SUM = 600

---

Result:

| id | salary | running_total |
| -- | ------ | ------------- |
| 1  | 100    | 100           |
| 2  | 200    | 300           |
| 3  | 300    | 600           |

---

# Why ORDER BY Changes Behavior

Without `ORDER BY`:

```sql id="gbdt0l"
SUM(salary) OVER()
```

Every row sees entire table.

---

With `ORDER BY`:

```sql id="rjw6ek"
SUM(salary) OVER(ORDER BY id)
```

Window grows row-by-row.

That is the major behavioral change.

---

# 4. OVER with ROWS Frame

You can explicitly define the window size.

Example:

```sql id="9p91m9"
AVG(salary) OVER(
    ORDER BY id
    ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING
)
```

Meaning:

For current row:

* include previous row
* include current row
* include next row

This creates a moving average.

---

# Visual Understanding

Suppose salaries:

| id | salary |
| -- | ------ |
| 1  | 100    |
| 2  | 200    |
| 3  | 300    |
| 4  | 400    |

---

For row id=2:

Window becomes:

| salary |
| ------ |
| 100    |
| 200    |
| 300    |

AVG = 200

---

For row id=3:

Window becomes:

| salary |
| ------ |
| 200    |
| 300    |
| 400    |

AVG = 300

---

# How OVER Changes Different Functions

---

## ROW_NUMBER()

```sql id="2cct8y"
ROW_NUMBER() OVER(ORDER BY salary DESC)
```

Changes numbering order.

---

## RANK()

```sql id="8f3r44"
RANK() OVER(PARTITION BY dept ORDER BY salary DESC)
```

Ranking resets per department.

---

## LAG()

```sql id="v4m5jl"
LAG(salary) OVER(ORDER BY id)
```

Previous row depends on ordering.

---

# Internal Concept

For every row:

1. SQL creates a temporary "window"
2. Applies function on that window
3. Returns result for current row

The window changes depending on:

* `PARTITION BY`
* `ORDER BY`
* `ROWS/RANGE`

---

# Easy Mental Model

Think:

```text
OVER() = define the viewing window
```

| OVER clause               | Meaning             |
| ------------------------- | ------------------- |
| `OVER()`                  | whole table         |
| `OVER(PARTITION BY dept)` | per group           |
| `OVER(ORDER BY id)`       | cumulative sequence |
| `OVER(... ROWS ...)`      | sliding window      |

---

# Most Important Thing to Remember

`OVER()` does NOT reduce rows.

It:

* keeps original rows
* adds calculated values using a dynamic window of rows.

======================================================================================================================================
   SELECT name, department, salary, ROW_NUMBER() OVER ( PARTITION BY department ORDER BY salary DESC) AS row_num FROM Employee;
    SELECT name, salary, RANK() over (ORDER BY salary DESC) as rank_num from Employee;
    SELECT name, salary, DENSE_RANK() OVER (ORDER BY salary DESC) AS salary_rank FROM Employee;
    SELECT name, salary, SUM(salary) over (ORDER BY id) AS running_total FROM Employee;
    SELECT name, department, salary,  AVG(salary) OVER (PARTITION BY department) as avg_salary from Employee;
    SELECT name, salary, LAG(salary) over (ORDER BY salary) as prev_salary FROM Employee;
    SELECT name, salary, LEAD(salary) over (ORDER BY salary) as next_salary FROM Employee;
    SELECT department, AVG(salary) FROM Employee GROUP BY department;
    SELECT name, department, salary, AVG(salary) OVER(PARTITION BY department) FROM Employee;





    select max(salary) as secondhig from Employee where salary < (select max(salary) from Employee);
    select distinct salary from Employee order by salary desc limit 2, 1;
    with ctc as (select salary, dense_rank() over(order By salary desc) as rnk from Employee ) select salary from ctc where rnk=2;

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
