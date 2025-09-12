import React, { useEffect, useState } from "react";
import Table from 'react-bootstrap/Table';
import Button from 'react-bootstrap/Button';
import Spinner from 'react-bootstrap/Spinner';
import { API_BASE } from '../../config.js';


function ServerList() {
  const [servers, setServers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  // Function to delete cookie
  const deleteCookie = (name) => {
    document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:00 GMT;';
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Are you sure you want to delete this server?")) return;

    try {
      const cookies = document.cookie.split(";").reduce((acc, cookie) => {
        const [name, value] = cookie.trim().split("=");
        acc[name] = value;
        return acc;
      }, {});
      const token = cookies.authToken;

      const response = await fetch(`${API_BASE}/servers/${id}`, {
        method: "DELETE",
        headers: { "Authorization": `Bearer ${token}` }
      });

      if (response.ok) {
        setServers(prev => prev.filter(s => s.id !== id)); // remove from UI
      } else {
        console.error("Failed to delete server");
      }
    } catch (err) {
      console.error(err);
    }
  };


  const fetchServers = async () => {
    setLoading(true);
    setError("");

    // Check cache first
    const cached = localStorage.getItem("servers");
    if (cached) {
      setServers(JSON.parse(cached));
      setLoading(false);
      return;
    }

    try {
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

      const response = await fetch(`${API_BASE}/servers`, {
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${token}`
        }
      });

      const data = await response.json();

      if (response.ok) {
        setServers(data);
        localStorage.setItem("servers", JSON.stringify(data)); // cache
      } else if (data.success === false) {
       
        if (data.data[0].toLowerCase().includes("token")) {
          setError(data.data || "Failed to fetch servers");
          deleteCookie('authToken');
          deleteCookie('isLoggedIn');
          window.location.reload();
        }
        
      }
    } catch (err) {
      // console.log( err.status );
      // console.log( err.data );
      setError("Something went wrong");
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchServers();
  }, []);

  const handleRefresh = () => {
    localStorage.removeItem("servers"); // clear cache
    fetchServers(); // fetch fresh data
  };

  if (loading) return (
    <div className="text-center mt-5">
      <Spinner animation="border" role="status">
        <span className="visually-hidden">Loading...</span>
      </Spinner>
    </div>
  );
  if (error) return <p className="text-danger">{error}</p>;

  return (
    <div>
      <Button
        variant="secondary"
        className="mb-3"
        onClick={handleRefresh}
        disabled={loading}
      >
        {loading ? (
          <>
            <Spinner
              as="span"
              animation="border"
              size="sm"
              role="status"
              aria-hidden="true"
            /> Refreshing...
          </>
        ) : "Refresh Server List"}
      </Button>

      <Table striped bordered hover size="sm">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Provider</th>
            <th>Ip Adress</th>
            <th>Cpu Core</th>
            <th>Ram</th>
            <th>Storage</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          {servers?.data?.map((server, index) => (
            <tr key={server.id}>
              <td>{server.id}</td>
              <td>{server.name}</td>
              <td>{server.provider}</td>
              <td>{server.ip_address}</td>
              <td>{server.cpu_cores}</td>
              <td>{server.ram_mb} MB</td>
              <td>{server.storage_gb} GB</td>
              <td>{server.status}</td>
              <td>
                <Button 
                  variant="warning" 
                  size="sm" 
                  className="me-2"
                  onClick={() => handleEdit(server.id)}
                >
                  Edit
                </Button>
                <Button 
                  variant="danger" 
                  size="sm"
                  onClick={() => handleDelete(server.id)}
                >
                  Delete
                </Button>
              </td>
            </tr>
          ))}
        </tbody>
      </Table>
    </div>
  );
}

export default ServerList;
