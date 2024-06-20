FROM node:lts-alpine AS base

RUN apk update && apk upgrade

WORKDIR /app

# Copy package.json and package-lock.json
COPY ../../frontend/package*.json .

# Install dependencies
RUN npm install

# Copy the rest of the application
COPY . .

ENV PATH ../../frontend/node_modules/.bin/:$PATH

# RUN chown -R node /app

USER node

FROM base AS build

# Build the application
RUN npm run build