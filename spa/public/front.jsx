import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter as Router, Routes, Route, Link, Navigate } from "react-router-dom";
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min';

import SignIn from "./component/signin";
import SignUp from "./component/signup";
import Server from "./component/server/server"; 
import EditServer from "./component/server/editserver"; 
import Button from 'react-bootstrap/Button';
import ButtonGroup from 'react-bootstrap/ButtonGroup';

function App() {
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  // Check login status on mount
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

  return (
    <Router>
      <div className="container mt-5">
        {/* Navigation */}
        {!isLoggedIn && (
          <ButtonGroup aria-label="Auth Navigation" className="mb-3">
            <Link to="/signin">
              <Button variant="primary">Sign In</Button>
            </Link>
            <Link to="/signup">
              <Button variant="secondary">Sign Up</Button>
            </Link>
          </ButtonGroup>
        )}

        {/* Routes */}
        <Routes>
          {/* Public routes */}
          {!isLoggedIn && (
            <>
              <Route path="/signin" element={<SignIn setIsLoggedIn={setIsLoggedIn} />} />
              <Route path="/signup" element={<SignUp />} />
              <Route path="*" element={<Navigate to="/signin" />} />
            </>
          )}

          {/* Private routes */}
          {isLoggedIn && (
            <>
              <Route path="/servers" element={<Server />} />
              <Route path="*" element={<Navigate to="/servers" />} />
              <Route path="/servers/edit/:id" element={<EditServer />} />
            </>
          )}
        </Routes>
      </div>
    </Router>
  );
}

const root = ReactDOM.createRoot(document.getElementById("csm-root"));
root.render(<App />);
