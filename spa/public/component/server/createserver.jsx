import React, { useState } from "react";
import Col from "react-bootstrap/Col";
import FloatingLabel from "react-bootstrap/FloatingLabel";
import Form from "react-bootstrap/Form";
import Row from "react-bootstrap/Row";
import Button from "react-bootstrap/Button";

import { API_BASE } from '../../config.js';

function CreateServer() {
  const [formData, setFormData] = useState({
    name: "",
    provider: "aws",
    status: "active",
    ip_address: "",
    cpu_cores: "",
    ram_mb: "",
    storage_gb: "",
  });

  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    const { id, value } = e.target;
    setFormData((prev) => ({ ...prev, [id]: value }));
    setErrors((prev) => ({ ...prev, [id]: "" })); // clear field-specific error on change
    setErrors((prev) => ({ ...prev, form: "" })); // clear global error
  };

  const validate = () => {
    const newErrors = {};
    if (!formData.name) newErrors.name = "Server name is required";

    const ipRegex = /^(\d{1,3}\.){3}\d{1,3}$/;
    if (!ipRegex.test(formData.ip_address)) {
      newErrors.ip_address = "Enter a valid IP address (e.g., 192.168.0.1)";
    }

    if (formData.cpu_cores < 1 || formData.cpu_cores > 128) {
      newErrors.cpu_cores = "CPU cores must be between 1 and 128";
    }

    if (formData.ram_mb < 512 || formData.ram_mb > 1048576) {
      newErrors.ram_mb = "RAM must be between 512 MB and 1,048,576 MB";
    }

    if (formData.storage_gb < 10 || formData.storage_gb > 1048576) {
      newErrors.storage_gb = "Storage must be between 10 GB and 1,048,576 GB";
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!validate()) return;

    const cookies = document.cookie.split(";").reduce((acc, cookie) => {
      const [name, value] = cookie.trim().split("=");
      acc[name] = value;
      return acc;
    }, {});
    const token = cookies.authToken;

    setLoading(true);
    try {
      const response = await fetch(`${API_BASE}/servers`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });

      const data = await response.json();

      if (data.success === false) {
      // Only delete cookie if token expired
      if (data.message?.toLowerCase().includes("token") || data.code === "TOKEN_EXPIRED") {
        document.cookie = "authToken=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        setErrors((prev) => ({
          ...prev,
          form: "Session expired. Please log in again.",
        }));
        return;
      }

      // Other API errors — just show message
      setErrors((prev) => ({
        ...prev,
        form: data.data?.[0] || "Something went wrong!",
      }));
      return;
    }


      // Success
      setErrors({});
      alert(data.data[0] || "Server created successfully!");
      console.log("Response:", data);

    } catch (err) {
      console.log("Error:", err);
      setErrors({ form: "Error creating server. Please try again." });
    } finally {
      setLoading(false);
    }
  };

  return (
    <Form onSubmit={handleSubmit}>
      {errors.form && (
        <div className="text-danger mb-2">{errors.form}</div>
      )}

      <Row className="g-2">
        <Col md>
          <FloatingLabel controlId="name" label="Server Name">
            <Form.Control
              type="text"
              placeholder="Enter your server name"
              value={formData.name}
              onChange={handleChange}
              isInvalid={!!errors.name}
            />
            <Form.Control.Feedback type="invalid">
              {errors.name}
            </Form.Control.Feedback>
          </FloatingLabel>
        </Col>
        <Col md>
          <FloatingLabel controlId="provider" label="Select Provider">
            <Form.Select
              value={formData.provider}
              onChange={handleChange}
            >
              <option value="aws">AWS</option>
              <option value="digitalocean">DigitalOcean</option>
              <option value="vultr">Vultr</option>
              <option value="other">Other</option>
            </Form.Select>
          </FloatingLabel>
        </Col>
        <Col md>
          <FloatingLabel controlId="status" label="Status">
            <Form.Select value={formData.status} onChange={handleChange}>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="maintenance">Maintenance</option>
            </Form.Select>
          </FloatingLabel>
        </Col>
      </Row>

      <Row className="g-2 mt-2">
        <Col md>
          <FloatingLabel controlId="ip_address" label="IP Address">
            <Form.Control
              type="text"
              placeholder="192.168.0.1"
              value={formData.ip_address}
              onChange={handleChange}
              isInvalid={!!errors.ip_address}
            />
            <Form.Control.Feedback type="invalid">
              {errors.ip_address}
            </Form.Control.Feedback>
          </FloatingLabel>
        </Col>
        <Col md>
          <FloatingLabel controlId="cpu_cores" label="CPU Cores">
            <Form.Control
              type="number"
              placeholder="Between 1 and 128 cores"
              value={formData.cpu_cores}
              onChange={handleChange}
              isInvalid={!!errors.cpu_cores}
            />
            <Form.Control.Feedback type="invalid">
              {errors.cpu_cores}
            </Form.Control.Feedback>
          </FloatingLabel>
        </Col>
        <Col md>
          <FloatingLabel controlId="ram_mb" label="RAM (MB)">
            <Form.Control
              type="number"
              placeholder="Between 512 MB and 1,048,576 MB"
              value={formData.ram_mb}
              onChange={handleChange}
              isInvalid={!!errors.ram_mb}
            />
            <Form.Control.Feedback type="invalid">
              {errors.ram_mb}
            </Form.Control.Feedback>
          </FloatingLabel>
        </Col>
        <Col md>
          <FloatingLabel controlId="storage_gb" label="Storage (GB)">
            <Form.Control
              type="number"
              placeholder="Between 10 GB and 1,048,576 GB"
              value={formData.storage_gb}
              onChange={handleChange}
              isInvalid={!!errors.storage_gb}
            />
            <Form.Control.Feedback type="invalid">
              {errors.storage_gb}
            </Form.Control.Feedback>
          </FloatingLabel>
        </Col>
      </Row>

      <Button
        type="submit"
        variant="primary"
        className="mt-3"
        disabled={loading}
      >
        {loading ? "Creating..." : "Create Server"}
      </Button>
    </Form>
  );
}

export default CreateServer;
