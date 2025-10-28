# Laravel Scaffold Plus

[![Latest Version on Packagist](https://img.shields.io/packagist/v/paulocoelho/laravel-scaffold-plus.svg?style=flat-square)](https://packagist.org/packages/paulocoelho/laravel-scaffold-plus)
[![Installs](https://img.shields.io/packagist/dt/paulocoelho/laravel-scaffold-plus.svg?style=flat-square)](https://packagist.org/packages/paulocoelho/laravel-scaffold-plus/stats)
[![License](https://img.shields.io/github/license/paulocoelho/laravel-scaffold-plus.svg?style=flat-square)](MIT)
[![PHP Version](https://img.shields.io/packagist/php-v/paulocoelho/laravel-scaffold-plus.svg?style=flat-square)](https://packagist.org/packages/paulocoelho/laravel-scaffold-plus)

## 🚀 Laravel Scaffold Plus

Um pacote que gera automaticamente a estrutura completa de artefatos para novas entidades no Laravel.

Com apenas um comando, você terá **Model, Migration, Requests, Controller, Service, Repository, Provider e Policy**
criados com exemplos de uso e injeção de dependência já configurados.

Ideal para acelerar estudos e projetos particulares.

## 📦 Instalação - Dependencia de desenvolvimento

```bash
  composer require --dev paulocoelho/laravel-scaffold-plus:dev-main
```

## ⚡ Uso

Para gerar a estrutura de uma entidade, execute:

```bash
  php artisan make:estrutura Produto
```

Exemplo com submódulo:

```bash
  php artisan make:estrutura Cadastro/Produto
```

## 🧱 Estrutura Gerada

### A execução do comando irá gerar automaticamente:

### Model

#### app/Models/Cadastro/Produto.php

### Migration

#### database/migrations/xxxx_xx_xx_xxxxxx_create_produtos_table.php

### Requests:

#### app/Http/Requests/Cadastro/Produto/StoreProdutoRequest.php

#### app/Http/Requests/Cadastro/Produto/UpdateProdutoRequest.php

### Controller

#### app/Http/Controllers/Cadastro/Produto/ProdutoController.php

### Service & Interface:

#### app/Services/Cadastro/Produto/ProdutoService.php

#### app/Services/Cadastro/Produto/ProdutoServiceInterface.php

### Repository & Interface:

#### app/Repositories/Cadastro/Produto/ProdutoRepository.php

#### app/Repositories/Cadastro/Produto/ProdutoRepositoryInterface.php

### Provider

#### app/Providers/Cadastro/Produto/ProdutoServiceProvider.php

### Policy

#### app/Policies/Cadastro/ProdutoPolicy.php

### Não se esqueça de registar o ScaffoldServiceProvider no arquivo bootstrap/providers.php.

## 📂 Exemplo de Controller Gerado

```php
<?php

namespace App\Http\Controllers\Cadastro\Produto;

use App\Http\Controllers\Controller;
use App\Services\Cadastro\Produto\ProdutoServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProdutoController extends Controller
{
    protected ProdutoServiceInterface $produtoService;

    public function __construct(ProdutoServiceInterface $produtoService)
    {
        $this->produtoService = $produtoService;
    }

    /**
     * Exibe uma lista de recursos.
     */
    public function index(): JsonResponse
    {
        $items = $this->produtoService->index();

        return response()->json(['data' => $items], Response::HTTP_OK);
    }
}
```

### ✅ Vantagens 🚀 Geração automática e padronizada de camadas

Facilita a criação de novas entidades com uma estrutura consistente.

### 🔄 Injeção de dependência já configurada

Facilita a manutenção e testes do código.

### 🔒 Policies para controle de autorização

Facilita a implementação de regras de acesso.

### 🧹 Código limpo e organizado

Facilita a leitura e manutenção do código.

### ⚙️ Compatível com Laravel 12+

Pronto para as versões mais recentes do Laravel.

### 📚 Documentação clara e objetiva

Documentação de codigo gerado para facilitar o entendimento.

### 🧪 Testes Preparado para testes com PHPUnit e Orchestra Testbench:

Execute os testes com o comando:

```bash 
  vendor/bin/phpunit
```

### 📜 Licença: Este projeto é open-source sob a licença MIT.

Utilize, modifique e distribua livremente.

### ✨ Autor Paulo Coelho

### 👨‍💻 Engenheiro de Software, entusiasta de tecnologia, automações, cloud, DevOps e metodologias ágeis.

### 🔗 Links

- [📦 Packagist](https://packagist.org/packages/paulocoelho/laravel-scaffold-plus)
- [🐙 GitHub](https://github.com/dev-paulocoelho-admin/laravel-scaffold-plus)
- [✉️ Gmail](mailto:paulomedinabr01@gmail.com)
- [💼 LinkedIn](https://www.linkedin.com/in/paulohcoelho/)