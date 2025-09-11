import React, { useState, useEffect } from "react";
import Button from 'react-bootstrap/Button';
import Form from 'react-bootstrap/Form';
import { API_BASE } from '../config.js';

import Server from "./server/server";  

function SignIn({ setIsLoggedIn }) {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [message, setMessage] = useState("");

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

        setIsLoggedIn(true); // ✅ Show <Server /> component
        setMessage(`Login successful! Welcome ${data.data.username}`);
      } else {
        setMessage(data.data || "Login failed");
      }
    } catch (error) {
      console.error(error);
      setMessage("Something went wrong");
    }
  };

  return (
    <Form onSubmit={handleSubmit}>
      <h1>Sign In</h1>
      <Form.Group className="mb-3">
        <Form.Label>Email address</Form.Label>
        <Form.Control type="email" placeholder="Enter email" value={email} onChange={e => setEmail(e.target.value)} required />
      </Form.Group>
      <Form.Group className="mb-3">
        <Form.Label>Password</Form.Label>
        <Form.Control type="password" placeholder="Password" value={password} onChange={e => setPassword(e.target.value)} required />
      </Form.Group>
      <Button variant="primary" type="submit">Sign In</Button>
      {message && <p className="mt-3">{message}</p>}
    </Form>
  );
}

export default SignIn;
