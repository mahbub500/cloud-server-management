# 🌥️ Cloud Server Management – WordPress Plugin

A WordPress plugin to manage cloud servers (AWS, DigitalOcean, Vultr, and more) via REST API.  
Easily create, list, edit, and delete servers using authentication tokens.  

## 🚀 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/mahbub500/cloud-server-managment.git
   cd cloud-server-managment
   ```

2. Install dependencies:
   ```bash
   composer update
   ```

3. Activate the plugin from the WordPress dashboard.

---

## 🔐 Authentication

### 1️⃣ Signup (New User)
**Endpoint:**
```
POST {siteurl}/wp-json/csm/v1/signup
```

**Response:**
- If successful → user registration confirmation.
- If user already exists → proceed to signin.

### 2️⃣ Signin (Generate Token)
**Endpoint:**
```
POST {siteurl}/wp-json/csm/v1/sign
```

**Response Example:**
```json
{
  "success": true,
  "user_id": 0,
  "token": "your_generated_token"
}
```
🔑 Copy the token — it is valid for **1 hour**.  
Include it in headers for all requests:

```
Authorization: Bearer your_generated_token
```

---

## ⚡ Server Management

### 3️⃣ Create Server
**Endpoint:**
```
POST {siteurl}/wp-json/csm/v1/servers
```

**Headers:**
```
Authorization: Bearer your_generated_token
```

**Fields:**

| Field       | Type   | Example        | Rules                             |
| ----------- | ------ | -------------- | --------------------------------- |
| name        | string | `server-1`     | required                          |
| ip_address  | string | `192.168.0.10` | required                          |
| provider    | string | `aws`          | (aws, digitalocean, vultr, other) |
| status      | string | `active`       | (active, inactive, maintenance)   |
| cpu_cores   | int    | `4`            | range: 1–128                      |
| ram_mb      | int    | `8192`         | range: 512–1048576                |
| storage_gb  | int    | `100`          | range: 10–1048576                 |

**Success Response:**
```json
{
  "success": true,
  "message": "Server created successfully."
}
```

---

### 4️⃣ List Servers
**Endpoint:**
```
GET {siteurl}/wp-json/csm/v1/servers
```

**Headers:**
```
Authorization: Bearer your_generated_token
```

**Response Example:**
```json
[
  {
    "id": "1",
    "name": "server-1",
    "ip_address": "192.168.0.10",
    "provider": "aws",
    "status": "active",
    "cpu_cores": "4",
    "ram_mb": "8192",
    "storage_gb": "100",
    "created_at": "2025-09-12 08:44:13"
  }
]
```

---

### 5️⃣ Edit Server
**Endpoint:**
```
PUT {siteurl}/wp-json/csm/v1/servers/{id}
```

**Headers:**
```
Authorization: Bearer your_generated_token
```

**Request Example:**
```json
{
  "name": "updated-server",
  "status": "maintenance"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Server updated successfully."
}
```

---

### 6️⃣ Delete Server(s)
**Endpoint:**
```
DELETE {siteurl}/wp-json/csm/v1/servers/{id}
DELETE {siteurl}/wp-json/csm/v1/servers/{id1,id2,id3}
```

✅ Supports deleting multiple IDs at once (comma-separated).  

**Headers:**
```
Authorization: Bearer your_generated_token
```

**Response:**
```json
{
  "success": true,
  "message": "Selected server(s) deleted successfully."
}
```
