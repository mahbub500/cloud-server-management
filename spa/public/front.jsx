import React, { useState } from "react"; 
import ReactDOM from "react-dom/client";
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min';

import Button from 'react-bootstrap/Button';
import ButtonGroup from 'react-bootstrap/ButtonGroup';

import SignIn from "./component/signin";  
import SignUp from "./component/signup";  

function App() {
  const [activeTab, setActiveTab] = useState("signin"); // ✅ useState works now

  return (
    <div className="container mt-5">
      <ButtonGroup aria-label="Basic example" className="mb-3">
        <Button
          variant={activeTab === "signin" ? "primary" : "secondary"}
          onClick={() => setActiveTab("signin")}
        >
          Sign In
        </Button>
        <Button
          variant={activeTab === "signup" ? "primary" : "secondary"}
          onClick={() => setActiveTab("signup")}
        >
          Sign Up
        </Button>
      </ButtonGroup>

      <div>
        {activeTab === "signin" && <SignIn />}
        {activeTab === "signup" && <SignUp />}
      </div>
    </div>
  );
}

const root = ReactDOM.createRoot(document.getElementById("csm-root"));
root.render(<App />);
