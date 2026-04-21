FROM node:lts-alpine

RUN apk update && apk upgrade

WORKDIR /frontend

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci

# Copy source code
COPY . .

# Run build at container startup to /output (clear old files first)
CMD ["sh", "-c", "rm -rf /app/output/* && npm run build -- --outDir=/app/output && tail -f /dev/null"]