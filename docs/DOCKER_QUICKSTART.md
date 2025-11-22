# 🐳 Docker Quick Start

**Setup Docker untuk CI3 dengan MySQL Container yang Sudah Ada**

---

## ✅ Prerequisites Check

Pastikan MySQL container sudah running:

```bash
docker ps | grep mysql
```

**Expected Output**:
```
8ad13db3e265   mysql:8.0   ...   0.0.0.0:3306->3306/tcp   mysql
```

---

## 🚀 Quick Start (3 Commands)

```bash
# 1. Build image
docker-compose build apotek-ci3

# 2. Start container
docker-compose up -d apotek-ci3

# 3. Check status
docker-compose ps
```

**Application URL**: `http://localhost:8081`

---

## 🧪 Test API

```bash
curl -X GET "http://localhost:8081/api/v1/sales/summary/daily?date=2025-02-21" \
  -H "X-API-KEY: sk_n8n_apotek_2025_test_key_12345"
```

---

## 🔧 Configuration

### Database Connection

Container akan connect ke MySQL yang sudah ada di host:

- **Host**: `host.docker.internal` (dari dalam container)
- **Port**: `3306`
- **User**: `root`
- **Password**: `dawamr`
- **Database**: `apotek_db`

### How It Works

- Docker menggunakan `host.docker.internal` untuk akses host machine
- MySQL container expose port 3306 ke host
- CI3 container connect via `host.docker.internal:3306`

---

## 📊 Docker Compose Structure

```yaml
services:
  apotek-ci3:
    ports:
      - "8081:80"
    environment:
      - DB_HOST=host.docker.internal
      - DB_USER=root
      - DB_PASS=dawamr
      - DB_NAME=apotek_db
    extra_hosts:
      - "host.docker.internal:host-gateway"
```

**No MySQL service needed** - menggunakan container yang sudah ada!

---

## 🔍 Useful Commands

### Container Management

```bash
# Start
docker-compose up -d apotek-ci3

# Stop
docker-compose stop apotek-ci3

# Restart
docker-compose restart apotek-ci3

# Logs
docker-compose logs -f apotek-ci3

# Remove
docker-compose down
```

### Debug

```bash
# Check container status
docker ps

# Access container shell
docker exec -it apotek-ci3-app bash

# Test MySQL connection from container
docker exec -it apotek-ci3-app bash
ping -c 3 host.docker.internal
mysql -h host.docker.internal -u root -p
```

---

## 🐛 Troubleshooting

### Cannot connect to MySQL

**Test connection**:
```bash
docker exec -it apotek-ci3-app bash
mysql -h host.docker.internal -u root -p apotek_db
```

If fails:
```bash
# Check MySQL accessible from host
mysql -h 127.0.0.1 -P 3306 -u root -p

# Check MySQL port
docker ps | grep mysql

# Restart apotek container
docker-compose restart apotek-ci3
```

### Port 8081 already in use

```bash
# Check what's using port
lsof -i :8081

# Kill the process or change port
# Edit docker-compose.yml: "8082:80"
```

---

## ✅ Verification Checklist

- [ ] MySQL container running (`docker ps`)
- [ ] Build apotek-ci3 image
- [ ] Start apotek-ci3 container
- [ ] Access http://localhost:8081 (homepage loads)
- [ ] Test API endpoint (returns JSON)
- [ ] Check logs (no database errors)

---

## 📝 Environment Variables

Container menggunakan environment variables untuk database:

```bash
DB_HOST=host.docker.internal
DB_USER=root
DB_PASS=dawamr
DB_NAME=apotek_db
```

Untuk local development tanpa Docker:
```bash
DB_HOST=localhost
```

---

## 🎯 Summary

✅ **Tidak perlu start MySQL container baru**
✅ **Menggunakan MySQL yang sudah ada** (port 3306)
✅ **CI3 app berjalan di port 8081**
✅ **Connection via `host.docker.internal`**

---

**Ready to build and run!** 🚀
