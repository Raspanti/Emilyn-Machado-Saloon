FROM node:22-alpine
WORKDIR /opt/browser-sync
COPY package.json package-lock.json ./
RUN npm ci --omit=dev --ignore-scripts && npm cache clean --force
USER node
CMD ["node_modules/.bin/browser-sync", "start", "--config", "/config/bs-config.cjs"]
