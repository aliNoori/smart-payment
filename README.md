# SmartPayment

SmartPayment is a flexible and extensible Laravel package for managing payment workflows. It follows clean architecture principles and allows dynamic resolution of gateways and models.

---

## 📦 Installation

Install the package via Composer:

```bash
composer require amedev/smart-payment
```

Then publish the package components:

```bash
php artisan vendor:publish --tag=smart-payment-config         # Configuration file
php artisan vendor:publish --tag=smart-payment-migrations     # Database migrations
php artisan vendor:publish --tag=smart-payment-models         # Default models
php artisan vendor:publish --tag=smart-payment-translations   # Translations (e.g. Farsi)
```

Run migrations:

```bash
php artisan migrate
```

---

## ⚙️ Configuration

The configuration file will be available at `config/smart-payment.php`:

```php
return [
    'default' => 'zarinpal', // Default payment gateway

    'gateways' => [
        'zarinpal' => \SmartPayment\Gateways\ZarinpalGateway::class,
        // You can register your own gateway classes like:
        // 'idpay' => \App\Gateways\CustomIDPayGateway::class,
    ],

    'models' => [
        'order' => \App\Models\Order::class,           // Your custom Order model
        'transaction' => \App\Models\Transaction::class, // Your custom Transaction model
    ],
];
```

---

## 🚀 Making a Payment

To initiate a payment request, make a `POST` call to `/api/payment/pay`:

```http
POST /api/payment/pay
Content-Type: application/json
```

Example JSON body:

```json
{
  "amount": 10000,
  "gateway": "zarinpal",
  "callback_url": "https://your-site.com/payment/callback",
  "meta": {
    "description": "Order #123",
    "email": "user@example.com",
    "mobile": "09123456789"
  }
}
```

This will create a transaction and redirect the user to the payment gateway.

---

## 🔄 Payment Callback

After payment, the user is redirected to the callback URL:

```http
GET /payment/callback
```

The package will verify the payment status and complete the transaction accordingly.

---

## 🛠 Artisan Utilities

To automatically fix namespaces for your custom models (especially if you override default ones):

```bash
php artisan smart-payment:fix-model-namespace
```

---

## 🌐 Translations

You can customize user-facing messages using the published translation file:

```
resources/lang/vendor/smart-payment/fa/messages.php
```

---

## 📌 Notes

- The package is UI-agnostic: ideal for REST APIs or frontend frameworks like Vue or React.
- You can extend it with custom gateways by implementing your own Gateway class and registering it.
- The `meta` field allows storing custom order-related data like user contact or description.

---
