<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Inventory & Sales Control System

Sistema de controle de estoque e vendas desenvolvido em **Laravel**, com funcionalidades de:

- Gestão de produtos e inventário.
- Registro e processamento de vendas via **jobs** e **fila (Redis)**.
- Relatórios de vendas com filtros por data e SKU, com paginação.
- Controle de estoque com **concorrência e locks** para evitar overselling.
- API RESTful para integração com front-end ou sistemas externos.

---

## Tecnologias

- Laravel 10
- PHP 8.2+
- PostgreSQL (ou outro banco relacional)
- Redis (para filas e locks)
- PHPUnit para testes automatizados

---

## Endpoints Principais

| Método | Endpoint           | Descrição                                                              |
|--------|--------------------|------------------------------------------------------------------------|
| POST   | /api/sales         | Registrar uma nova venda                                               |
| GET    | /api/reports/sales | Relatório de vendas (filtros: `start_date`, `end_date`, `product_sku`) |
| GET    | /api/inventory     | Listagem de produtos e quantidade em estoque                           |

Todos os endpoints aceitam **JSON** e retornam respostas no formato padrão Laravel Resource.

---

## Instalação

1. Clone o repositório:

```bash
  git clone git@github.com:alberto-morais-oliveira-f/inventory_control.git
```

2. Acesse o projeto:

```bash
cd inventory_control
```

3. Instale as dependências:

```bash
composer install
```

4. Configure o .env:

```bash
APP_NAME="Inventory Control"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=inventory_control
DB_USERNAME='Seu user'
DB_PASSWORD='Sua senha'

QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Obs: Usei o redis para a job, mas pode usar sync ou database.

5. Rode migrations e seeders:

```bash
php artisan migrate --seed
```

6. Rode as filas:

```bash
php artisan queue:work
```

#### Obs: Para verificar o processamento da venda como atualização de valores e alteração do status deve rodar a fila.

5. Execute os testes unitários e de integração:

```bash
php artisan test
```