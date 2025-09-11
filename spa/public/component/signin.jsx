import React, { useState, useEffect } from "react";
import Button from 'react-bootstrap/Button';
import Form from 'react-bootstrap/Form';
import { API_BASE } from '../config.js';

function SignIn() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [message, setMessage] = useState("");
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  // Check if user is already logged in
  useEffect(() => {
    const cookies = document.cookie.split(";").reduce((acc, cookie) => {
      const [name, value] = cookie.trim().split("=");
      acc[name] = value;
      return acc;
    }, {});
    if (cookies.authToken && cookies.isLoggedIn === "true") {
      setIsLoggedIn(true);
    }
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      const response = await fetch(`${API_BASE}/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (response.ok) {
        // Save token and login flag in cookies (7 days)
        document.cookie = `authToken=${data.data.token}; path=/; max-age=${7*24*60*60}`;
        document.cookie = `isLoggedIn=true; path=/; max-age=${7*24*60*60}`;

        setIsLoggedIn(true);
        setMessage(`Login successful! Welcome ${data.data.username}`);
      } else {
        setMessage(data.data?.message || "Login failed");
      }
    } catch (error) {
      console.error(error);
      setMessage("Something went wrong");
    }
  };

  // Hide the component if user is already logged in
  if (isLoggedIn) {
    return <p>You are already logged in.</p>;
  }

  return (
    <Form onSubmit={handleSubmit}>
      <h1>Sign In</h1>

      <Form.Group className="mb-3" controlId="formBasicEmail">
        <Form.Label>Email address</Form.Label>
        <Form.Control
          type="email"
          placeholder="Enter email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          required
        />
      </Form.Group>

      <Form.Group className="mb-3" controlId="formBasicPassword">
        <Form.Label>Password</Form.Label>
        <Form.Control
          type="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          required
        />
      </Form.Group>

      <Button variant="primary" type="submit">
        Sign In
      </Button>

      {message && <p className="mt-3">{message}</p>}
    </Form>
  );
}

export default SignIn;
