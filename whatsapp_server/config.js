/**
 * config.js (ESM version)
 * Compatible with Node.js 18/20/22
 * Reads from environment variables for Railway/cloud deployment
 */
const config = {
  debug: process.env.DEBUG === 'true' || false,
  app_url: process.env.APP_URL || "http://localhost/",
  database: {
    connectionLimit: parseInt(process.env.DB_CONNECTION_LIMIT || '500'),
    host: process.env.DB_HOST || "localhost",
    user: process.env.DB_USERNAME || "[YOUR_DB_USERNAME]",
    password: process.env.DB_PASSWORD || "[YOUR_DB_PASSWORD]",
    database: process.env.DB_DATABASE || "[YOUR_DB_NAME]",
    port: parseInt(process.env.DB_PORT || '3306'),
    charset: "utf8mb4",
    debug: false,
    waitForConnections: true,
    multipleStatements: true,
  },
  cors: {
    origin: "*",
    optionsSuccessStatus: 200,
  },
};

export default config;
