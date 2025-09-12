import { useState, useEffect } from "react";
import Button from "react-bootstrap/Button";
import ButtonGroup from "react-bootstrap/ButtonGroup";

import ServerList from "./serverlist";   
import CreateServer from "./createserver"; 

function Server() {
  const [activeTab, setActiveTab] = useState("createserver");

  // Helper functions
  const setCookie = (name, value, days) => {
    let expires = "";
    if (days) {
      const date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
  };

  const getCookie = (name) => {
    const nameEQ = name + "=";
    const ca = document.cookie.split(";");
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) === " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  };

  useEffect(() => {
    const savedTab = getCookie("activeTab");
    if (savedTab) {
      setActiveTab(savedTab);
    }
  }, []);

  const handleTabChange = (tab) => {
    setActiveTab(tab);
    setCookie("activeTab", tab, 7); // valid for 7 days
  };

  return (
    <div className="container mt-5">
      <ButtonGroup aria-label="Basic example" className="mb-3">
        <Button
          variant={activeTab === "serverlist" ? "primary" : "secondary"}
          onClick={() => handleTabChange("serverlist")}
        >
          Server List
        </Button>
        <Button
          variant={activeTab === "createserver" ? "primary" : "secondary"}
          onClick={() => handleTabChange("createserver")}
        >
          Create Server
        </Button>
        
      </ButtonGroup>

      <div>
        {activeTab === "serverlist" && <ServerList />}
        {activeTab === "createserver" && <CreateServer />}
      </div>
    </div>
  );
}

export default Server;
