// import express from 'express';
// import http from 'http';
// import { Server } from 'socket.io';
// import cors from 'cors';

// // Initialize Express app
// const app = express();
// app.use(cors());

// // Create HTTP server
// const server = http.createServer(app);

// // Initialize Socket.IO
// const io = new Server(server, {
//     cors: {
//         origin: "*", // Allow all origins (adjust for production)
//         methods: ["GET", "POST"],
//     },
// });

// // Listen for client connections
// io.on('connection', (socket) => {
//     console.log('A user connected:', socket.id);

//     // Handle the joinRoom event
//     socket.on('joinRoom', (data) => {
//         const { room } = data;
//         socket.join(room); // Join the specific room
//         console.log(`User joined room: ${room}`);
//     });

//     // Handle the sendMessage event
//     socket.on('sendMessage', (data) => {
//         const { room, message, sender_id, to } = data;

//         console.log(`Message received in room ${room}:`, data);

//         // Emit the message only to the specific room
//         io.to(room).emit('receiveMessage', {
//             sender_id: sender_id,
//             receiver_id: to, // Include the receiver ID
//             message: message,
//             time: new Date().toLocaleTimeString(),
//         });
//     });

//     // Handle the sendNotification event
//     socket.on('sendNotification', (data) => {
//         const { message, type, userId } = data;

//         console.log(`Notification received:`, data);

//         // Emit the notification to a specific user or broadcast to all
//         if (userId) {
//             io.to(userId).emit('receiveNotification', {
//                 message: message,
//                 type: type || 'info',
//                 time: new Date().toLocaleTimeString(),
//             });
//         } else {
//             io.emit('receiveNotification', {
//                 message: message,
//                 type: type || 'info',
//                 time: new Date().toLocaleTimeString(),
//             });
//         }
//     });
// });

// // Start the server
// const PORT = 3000; // You can change the port if needed
// server.listen(PORT, () => {
//     console.log(`Server is running on http://localhost:${PORT}`);
// });



import express from 'express';
import http from 'http';
import { Server } from 'socket.io';
import cors from 'cors';
import moment from 'moment';
import dotenv from 'dotenv';

dotenv.config();  // Loads the environment variables from the .env file



// Initialize Express app
const app = express();
// Allow same origins as Socket.IO so /notify and other routes work from the browser
const corsOrigins = [
  "http://localhost",
  "http://localhost:80",
  "http://127.0.0.1",
  "http://localhost/stockmgnt/public",
];
app.use(cors({ origin: corsOrigins, credentials: true }));
app.use(express.json());

// Create HTTP server
const server = http.createServer(app);

// Initialize Socket.IO
/*const io = new Server(server, {
    cors: {
        origin: "https://auction.itexpertiseindia.com:8081",
        // origin: "https://localhost:3050",
        methods: ["GET", "POST"],
         credentials: true
    },
});*/
const allowedOrigins = [
  "https://auction.itexpertiseindia.com:8081",
  "https://localhost:3050",
  "http://localhost",
  "http://localhost:80",
  "http://127.0.0.1",
  "http://127.0.0.1:80",
  "http://localhost/stockmgnt/public",
];

const io = new Server(server, {
  cors: {
    origin: (origin, callback) => {
      // Allow requests with no origin (e.g. Postman, same-origin) or from allowed list
      if (!origin || allowedOrigins.includes(origin)) {
        callback(null, true);
      } else {
        callback(new Error("Not allowed by CORS"));
      }
    },
    methods: ["GET", "POST"],
    credentials: true,
  },
});

// Track connected users for notifications
const connectedUsers = {};

// Handle socket connections
io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);

    // Register the user for notifications
    socket.on('register-user', (userId) => {
        connectedUsers[userId] = socket.id;
        console.log(`User ${userId} registered with socket ID ${socket.id}`);
    });

    // Handle user disconnect
    socket.on('disconnect', () => {
        for (const id in connectedUsers) {
            if (connectedUsers[id] === socket.id) {
                delete connectedUsers[id];
                console.log(`User ${id} disconnected`);
                break;
            }
        }
    });

    // Join a room for chat
    socket.on('joinRoom', (data) => {
        const { room } = data;
        socket.join(room);
        console.log(`User joined room: ${room}`);
    });

    // Send chat message to room
    socket.on('sendMessage', (data) => {
        const { room, message, sender_id, to } = data;
        console.log(`Message in room ${room}:`, data);

        io.to(room).emit('receiveMessage', {
            sender_id,
            receiver_id: to,
            message,
            time: new Date().toLocaleTimeString(),
        });
    });

    // Send notification to specific user or broadcast
    socket.on('sendNotification', (data) => {
        const { message, type, userId } = data;
        console.log(`Notification received:`, data);

        const targetSocket = connectedUsers[userId];
        const payload = {
            message,
            type: type || 'info',
            time: new Date().toLocaleTimeString(),
        };

        if (userId && targetSocket) {
            io.to(targetSocket).emit('receiveNotification', payload);
        } else {
            io.emit('receiveNotification', payload);
        }
    });
});
// HTTP endpoint to send notification
app.post('/notify', (req, res) => {
    const { userId, message, user_name, time_ago } = req.body;

    const socketId = connectedUsers[userId];
    const notification = {
        user_name: user_name || 'System',
        notification_text: message,
        time_ago: moment().fromNow(),
    };

    if (socketId) {
        io.to(socketId).emit('notification', notification);
        console.log(`Notification sent to user ${userId}:`, notification);
    } else {
        console.log(`User ${userId} not connected`);
    }

    res.sendStatus(200);
});
app.get('/test', (req, res) => {
    res.json({ message: 'Hello from the API!' });
});
// Start server on a single port
const PORT = process.env.NOTIFICATION_PORT;
server.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});