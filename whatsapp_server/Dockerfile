FROM node:20-slim

# System dependencies for Baileys (WhatsApp)
RUN apt-get update && apt-get install -y \
    chromium \
    libatk1.0-0 libatk-bridge2.0-0 libcups2 libdrm2 \
    libxkbcommon0 libxcomposite1 libxdamage1 libxrandr2 \
    libgbm1 libpango-1.0-0 libasound2 \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Install dependencies
COPY package*.json yarn.lock* ./
RUN npm install --omit=dev

# Copy application
COPY . .

EXPOSE 8000

CMD ["node", "--no-warnings", "app.js"]
