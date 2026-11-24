
// notification.js
import express from 'express';
import { Server } from 'socket.io';
import http from 'http';
import cors from 'cors';
import moment from 'moment';  // Add moment for dynamic time formatting

// Initialize the Express app
const app = express();
const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: '*',
  }
});

app.use(cors());
app.use(express.json());

// Track connected users
const connectedUsers = {};

// When a user connects, register their userId and socketId
io.on('connection', socket => {
  // Register the user by userId
  socket.on('register-user', userId => {
    connectedUsers[userId] = socket.id;
    console.log(`User ${userId} connected with socket ID ${socket.id}`);
  });

  // When a user disconnects, remove their userId and socketId from the connected users
  socket.on('disconnect', () => {
    for (const id in connectedUsers) {
      if (connectedUsers[id] === socket.id) {
        delete connectedUsers[id];
        console.log(`User ${id} disconnected`);
        break;
      }
    }
  });
});

// Accept HTTP POST requests to send a notification
app.post('/notify', (req, res) => {
  const { userId, message, user_name, time_ago } = req.body;

  const socketId = connectedUsers[userId];

  if (socketId) {
    // Dynamically fetch user_name and time_ago
    const notification = {
      user_name: user_name || 'System',  // Default to 'System' if no user_name provided
      notification_text: message,
      time_ago: moment().fromNow(),  // Use moment.js to get a dynamic relative time like 'Just now', 'a minute ago', etc.
    };

    io.to(socketId).emit('notification', notification); // Emit notification to the specific user
    console.log(`Notification sent to user ${userId}:`, notification);
  } else {
    console.log(`User ${userId} not connected`);
  }

  res.sendStatus(200); // Respond with a success status
});

// Start the server on port 6002
server.listen(6002, () => {
  console.log('Notification server running on port 6002');
});
