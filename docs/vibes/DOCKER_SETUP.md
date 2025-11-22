# 🐳 Docker Setup Guide

**Setup Docker untuk Sistem Informasi Apotek CI3 dengan PHP 7.4**

---

## 📋 Prerequisites

- Docker installed
- Docker Compose installed
- MySQL container sudah running (port 3306)

---

## 🚀 Quick Start

### 1. Build Docker Image

```bash
cd /Users/dawamraja/Downloads/apotek-ci3-n8n

# Build image
docker-compose build
```

### 2. Start Container

```bash
# Start container
docker-compose up -d apotek-ci3

# Check status
docker-compose ps
```

### 3. Access Application

**URL**: `http://localhost:8081`

**API Endpoints**: `http://localhost:8081/api/v1/...`

---

## 🔧 Configuration

### Database Connection

Application akan otomatis connect ke MySQL container yang sudah ada:

- **Host**: `mysql` (container name)
- **Port**: `3306`
- **User**: `root`
- **Password**: `dawamr`
- **Database**: `apotek_db`

### Environment Variables

File `application/config/database.php` sudah dikonfigurasi untuk:
- Docker: hostname = `mysql`
- Local: hostname = `localhost`

---

## 📊 Docker Setup Details

### Dockerfile

**Base Image**: `php:7.4-apache`

**Extensions Installed**:
- mysqli
- pdo_mysql
- zip

**Apache Modules**:
- mod_rewrite (enabled)
- headers (enabled)

### Docker Compose

**Services**:
1. **apotek-ci3** - CI3 Application
   - Port: 8081:80
   - PHP 7.4 + Apache
   
2. **mysql** - Database (existing container)
   - Port: 3306:3306
   - MySQL 8.0

**Network**: `apotek-network` (bridge)

---

## 🧪 Testing API

### Using cURL

```bash
# Test Sales Summary
curl -X GET "http://localhost:8081/api/v1/sales/summary/daily?date=2025-02-21" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"

# Test Stock Check
curl -X GET "http://localhost:8081/api/v1/stock/check?q=paracetamol" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

### Using Postman

1. Import collection: `docs/postman_collection.json`
2. Update `base_url` variable to: `http://localhost:8081`
3. Run requests

---

## 🔍 Useful Commands

### Container Management

```bash
# Start container
docker-compose up -d apotek-ci3

# Stop container
docker-compose stop apotek-ci3

# Restart container
docker-compose restart apotek-ci3

# View logs
docker-compose logs -f apotek-ci3

# Remove container
docker-compose down apotek-ci3
```

### Access Container Shell

```bash
# Bash into container
docker exec -it apotek-ci3-app bash

# Check PHP version
docker exec apotek-ci3-app php -v

# Check Apache status
docker exec apotek-ci3-app apache2ctl -v
```

### Database Access from Container

```bash
# Access MySQL from apotek-ci3 container
docker exec -it apotek-ci3-app bash
mysql -h mysql -u root -p apotek_db
# Password: dawamr
```

---

## 📝 File Structure

```
apotek-ci3-n8n/
├── Dockerfile                 # PHP 7.4 + Apache setup
├── docker-compose.yml         # Docker services configuration
├── .env.example              # Environment variables template
├── .htaccess                 # Apache rewrite rules
├── application/
│   └── config/
│       └── database.php      # Database config (Docker-ready)
└── docs/
    └── DOCKER_SETUP.md       # This file
```

---

## 🐛 Troubleshooting

### "Connection refused" to MySQL

**Cause**: Container tidak bisa connect ke MySQL

**Solution**:
```bash
# Check MySQL container running
docker ps | grep mysql

# Check network
docker network ls
docker network inspect apotek-network

# Restart both containers
docker-compose restart
```

### "Table not found"

**Cause**: Database belum di-seed

**Solution**:
```bash
# Import schema dari host
mysql -h 127.0.0.1 -P 3306 -u root -p apotek_db < docs/database_schema.sql

# Seed data
mysql -h 127.0.0.1 -P 3306 -u root -p apotek_db < docs/database_seeder.sql
```

### Port 8081 already in use

**Cause**: Port sudah digunakan aplikasi lain

**Solution**:
```bash
# Check what's using port 8081
lsof -i :8081

# Stop the process or change port in docker-compose.yml
# Change "8081:80" to "8082:80"
```

### Apache permissions error

**Cause**: File permissions tidak sesuai

**Solution**:
```bash
# Fix permissions from container
docker exec -it apotek-ci3-app bash
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/application/cache
chmod -R 755 /var/www/html/application/logs
```

---

## 🔄 Development Workflow

### Local Development

1. Edit files locally (changes reflected immediately via volume mount)
2. Refresh browser to see changes
3. No need to rebuild container for code changes

### Rebuild After Dockerfile Changes

```bash
# Stop container
docker-compose down apotek-ci3

# Rebuild
docker-compose build --no-cache apotek-ci3

# Start again
docker-compose up -d apotek-ci3
```

---

## 📊 Container Info

### PHP Configuration

```bash
# Check PHP info
docker exec apotek-ci3-app php -i

# Check loaded extensions
docker exec apotek-ci3-app php -m

# Check PHP version
docker exec apotek-ci3-app php -v
```

**Expected Output**:
```
PHP 7.4.x (cli)
```

### Apache Configuration

**Document Root**: `/var/www/html`

**Virtual Host**: 
- Port: 80 (mapped to 8081)
- AllowOverride: All
- Rewrite: Enabled

---

## ✅ Setup Checklist

- [ ] Docker & Docker Compose installed
- [ ] MySQL container running
- [ ] Build Docker image
- [ ] Start apotek-ci3 container
- [ ] Database schema imported
- [ ] Database seeded
- [ ] Test API endpoint
- [ ] Verify authentication
- [ ] Check logs for errors

---

## 🚀 Production Notes

Untuk production deployment:

1. **Update Environment**:
   - Change ENVIRONMENT to 'production' in `index.php`
   - Disable error display

2. **Security**:
   - Change API keys
   - Update MySQL password
   - Enable HTTPS

3. **Performance**:
   - Enable OPcache
   - Configure Apache for production
   - Add caching layer

4. **Monitoring**:
   - Setup logging
   - Monitor container health
   - Setup alerts

---

**Last Updated**: 2025-02-21  
**PHP Version**: 7.4  
**Port**: 8081  
**Status**: ✅ Ready for Development
