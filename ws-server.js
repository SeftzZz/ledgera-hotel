const WebSocket = require('ws');
const express = require('express');
const mysql = require('mysql2/promise');
const jwt = require('jsonwebtoken');
const dotenv = require('dotenv');
const path = require('path');
const cors = require('cors');

/**
 * =========================
 * LOAD ENV CI4 🔥
 * =========================
 */
dotenv.config({
  path: path.resolve(__dirname, '.env')
});

const JWT_SECRET = process.env.JWT_SECRET;

if (!JWT_SECRET) {
  throw new Error('JWT_SECRET not found in CI4 .env');
}

console.log('JWT Loaded from CI4 .env');

/**
 * =========================
 * DB
 * =========================
 */
const db = mysql.createPool({
  host: 'localhost',
  user: 'root',
  password: 'Salam123!',
  database: 'ledgera',
  waitForConnections: true,
  connectionLimit: 10
});

const app = express();

app.use(cors({
  origin: '*',
  methods: ['GET', 'POST', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization']
}));

app.use(express.json());

/**
 * =========================
 * WEBSOCKET SERVER
 * =========================
 */
const wss = new WebSocket.Server({
  port: 4002,
  host: '0.0.0.0'
});

console.log('WebSocket server running on port 4002');

/**
 * =========================
 * HEARTBEAT 🔥
 * =========================
 */
function heartbeat() {
  this.isAlive = true;
}

wss.on('connection', (ws, req) => {
  try {
    const url = new URL(req.url, 'http://localhost');
    const token = url.searchParams.get('token');

    if (!token) {
      console.log('No token, disconnect');
      ws.close();
      return;
    }

    // 🔥 VERIFY JWT
    const decoded = jwt.verify(token, JWT_SECRET);
    console.log(decoded);

    ws.userId   = decoded.data.id;
    ws.branchId = decoded.data.branch_id;

    ws.isAlive = true;

    console.log('WS connected:', ws.userId, 'branch:', ws.branchId);

    ws.on('pong', heartbeat);

    ws.on('close', () => {
      console.log('Client disconnected:', ws.userId);
    });

    ws.on('error', (err) => {
      console.log('WS error:', err.message);
    });

    ws.send(JSON.stringify({
      type: 'connected',
      user_id: ws.userId,
      branch_id: ws.branchId
    }));

  } catch (err) {
    console.log('Invalid token:', err.message);
    ws.close();
  }
});

/**
 * 🔥 Ping tiap 30 detik
 */
setInterval(() => {
  wss.clients.forEach((ws) => {
    if (ws.isAlive === false) {
      console.log('Terminating dead client:', ws.userId);
      return ws.terminate();
    }

    ws.isAlive = false;
    ws.ping();
  });
}, 30000);

/**
 * =========================
 * BROADCAST PER BRANCH 🔥
 * =========================
 */
function broadcastToBranch(branchId, payload) {
  const message = JSON.stringify(payload);

  console.log('Broadcast to branch:', branchId);

  wss.clients.forEach(client => {
    if (
      client.readyState === WebSocket.OPEN &&
      client.branchId == branchId
    ) {
      client.send(message);
    }
  });
}

/**
 * =========================
 * HTTP ENDPOINT (CI4 / FRONTEND → WS)
 * =========================
 */
app.post('/emit', (req, res) => {
  const { branch_id, type } = req.body;

  console.log('📡 EMIT:', req.body);
  
  if (!type) {
    return res.status(400).json({ message: 'Invalid payload' });
  }

  if (branch_id) {
    broadcastToBranch(branch_id, req.body); // 🔥 FIX DI SINI
  } else {
    wss.clients.forEach(client => {
      if (client.readyState === WebSocket.OPEN) {
        client.send(JSON.stringify(req.body));
      }
    });
  }

  return res.json({ success: true });
});

/**
 * =========================
 * HTTP SERVER (PORT 4003)
 * =========================
 */
app.listen(4003, () => {
  console.log('WS HTTP bridge running on port 4003');
});