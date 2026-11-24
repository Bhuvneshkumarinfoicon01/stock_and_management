//
In React, **Hooks** are special functions that let you use features like state, lifecycle methods, context, refs, and side effects inside **function components**.

Before hooks, these features mostly required class components. Hooks made React code simpler, reusable, and easier to organize.

Official docs: [React Hooks Documentation](https://react.dev/reference/react/hooks?utm_source=chatgpt.com)

---

# Why Hooks Exist

Hooks solve common problems:

* Reusing logic between components
* Managing state in functional components
* Handling side effects cleanly
* Avoiding complex class components
* Making code more readable

---

# Rules of Hooks

You must follow these rules:

1. Only call hooks at the top level

   * Not inside loops, conditions, or nested functions

2. Only call hooks inside:

   * React function components
   * Custom hooks

Example:

```jsx
// ✅ Correct
function App() {
  const [count, setCount] = useState(0)
}

// ❌ Wrong
if (true) {
  useState(0)
}
```

---

# Main React Hooks Explained

---

# 1. useState()

Used to store and update component data.

## When to use

Use when UI data changes over time:

* Counter
* Form input
* Toggle
* Modal open/close
* API response storage

## Example

```jsx
import { useState } from "react";

function Counter() {
  const [count, setCount] = useState(0);

  return (
    <div>
      <h2>{count}</h2>

      <button onClick={() => setCount(count + 1)}>
        Increment
      </button>
    </div>
  );
}
```

## How it works

```js
const [state, setState] = useState(initialValue)
```

* `state` → current value
* `setState` → update function

---

# 2. useEffect()

Used for **side effects**.

Side effects include:

* API calls
* Timers
* Event listeners
* DOM updates
* Local storage
* Subscriptions

## When to use

Whenever something should happen:

* after rendering
* after state changes
* after component mount/unmount

---

## Example: API Fetch

```jsx
import { useEffect, useState } from "react";

function Users() {
  const [users, setUsers] = useState([]);

  useEffect(() => {
    fetch("https://jsonplaceholder.typicode.com/users")
      .then(res => res.json())
      .then(data => setUsers(data));
  }, []);

  return (
    <ul>
      {users.map(user => (
        <li key={user.id}>{user.name}</li>
      ))}
    </ul>
  );
}
```

---

## Dependency Array

### Run once

```js
useEffect(() => {
  console.log("Mounted");
}, []);
```

### Run on value change

```js
useEffect(() => {
  console.log("Count changed");
}, [count]);
```

### Cleanup function

```jsx
useEffect(() => {
  const timer = setInterval(() => {
    console.log("Running");
  }, 1000);

  return () => clearInterval(timer);
}, []);
```

Used to:

* prevent memory leaks
* remove listeners
* clear timers

---

# 3. useContext()

Used to share data globally without prop drilling.

## When to use

Good for:

* Theme
* Auth user
* Language
* Global settings

---

## Example

### Create Context

```jsx
import { createContext } from "react";

export const ThemeContext = createContext();
```

### Provide Context

```jsx
<ThemeContext.Provider value="dark">
  <App />
</ThemeContext.Provider>
```

### Consume Context

```jsx
import { useContext } from "react";

function Header() {
  const theme = useContext(ThemeContext);

  return <h1>{theme}</h1>;
}
```

---

# 4. useRef()

Stores mutable values without re-rendering.

## When to use

* Access DOM elements
* Store previous values
* Timers/IDs
* Avoid re-render

---

## Example: Focus Input

```jsx
import { useRef } from "react";

function InputFocus() {
  const inputRef = useRef();

  const focusInput = () => {
    inputRef.current.focus();
  };

  return (
    <>
      <input ref={inputRef} />

      <button onClick={focusInput}>
        Focus
      </button>
    </>
  );
}
```

---

# 5. useMemo()

Caches expensive calculations.

## When to use

Use only for expensive operations:

* Large filtering
* Sorting
* Heavy calculations

Avoid unnecessary recalculations.

---

## Example

```jsx
import { useMemo } from "react";

function App({ numbers }) {
  const total = useMemo(() => {
    return numbers.reduce((a, b) => a + b, 0);
  }, [numbers]);

  return <h1>{total}</h1>;
}
```

---

# 6. useCallback()

Caches functions.

## When to use

Useful when:

* Passing functions to child components
* Preventing unnecessary renders

---

## Example

```jsx
import { useCallback, useState } from "react";

function App() {
  const [count, setCount] = useState(0);

  const handleClick = useCallback(() => {
    console.log("Clicked");
  }, []);

  return (
    <>
      <button onClick={() => setCount(count + 1)}>
        Count
      </button>

      <Child onClick={handleClick} />
    </>
  );
}
```

---

# Difference: useMemo vs useCallback

| Hook        | Caches   |
| ----------- | -------- |
| useMemo     | value    |
| useCallback | function |

---

# 7. useReducer()

Alternative to useState for complex state logic.

## When to use

Best for:

* Multiple related states
* Complex forms
* Shopping cart
* Authentication flows

---

## Example

```jsx
import { useReducer } from "react";

const initialState = { count: 0 };

function reducer(state, action) {
  switch (action.type) {
    case "increment":
      return { count: state.count + 1 };

    case "decrement":
      return { count: state.count - 1 };

    default:
      return state;
  }
}

function Counter() {
  const [state, dispatch] = useReducer(
    reducer,
    initialState
  );

  return (
    <>
      <h1>{state.count}</h1>

      <button onClick={() =>
        dispatch({ type: "increment" })
      }>
        +
      </button>
    </>
  );
}
```

---

# 8. useLayoutEffect()

Similar to useEffect but runs before browser paint.

## When to use

Use only when:

* Measuring DOM
* Preventing flicker
* Animations

Usually prefer `useEffect`.

---

# 9. useImperativeHandle()

Used with `forwardRef`.

Lets parent control child methods.

## When to use

Rare cases:

* Custom input controls
* Exposing methods

---

# 10. useId()

Generates unique IDs.

## When to use

Useful for:

* Accessibility
* Form labels

---

## Example

```jsx
import { useId } from "react";

function Login() {
  const id = useId();

  return (
    <>
      <label htmlFor={id}>Email</label>
      <input id={id} />
    </>
  );
}
```

---

# 11. Custom Hooks

Custom hooks are reusable logic functions.

## When to use

Whenever logic repeats across components.

---

## Example

```jsx
import { useState, useEffect } from "react";

function useWindowWidth() {
  const [width, setWidth] = useState(window.innerWidth);

  useEffect(() => {
    const handleResize = () => {
      setWidth(window.innerWidth);
    };

    window.addEventListener("resize", handleResize);

    return () => {
      window.removeEventListener("resize", handleResize);
    };
  }, []);

  return width;
}
```

Usage:

```jsx
function App() {
  const width = useWindowWidth();

  return <h1>{width}</h1>;
}
```

---

# Most Important Hooks in Real Projects

You’ll use these daily:

| Hook        | Usage Frequency |
| ----------- | --------------- |
| useState    | Very High       |
| useEffect   | Very High       |
| useContext  | High            |
| useRef      | High            |
| useReducer  | Medium          |
| useMemo     | Medium          |
| useCallback | Medium          |

---

# Real Project Examples

| Scenario              | Hook        |
| --------------------- | ----------- |
| Login form            | useState    |
| Fetch API             | useEffect   |
| Dark mode             | useContext  |
| Focus input           | useRef      |
| Shopping cart         | useReducer  |
| Large table filtering | useMemo     |
| Optimized callbacks   | useCallback |

---

# Common Mistakes Beginners Make

## 1. Infinite loop in useEffect

```jsx
useEffect(() => {
  setCount(count + 1);
}, [count]);
```

This keeps updating forever.

---

## 2. Overusing useMemo/useCallback

Do NOT optimize everything.

Use only when performance problems exist.

---

## 3. Mutating state directly

```js
// ❌ Wrong
state.name = "John";
```

Always create new state.

---

# Hook Lifecycle Understanding

## Component Mount

```jsx
useEffect(() => {
  console.log("Mounted");
}, []);
```

---

## Component Update

```jsx
useEffect(() => {
  console.log("Updated");
}, [count]);
```

---

## Component Unmount

```jsx
useEffect(() => {
  return () => {
    console.log("Unmounted");
  };
}, []);
```

---

# Recommended Learning Order

1. useState
2. useEffect
3. useContext
4. useRef
5. Custom Hooks
6. useReducer
7. useMemo
8. useCallback

---

# Best Practices

## Keep hooks simple

Bad:

```jsx
useEffect(() => {
  // 200 lines
}, []);
```

Good:

* split logic
* use custom hooks

---

## Prefer multiple states if unrelated

```jsx
const [name, setName] = useState("");
const [age, setAge] = useState(0);
```

---

## Use custom hooks for reuse

Instead of duplicating logic.

---

# Small Real App Example

```jsx
import { useState, useEffect } from "react";

function App() {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchUsers() {
      const res = await fetch(
        "https://jsonplaceholder.typicode.com/users"
      );

      const data = await res.json();

      setUsers(data);
      setLoading(false);
    }

    fetchUsers();
  }, []);

  if (loading) return <h1>Loading...</h1>;

  return (
    <ul>
      {users.map(user => (
        <li key={user.id}>
          {user.name}
        </li>
      ))}
    </ul>
  );
}
```

---

# Summary

| Hook         | Purpose         |
| ------------ | --------------- |
| useState     | Store state     |
| useEffect    | Side effects    |
| useContext   | Global data     |
| useRef       | DOM/reference   |
| useMemo      | Cache values    |
| useCallback  | Cache functions |
| useReducer   | Complex state   |
| useId        | Unique IDs      |
| Custom Hooks | Reusable logic  |

---

# Official Resources

* [React Official Website](https://react.dev?utm_source=chatgpt.com)
* [Hooks API Reference](https://react.dev/reference/react/hooks?utm_source=chatgpt.com)
* [Learn React Hooks](https://react.dev/learn?utm_source=chatgpt.com)
