# Firebase Cloud Messaging Setup

## خطوات إعداد Firebase

### 1. إنشاء مشروع Firebase

1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. اضغط "Create a project" أو "إضافة مشروع"
3. أدخل اسم المشروع
4. اختر إذا كنت تريد تفعيل Google Analytics أم لا
5. اضغط "Create project"

### 2. إضافة تطبيق Android للتطبيق الخاص بك

1. في Firebase Console، اضغط "Add app"
2. اختر Android
3. أدخل `Package name` للتطبيق (مثل: com.example.yourapp)
4. أدخل `App nickname` (اختياري)
5. أدخل `SHA-1 certificate fingerprint` (يمكنك الحصول عليه بـ `keytool`)
6. اضغط "Register app"
7. حمل ملف `google-services.json` وضعه في مجلد `app` في تطبيق Android
8. اتبع التعليمات لإضافة dependencies في Android

### 3. الحصول على Server Key

1. في Firebase Console، اذهب إلى Project Settings
2. اضغط على تبويب "Cloud Messaging"
3. انسخ "Server key"

### 4. إعداد متغيرات البيئة

أضف هذه المتغيرات إلى ملف `.env` الخاص بك:

```env
# Firebase Configuration
FIREBASE_SERVER_KEY=your_firebase_server_key_here
FIREBASE_PROJECT_ID=your_firebase_project_id_here
```

### 5. تشغيل Migration

```bash
php artisan migrate
```

### 6. إنجاز الـ Cache

```bash
php artisan config:cache
```

## استخدام مركز الإشعارات

### إنشاء إشعار جديد

1. اذهب إلى لوحة التحكم الإدارية `/admin`
2. في القائمة الجانبية، اضغط على "مركز الإشعارات"
3. اضغط "Create new" أو "إضافة جديد"
4. املأ البيانات:
   - العنوان والمحتوى
   - الصورة (اختياري)
   - نوع الاستهداف (الجميع، محدد، شريحة)
   - تاريخ ووقت الإرسال
5. احفظ الإشعار

### أنواع الاستهداف

- **جميع المستخدمين**: سيتم إرسال الإشعار لجميع المستخدمين المسجلين
- **مستخدمين محددين**: اختر مستخدمين معينين لإرسال الإشعار لهم
- **شريحة من المستخدمين**: استخدم معايير مثل المدينة أو العمر أو تاريخ التسجيل

### جدولة الإرسال

- **إرسال فوري**: سيتم إرسال الإشعار فور حفظه
- **إرسال مجدول**: اختر تاريخ ووقت محدد لإرسال الإشعار

## إعداد تطبيق Flutter

### 1. إضافة dependencies

أضف هذه الـ packages إلى `pubspec.yaml`:

```yaml
dependencies:
  firebase_core: ^2.24.2
  firebase_messaging: ^14.7.18
  flutter_local_notifications: ^16.3.0
```

### 2. إعداد Firebase Messaging

```dart
// main.dart
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  runApp(MyApp());
}
```

### 3. تسجيل FCM Token

```dart
class NotificationService {
  static String? _token;

  static Future<String?> getToken() async {
    FirebaseMessaging messaging = FirebaseMessaging.instance;
    
    // التسجيل للحصول على الإذن
    NotificationSettings settings = await messaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
      provisional: false,
    );

    if (settings.authorizationStatus == AuthorizationStatus.authorized) {
      // الحصول على FCM Token
      String? token = await messaging.getToken();
      _token = token;
      
      // إرسال الـ Token إلى الخادم
      await sendTokenToServer(token);
      
      return token;
    }
    return null;
  }

  static Future<void> sendTokenToServer(String token) async {
    // إرسال الـ Token إلى Laravel API
    await http.post(
      Uri.parse('YOUR_API_URL/users/fcm-token'),
      headers: {'Content-Type': 'application/json'},
      body: json.encode({'firebase_token': token}),
    );
  }
}
```

### 4. التعامل مع الإشعارات

```dart
class NotificationHandler {
  static Future<void> initialize() async {
    FirebaseMessaging messaging = FirebaseMessaging.instance;

    // الإشعارات الواردة أثناء تشغيل التطبيق
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      print('جديد: ${message.notification?.title}');
      // عرض الإشعار محلياً
      _showLocalNotification(message);
    });

    // فتح التطبيق من الإشعارات
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      _handleNotificationTap(message);
    });

    // معالجة الإشعارات الضارة
    RemoteMessage? initialMessage = await messaging.getInitialMessage();
    if (initialMessage != null) {
      _handleNotificationTap(initialMessage);
    }
  }

  static void _showLocalNotification(RemoteMessage message) {
    // عرض الإشعار محلياً
  }

  static void _handleNotificationTap(RemoteMessage message) {
    // التعامل مع النقر على الإشعار
    String? clickAction = message.data['click_action'];
    if (clickAction != null) {
      // التنقل إلى الصفحة المطلوبة
      Get.toNamed(clickAction);
    }
  }
}
```

## استكشاف الأخطاء

### مشاكل شائعة

1. **Token غير صحيح**: تأكد من تحديث FCM Token بانتظام
2. **عدم وصول الإشعارات**: تحقق من اتصال الإنترنت وإعدادات الإشعارات
3. **أخطاء الـ Server**: راجع سجلات الخادم من أجل أخطاء FCM

### مراقبة الإحصائيات

- استخدم Firebase Console لمراقبة معدلات النجاح والفشل في الإشعارات
- راجع سجلات Laravel للتحقق من أي أخطاء في إرسال الإشعارات

## الدعم الفني

للمساعدة التقنية:
- Firebase Documentation: https://firebase.google.com/docs/cloud-messaging
- Laravel Documentation: https://laravel.com/docs
