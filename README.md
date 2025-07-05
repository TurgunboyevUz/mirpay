# Mirpay Laravel SDK

Laravel uchun Mirpay to‘lov tizimini qulay integratsiya qiluvchi SDK.

![Packagist Version](https://img.shields.io/packagist/v/TurgunboyevUz/mirpay)
![License](https://img.shields.io/github/license/TurgunboyevUz/mirpay)
![Laravel](https://img.shields.io/badge/Laravel-10%2F11%2F12-red)

---

## 📦 O‘rnatish

```bash
composer require turgunboyevuz/mirpay
````

Paketni to‘liq o‘rnatish uchun quyidagi artisan komandani ishga tushiring:

```bash
php artisan mirpay:install
```

Bu quyidagilarni avtomatik bajaraydi:

* `routes/mirpay.php` faylini yaratadi
* `config/mirpay.php` faylini yaratadi
* `app/Payment/after_success.php` va `after_fail.php` fayllarni yaratadi

### Mirpay Tranzaksiyalari uchun tableni yarating
```bash
php artisan migrate
```

---

## ⚙️ Konfiguratsiya

`.env` faylingizga quyidagilarni yozing:

```env
MIRPAY_MERCHANT_ID=your-merchant-id
MIRPAY_SECRET_KEY=your-secret-key
```

`config/mirpay.php` fayli quyidagicha ko‘rinadi:

```php
return [
    'merchant_id' => env('MIRPAY_MERCHANT_ID', ''),
    'secret_key'  => env('MIRPAY_SECRET_KEY', ''),

    // Agar true bo‘lsa, paketdagi routes/mirpay.php fayli avtomatik ulanadi
    'routes' => true,
];
```

---

## 🧾 Marshrutlar (`routes`)

Agar `routes` konfiguratsiyasi `true` bo‘lsa, `routes/mirpay.php` fayli avtomatik yuklanadi.

Siz bu faylga Mirpay webhook routelarini joylashtirishingiz mumkin:

---

## ⚠️ Diqqat bilan o'qing
Mirpay to'lov tizimidan foydalanishning 2 ta turi bor:
- Webhook (Mirpay to'lov statusini sizga yuboradi)
- Long polling (Mirpaydan to'lov statusini so'rab olasiz).

Ushbu paketdan foydalanishda yuqoridagilardan faqat bittasidan foydalaning, aks holda to'lovni qabul qilish jarayonida turli xatolar kelib chiqishi mumkin.

---

```php
use Illuminate\Support\Facades\Route;
use TurgunboyevUz\Mirpay\Controllers\MirpayController;

// webhook usuli
// quyidagi linklarni Mirpay Kassangizda ro'yxatdan o'tkazing
// https://localhost/api/mirpay/success
// https://localhost/api/mirpay/fail

Route::post('/mirpay/success', [MirpayController::class, 'success']);

Route::post('/mirpay/fail', [MirpayController::class, 'fail']);
```

---

## 💳 Foydalanish

Trait orqali modelga Mirpay to‘lovlarini ulash:

```php
use TurgunboyevUz\Mirpay\Traits\HasMirpayTransactions;

class User extends Model
{
    use HasMirpayTransactions;
}
```

### ➕ To‘lov yaratish

```php
$user = auth()->user();

$transaction = $user->createMirpayTransaction(25000, 'Pro subscription');

return redirect($transaction->redirect_url);
```

### ✅ To‘lovni tekshirish (Polling)

```php
use TurgunboyevUz\Mirpay\Enums\MirpayState;

$user = auth()->user();
$status = $user->checkoutMirpayTransaction();

if ($status === MirpayState::PENDING) {
    // jarayonda
}

if ($status === MirpayState::SUCCESS) {
    // muvaffaqiyatli to‘lov
    // status "muvaffaqiyatli" bo'lsa avtomatik after_success.php chaqiriladi
}

if ($status === MirpayState::FAILED) {
    // bekor qilingan
    // status "bekor qilingan" bo'lsa avtomatik after_fail.php chaqiriladi
}
```

---

## 📁 Callback fayllar

Paket o‘rnatilgach, quyidagi fayllar yaratiladi:

* `app/Payment/after_success.php`
* `app/Payment/after_fail.php`

Ushbu fayllar `$model` nomli o‘zgaruvchiga ega bo‘lib, siz xohlagancha kod yozishingiz mumkin:

```php
<?php
/** @var TurgunboyevUz\Mirpay\Models\MirpayTransaction $transaction */

// after_success.php
// to'lov bajarilgandan keyin model bilan bajariladigan ishlar

// Tranzaksiya ichidagi ma'lumotlar $transaction o'zgaruvchisida keladi

$model->update(['paid' => true]);
\Log::info("To‘lov muvaffaqiyatli: User #{$model->id}");
```

```php
<?php
/** @var TurgunboyevUz\Mirpay\Models\MirpayTransaction $transaction */

// after_fail.php
// to'lov bekor qilingandan keyin model bilan bajariladigan ishlar

// Tranzaksiya ichidagi ma'lumotlar $transaction o'zgaruvchisida keladi

\Log::warning("To‘lov bekor qilindi: User #{$model->id}");
```

## ⚙️ Model overriding (MirpayTransaction)
Siz `mirpay_transactions` tableini o'zgartirishingiz mumkin (faqat default columnlarga tegmagan holatda):
```php
Schema::create('mirpay_transactions', function (Blueprint $table) {
    $table->id();
    $table->nullableMorphs('payable'); // tegilmaydi
    $table->string('transaction_id'); // tegilmaydi
    $table->bigInteger('amount'); // tegilmaydi
    $table->integer('state'); // tegilmaydi

    $table->foreignId('user')->constrained();
    $table->timestamps();
});
```

Shuningdek o'z logikangizni yaratish uchun MirpayTransaction modelini qayta yozishingiz mumkin:
```php
<?php
// app/Models/Transaction.php
namespace App\Models;

use TurgunboyevUz\Mirpay\Models\MirpayTransaction;

class Transaction extends MirpayTransaction
{
    public function user()
    {
        $this->belongsTo(User::class);
    }
}
```

Barcha o'zgarishlarni amalga oshirib bo'lgach `config/mirpay.php` faylida yangi modelni ko'rsatib o'ting:
```php
<?php

return [
    //...
    'transaction_model' => App\Users\Transaction::class
];
```

---

## 🔐 Minimal talablar

| PHP  | Laravel             |
| ---- | ------------------- |
| ^8.1 | ^10.0, ^11.0, ^12.0 |

---

## ✅ Qo‘llab-quvvatlanadigan Laravel versiyalari

| Laravel | Qo‘llab-quvvatlanadi |
| ------- | -------------------- |
| 10.x    | ✅                    |
| 11.x    | ✅                    |
| 12.x    | ✅                    |

---

## 📚 Muallif

 - [Diyorbek Turg'unboyev](https://t.me/Turgunboyev_D)

## 📄 Litsenziya

MIT — [LICENSE](LICENSE) fayliga qarang.