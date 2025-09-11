import React, { useEffect, useState } from "react";
import ButtonGroup from 'react-bootstrap/ButtonGroup';
import Button from 'react-bootstrap/Button';

import ServerList from "./serverlist";   
import CreateServer from "./createserver";   




function Server() {

  const [activeTab, setActiveTab] = useState("serverlist");
return (
    <div className="container mt-5">
      <ButtonGroup aria-label="Basic example" className="mb-3">

        <Button
          variant={activeTab === "createserver" ? "primary" : "secondary"}
          onClick={() => setActiveTab("createserver")}
        >
          Create Server
        </Button>
        <Button
          variant={activeTab === "serverlist" ? "primary" : "secondary"}
          onClick={() => setActiveTab("serverlist")}
        >
          Server List
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