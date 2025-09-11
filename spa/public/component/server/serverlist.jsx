import React, { useEffect, useState } from "react";
import Table from 'react-bootstrap/Table';
import { API_BASE } from '../../config.js'; // correct relative path

function ServerList() {
  const [servers, setServers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchServers = async () => {
      try {
        // ✅ Read token from cookie
        const cookies = document.cookie.split(";").reduce((acc, cookie) => {
          const [name, value] = cookie.trim().split("=");
          acc[name] = value;
          return acc;
        }, {});
        const token = cookies.authToken;

        if (!token) {
          setError("User not authenticated");
          setLoading(false);
          return;
        }

        // ✅ Fetch with Bearer token
        const response = await fetch(`${API_BASE}/servers`, {
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${token}`
          }
        });

        const data = await response.json();
        console.log(data);
        console.log(token);

        if (response.ok) {
          setServers(data); // assuming data is an array of servers
        } else {
          setError(data.message || "Failed to fetch servers");
        }
      } catch (err) {
        setError("Something went wrong");
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchServers();
  }, []);

  if (loading) return <p>Loading servers...</p>;
  if (error) return <p>{error}</p>;

  return (
    <Table striped bordered hover size="sm">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Provider</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        {servers.data.map((server, index) => (
          <tr key={server.id || index}>
            <td>{index + 1}</td>
            <td>{server.name}</td>
            <td>{server.provider}</td>
            <td>{server.status}</td>
          </tr>
        ))}
      </tbody>
    </Table>
  );
}

export default ServerList;
