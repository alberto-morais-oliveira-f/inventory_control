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

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST   | /api/sales | Registrar uma nova venda |
| GET    | /api/reports/sales | Relatório de vendas (filtros: `start_date`, `end_date`, `product_sku`) |
| GET    | /api/inventory | Listagem de produtos e quantidade em estoque |

Todos os endpoints aceitam **JSON** e retornam respostas no formato padrão Laravel Resource.

---

## Instalação

1. Clone o repositório:
```bash
git clone <repo_url>
cd <repo_dir>
