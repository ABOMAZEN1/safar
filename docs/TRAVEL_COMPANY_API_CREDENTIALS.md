# API Test Credentials

## Travel Company API

### API Endpoint
```
POST /api/v1/auth/travel-companies/login
```

### Test Credentials

تم إنشاء 8 شركات سفر للاختبار مع البيانات التالية:

### 1. شركة النقل السوري
- **Phone Number:** `0934567890`
- **Password:** `TravelCompany@2024`
- **Company Name:** Syrian Transport Company
- **Address:** دمشق، سوريا

### 2. خطوط حلب للنقل
- **Phone Number:** `0945678901`
- **Password:** `TravelCompany@2024`
- **Company Name:** Aleppo Bus Lines
- **Address:** حلب، سوريا

### 3. شركة السفر الذهبي
- **Phone Number:** `0956789012`
- **Password:** `TravelCompany@2024`
- **Company Name:** Golden Travel Company
- **Address:** حمص، سوريا

### 4. النقل السريع
- **Phone Number:** `0967890123`
- **Password:** `TravelCompany@2024`
- **Company Name:** Fast Transport
- **Address:** حماة، سوريا

### 5. شركة الرحلات المتميزة
- **Phone Number:** `0978901234`
- **Password:** `TravelCompany@2024`
- **Company Name:** Premium Travel Company
- **Address:** اللاذقية، سوريا

### 6. خطوط الشمال للنقل
- **Phone Number:** `0989012345`
- **Password:** `TravelCompany@2024`
- **Company Name:** North Transport Lines
- **Address:** إدلب، سوريا

### 7. شركة السفر الآمن
- **Phone Number:** `0990123456`
- **Password:** `TravelCompany@2024`
- **Company Name:** Safe Travel Company
- **Address:** درعا، سوريا

### 8. النقل المتطور
- **Phone Number:** `0901234567`
- **Password:** `TravelCompany@2024`
- **Company Name:** Advanced Transport
- **Address:** القنيطرة، سوريا

## Example API Request

```bash
curl -X POST "https://safar.techpundits.net/api/v1/auth/travel-companies/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "phone_number": "0934567890",
    "password": "TravelCompany@2024"
  }'
```

## Example Response

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "شركة النقل السوري",
      "phone_number": "0934567890",
      "type": "travel_company",
      "verified_at": "2024-01-01T00:00:00.000000Z"
    },
    "travel_company": {
      "id": 1,
      "company_name": "Syrian Transport Company",
      "contact_number": "0934567891",
      "address": "دمشق، سوريا",
      "image_path": "uploads/travel_company_images/syrian_transport_logo.jpeg"
    },
    "token": "1|abc123def456..."
  }
}
```

## Notes

- جميع الشركات تستخدم نفس كلمة المرور: `TravelCompany@2024`
- جميع الحسابات تم التحقق منها مسبقاً (`verified_at` is set)
- يمكن استخدام أي من أرقام الهواتف المذكورة أعلاه للاختبار
- الـ API يتطلب Content-Type: application/json
- الـ API يتطلب Accept: application/json

## Bus Driver API

### API Endpoint
```
POST /api/v1/auth/bus-drivers/login
```

### Test Credentials

تم إنشاء 4 سائقين حافلات للاختبار:

1. **أحمد محمد** - Phone: `0911111111` - Password: `BusDriver@2024`
2. **محمد علي** - Phone: `0922222222` - Password: `BusDriver@2024`
3. **علي حسن** - Phone: `0933333333` - Password: `BusDriver@2024`
4. **حسن أحمد** - Phone: `0944444444` - Password: `BusDriver@2024`

## Customer API

### API Endpoint
```
POST /api/v1/auth/customers/login
```

### Test Credentials

تم إنشاء 4 عملاء للاختبار:

1. **أحمد محمد** - Phone: `0955555555` - Password: `Customer@2024`
2. **فاطمة علي** - Phone: `0966666666` - Password: `Customer@2024`
3. **محمد حسن** - Phone: `0977777777` - Password: `Customer@2024`
4. **عائشة أحمد** - Phone: `0988888888` - Password: `Customer@2024`

## Summary

تم إنشاء البيانات التالية:
- ✅ 8 شركات سفر
- ✅ 4 سائقين حافلات
- ✅ 4 عملاء
- ✅ 101 رحلة حافلة
- ✅ 40 حجز رحلة
- ✅ 41 رفيق سفر

## Running the Seeders

لتشغيل الـ seeders وإنشاء هذه البيانات:

```bash
# تشغيل جميع الـ seeders
php artisan db:seed

# أو تشغيل seeders محددة
php artisan db:seed --class=TravelCompanySeeder
php artisan db:seed --class=BusDriverSeeder
php artisan db:seed --class=CustomerSeeder
```
