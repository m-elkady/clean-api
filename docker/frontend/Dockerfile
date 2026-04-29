FROM node:lts-alpine

RUN apk update && apk upgrade

WORKDIR /frontend

# Copy package files
COPY package*.json ./

# Install all dependencies (including dev - needed for build)
RUN npm ci

# Copy source code
COPY . .

# Run build at container startup to /output (clear old files first)
CMD ["sh", "-c", "npm run build && tail -f /dev/null"]