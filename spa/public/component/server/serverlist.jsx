import React, { useEffect, useState } from "react";
import Table from 'react-bootstrap/Table';
import Button from 'react-bootstrap/Button';
import Spinner from 'react-bootstrap/Spinner';
import { API_BASE } from '../../config.js';

function ServerList() {
  const [servers, setServers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [selected, setSelected] = useState([]);
  const [deleting, setDeleting] = useState(false);

  const deleteCookie = (name) => {
    document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:00 GMT;';
  };

  const handleEdit = (id) => {
    alert(`Edit server ${id}`);
  };

  const toggleSelect = (id) => {
    setSelected(prev =>
      prev.includes(id) ? prev.filter(sid => sid !== id) : [...prev, id]
    );
  };

  const toggleSelectAll = () => {
    if (selected.length === servers.length) {
      setSelected([]);
    } else {
      setSelected(servers.map(s => s.id));
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Are you sure you want to delete this server?")) return;

    try {
      const cookies = Object.fromEntries(
        document.cookie.split(";").map(c => c.trim().split("="))
      );
      const token = cookies.authToken;

      const response = await fetch(`${API_BASE}/servers/${id}`, {
        method: "DELETE",
        headers: { "Authorization": `Bearer ${token}` }
      });

      if (response.ok) {
        setServers(prev => prev.filter(s => s.id !== id));
        setSelected(prev => prev.filter(sid => sid !== id));
      }
    } catch (err) {
      console.error(err);
    }
  };

  const handleBulkDelete = async () => {
    if (selected.length === 0) return;
    if (!window.confirm("Are you sure you want to delete selected servers?")) return;

    setDeleting(true);
    try {
      const cookies = Object.fromEntries(
        document.cookie.split(";").map(c => c.trim().split("="))
      );
      const token = cookies.authToken;

      const idsParam = selected.join(","); // -> "1,2,3"
      const response = await fetch(`${API_BASE}/servers/${idsParam}`, {
        method: "DELETE",
        headers: { "Authorization": `Bearer ${token}` }
      });

      if (response.ok) {
        setServers(prev => prev.filter(s => !selected.includes(s.id)));
        setSelected([]);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setDeleting(false);
    }
  };

  const fetchServers = async () => {
    setLoading(true);
    setError("");

    try {
      const cookies = Object.fromEntries(
        document.cookie.split(";").map(c => c.trim().split("="))
      );
      const token = cookies.authToken;

      if (!token) {
        setError("User not authenticated");
        setLoading(false);
        return;
      }

      const response = await fetch(`${API_BASE}/servers`, {
        headers: { "Authorization": `Bearer ${token}` }
      });

      const data = await response.json();

      if (response.ok) {
        setServers(data.data || []);
      } else if (data.success === false) {
        if (data.data?.[0]?.toLowerCase().includes("token")) {
          setError(data.data || "Failed to fetch servers");
          deleteCookie("authToken");
          deleteCookie("isLoggedIn");
          window.location.reload();
        }
      }
    } catch (err) {
      setError("Something went wrong");
    } finally {
      setLoading(false);
    }
  };

  const handleRefresh = () => {
    fetchServers();
  };

  useEffect(() => {
    fetchServers();
  }, []);

  if (loading) return (
    <div className="text-center mt-5">
      <Spinner animation="border" />
    </div>
  );
  if (error) return <p className="text-danger">{error}</p>;

  return (
    <div>
      <div className="mb-3 d-flex ">
        <Button variant="primary " className="gap-2" onClick={handleRefresh}>
          Refresh
        </Button>
        <Button 
          variant="danger" 
          disabled={selected.length === 0 || deleting} 
          onClick={handleBulkDelete}
        >
          {deleting ? "Deleting..." : `Delete All (${selected.length})`}
        </Button>
      </div>

      <Table striped bordered hover size="sm">
        <thead>
          <tr>
            <th>
              <input
                type="checkbox"
                checked={selected.length === servers.length && servers.length > 0}
                onChange={toggleSelectAll}
              />
            </th>
            <th>#</th>
            <th>Name</th>
            <th>Provider</th>
            <th>IP Address</th>
            <th>CPU Cores</th>
            <th>RAM</th>
            <th>Storage</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          {servers.map((server, index) => (
            <tr key={server.id}>
              <td>
                <input
                  type="checkbox"
                  checked={selected.includes(server.id)}
                  onChange={() => toggleSelect(server.id)}
                />
              </td>
              <td>{index + 1}</td>
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
