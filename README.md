# Görev Takip Uygulaması (To-Do App)

macOS Reminders benzeri, PHP + MySQL tabanlı, modern ve modüler görev yönetim sistemi.

## Özellikler
- Kullanıcı yetkilendirme (user, admin, viewer rolleri)
- Liste, bölüm (section) ve kart (görev) yapısı
- Takvim entegrasyonu (günlük/haftalık görevler)
- Görev kartlarında başlık, açıklama, tarih, etiket, durum
- Hızlı kart taşıma (Acil, Sorun, Tamamlananlar)
- Günlük/haftalık/aylık istatistikler (Chart.js ile)
- Bildirim ve sesli uyarı (listeye yeni görev eklenince)
- Responsive ve kullanıcı dostu arayüz
- AJAX ile dinamik işlemler

## Kurulum

### 1. Veritabanı
- `sql/schema.sql` dosyasını MySQL veritabanınıza uygulayın.
- `config/database.php` dosyasındaki veritabanı bağlantı bilgilerini güncelleyin.

### 2. PHP ve Sunucu
- PHP 7.4+ ve MySQL gereklidir.
- Proje kökünü bir web sunucusunda (ör. Apache, Nginx) `public/` klasörünü ana dizin olarak ayarlayın.
- `public/assets/sounds/notify.mp3` dosyasını bir bildirim sesiyle doldurun (veya örnek bir mp3 ekleyin).

### 3. Kullanıcı Oluşturma
- İlk kullanıcıyı manuel olarak veritabanına ekleyin veya bir kayıt ekranı ekleyin.
- Örnek SQL:
  ```sql
  INSERT INTO users (username, password, role) VALUES ('admin', '<bcrypt_hash>', 'admin');
  ```
  (Şifreyi PHP ile `password_hash('sifreniz', PASSWORD_DEFAULT)` ile üretin.)

### 4. Gerekli Kütüphaneler
- Chart.js: `public/assets/js/chart.min.js` (veya CDN: https://cdn.jsdelivr.net/npm/chart.js)

## Klasör Yapısı
```
/tda
  /public
    index.php
    login.php
    logout.php
    /assets
      /css
      /js
      /images
      /sounds
    /api
      lists.php
      sections.php
      tasks.php
      stats.php
      notifications.php
  /app
    /controllers
    /models
    /views
  /config
    database.php
  /sql
    schema.sql
```

## Geliştirme
- Kodlar MVC prensibine uygun, modüler ve genişletilebilir yapıdadır.
- Frontend vanilla JS ile yazılmıştır, istenirse Vue/React ile kolayca genişletilebilir.
- Responsive tasarım ve modern UI/UX.

## Katkı ve Lisans
- Açık kaynak, dilediğiniz gibi geliştirebilir ve kullanabilirsiniz. 