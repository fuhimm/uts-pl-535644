# Stage 1: Build aplikasi Vue.js dengan Node.js
FROM node:20-alpine AS build

WORKDIR /app

ARG VITE_API_BASE_URL=/api
ENV VITE_API_BASE_URL=${VITE_API_BASE_URL}

# Salin package.json dan package-lock.json lebih dulu untuk layer caching
COPY package*.json ./
RUN npm ci

# Salin seluruh kode sumber frontend dan jalankan build produksi
COPY . .
RUN npm run build

# Stage 2: Sajikan hasil build statis menggunakan Nginx (tanpa node_modules)
FROM nginx:1.27-alpine

# Salin hanya hasil build (dist) dari Stage 1
COPY --from=build /app/dist /usr/share/nginx/html

# Salin konfigurasi Nginx (SPA fallback & reverse proxy /api ke backend:8000)
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
