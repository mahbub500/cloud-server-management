import React from "react";
import ReactDOM from "react-dom/client";
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min';

import SignIn from "./component/signin";  
import SignUp from "./component/signup";  

function App() {
  return (
    <div className="container mt-5">
      <h1 className="text-primary">Hello React + Bootstrap</h1>
      <SignIn />
      <SignUp />
    </div>
  );
}

const root = ReactDOM.createRoot(document.getElementById("csm-root"));
root.render(<App />);
