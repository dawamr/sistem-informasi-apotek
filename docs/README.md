# 📚 Dokumentasi Sistem Informasi Apotek CI3

Selamat datang! Folder ini berisi semua dokumentasi untuk project Sistem Informasi Apotek berbasis CodeIgniter 3.

---

## 📖 Panduan Membaca Dokumentasi

### 🚀 Untuk Memulai (Start Here)

1. **[SETUP_SUMMARY.md](./SETUP_SUMMARY.md)** ⭐
   - Ringkasan project
   - Arsitektur sistem
   - Tech stack
   - Struktur folder
   - **Baca ini terlebih dahulu!**

2. **[TODO.md](./TODO.md)** 📋
   - Daftar lengkap tasks (33 items)
   - Breakdown per phase
   - Effort estimate
   - Dependencies
   - **Gunakan ini sebagai checklist**

3. **[PROGRESS.md](./PROGRESS.md)** 📊
   - Status tracking
   - Timeline
   - Milestone tracking
   - **Update saat progress berubah**

---

### 📚 Dokumentasi Detail

4. **[project-overview.md](./project-overview.md)**
   - Tujuan aplikasi
   - Tech stack lengkap
   - Modul utama
   - Schema model data (10 tabel)
   - **Referensi untuk requirements**

5. **[API_GUIDE.md](./API_GUIDE.md)**
   - Spesifikasi 8 API endpoints
   - Request/response examples
   - Error codes
   - Testing dengan Postman
   - **Referensi untuk implementasi API**

6. **[brain-strom.md](./brain-strom.md)**
   - Arsitektur integrasi N8N
   - Alur proses WhatsApp
   - Keamanan akses
   - Manfaat sistem
   - **Referensi untuk N8N integration**

---

## 🎯 Quick Navigation

### Saya ingin...

| Kebutuhan | Baca File | Catatan |
|-----------|-----------|---------|
| Memahami project secara keseluruhan | SETUP_SUMMARY.md | Start here! |
| Tahu apa yang harus dikerjakan | TODO.md | Gunakan sebagai checklist |
| Cek progress project | PROGRESS.md | Update regularly |
| Memahami requirements detail | project-overview.md | Technical spec |
| Implementasi API endpoints | API_GUIDE.md | Copy-paste examples |
| Setup N8N integration | brain-strom.md | Architecture reference |

---

## 📊 Project Structure

```
docs/
├── README.md (file ini)
├── SETUP_SUMMARY.md (ringkasan project)
├── TODO.md (task checklist)
├── PROGRESS.md (tracking progress)
├── project-overview.md (requirements detail)
├── API_GUIDE.md (API specifications)
└── brain-strom.md (N8N integration)
```

---

## 🔄 Workflow

### Fase Development

```
1. Baca SETUP_SUMMARY.md
   ↓
2. Baca project-overview.md (requirements)
   ↓
3. Buka TODO.md sebagai checklist
   ↓
4. Mulai Phase 1 (Environment & Database)
   ↓
5. Update PROGRESS.md setiap selesai task
   ↓
6. Lanjut ke Phase 2, 3, 4, 5
   ↓
7. Referensi API_GUIDE.md saat implementasi API
   ↓
8. Referensi brain-strom.md saat setup N8N
```

---

## 📈 Progress Tracking

| Phase | Status | Effort | Docs |
|-------|--------|--------|------|
| Phase 1: Environment & DB | ⏳ | 2.5h | TODO.md |
| Phase 2: Models & Core | ⏳ | 3.5h | TODO.md |
| Phase 3: API Controllers | ⏳ | 2.75h | API_GUIDE.md |
| Phase 4: Integration | ⏳ | 1.5h | TODO.md |
| Phase 5: Testing & Docs | ⏳ | 3.5h | TODO.md |

**Total**: ~13.75 hours

---

## 🔗 Key Information

### Tech Stack
- **Backend**: PHP 7.x/8.x + CodeIgniter 3
- **Database**: MySQL/MariaDB
- **API Auth**: X-API-KEY header
- **Response Format**: JSON
- **Integration**: N8N + Gemini AI

### Database Tables (10 Total)
1. users
2. medicine_categories
3. medicines
4. customers
5. sales
6. sale_items
7. stock_logs
8. shifts
9. attendances
10. api_keys

### API Endpoints (8 Total)
- 3 Sales endpoints
- 2 Attendance endpoints
- 1 Visits endpoint
- 1 Stock endpoint
- 1 Health check endpoint

---

## 💡 Tips

1. **Selalu baca SETUP_SUMMARY.md dulu** - Ini memberikan konteks lengkap
2. **Gunakan TODO.md sebagai checklist** - Update status saat selesai
3. **Update PROGRESS.md regularly** - Tracking progress penting
4. **Referensi API_GUIDE.md saat coding** - Copy-paste examples
5. **Jika ada pertanyaan, cek project-overview.md** - Jawaban ada di sana

---

## 📞 Dokumentasi Tambahan (TODO)

File-file berikut akan dibuat saat Phase 5:
- `SETUP_GUIDE.md` - Step-by-step installation
- `DEVELOPMENT.md` - Development guidelines
- `database_schema.sql` - SQL schema
- `postman_collection.json` - Postman collection

---

## 🎓 Referensi Eksternal

- [CodeIgniter 3 Docs](https://codeigniter.com/user_guide/)
- [N8N Documentation](https://docs.n8n.io/)
- [MySQL Best Practices](https://dev.mysql.com/doc/)
- [RESTful API Design](https://restfulapi.net/)

---

**Status**: 🔄 Project Setup Phase  
**Last Updated**: 2025-02-21  
**Next Step**: Baca SETUP_SUMMARY.md → Mulai Phase 1
