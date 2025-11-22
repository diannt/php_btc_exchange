# PHP Bitcoin Exchange

A full-featured cryptocurrency exchange platform, currently undergoing modernization to a service-oriented architecture.

## 🚀 Features

### Core Functionality
*   **Trading Engine**: Real-time order matching and execution.
*   **Wallet Management**: Secure deposit and withdrawal processing.
*   **Currencies**: Support for BTC, LTC, and fiat currencies (USD, EUR, RUB).
*   **User Dashboard**: Customizable widgets for charts, order books, and history.
*   **Admin Panel**: Comprehensive tools for user management, audit logs, and financial oversight.

### Payment Integrations
*   **OKPay**
*   **Perfect Money**
*   **Yandex Money**
*   **EgoPay** (Legacy)

## 🏗️ Architecture & Modernization

This project is currently being refactored from a legacy MVC structure to a modular, testable architecture.

### New Service Layer (`lib/`)
The application now utilizes a service layer to decouple business logic from controllers:

*   **Services**: `PaymentService`, `UserService`, `AdminService` handle core business logic.
*   **Payment Gateways**: Adapter pattern implementation (`PaymentGatewayInterface`) allows easy switching and testing of payment providers.
*   **Infrastructure**: centralized logging (`Logger`), audit trails (`AuditLog`), and request validation (`RequestValidator`).

### Directory Structure
```
/
├── config/         # Configuration files
├── controllers/    # Legacy HTTP request handlers
├── core/           # Framework bootstrap and utilities
├── entity/         # Database entities (Active Record style)
├── lib/            # Modern Service Layer (PSR-4 autoloaded)
│   ├── Logging/    # Logger and AuditLog
│   ├── Payment/    # Gateway Adapters and Interfaces
│   ├── Services/   # Business Logic Services
│   └── Validation/ # Request Validation
├── modules/        # Legacy modules
├── public/         # Public web root
├── view/           # Template files
└── index.php       # Application entry point
```

## 🛠️ Installation & Setup

### Requirements
*   PHP 7.4+
*   MySQL / MariaDB
*   Web Server (Apache/Nginx)

### Setup Steps
1.  **Clone the repository**
    ```bash
    git clone https://github.com/diannt/php_btc_exchange.git
    ```

2.  **Database Setup**
    *   Create a new database.
    *   Import the schema from `emonex.sql`.
    *   Update database credentials in `config/config.php`.

3.  **Configuration**
    *   Copy `config/services.php` (if not present) and configure payment gateway credentials.
    *   Set environment variables for sensitive keys (recommended) or update `config/services.php` directly.

    **Environment Variables:**
    ```env
    OKPAY_WALLET_ID=...
    OKPAY_API_PASSWORD=...
    PM_ACCOUNT_ID=...
    PM_PASS_PHRASE=...
    YM_ACCESS_TOKEN=...
    ```

## 🔒 Security

*   **Audit Logging**: Sensitive admin actions are recorded in `logs/audit.log`.
*   **Validation**: Input validation is handled by `RequestValidator` to prevent injection and malformed data.
*   **Logging**: Application errors and events are logged to `logs/app.log`.

## 🤝 Contributing

We are actively moving legacy code to the new `lib/` structure. When contributing:
1.  Avoid adding logic to `controllers/`.
2.  Use the `Service` classes in `lib/Services/`.
3.  Implement new payment methods using `PaymentGatewayInterface`.

## 📄 License

Proprietary / Closed Source (Contact owner for details).

---
*Original project by diannt.net*
