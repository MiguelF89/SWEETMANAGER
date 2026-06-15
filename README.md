# SweetManager

Sistema de gestão administrativa e financeira desenvolvido em Laravel, com leitura automatizada de boletos bancários através de imagens, PDFs e captura por câmera.

---

## Visão Geral

O SweetManager foi desenvolvido para automatizar processos financeiros e reduzir o trabalho manual relacionado ao registro e controle de boletos.

Além da leitura inteligente de boletos, o sistema oferece recursos de gestão administrativa, cadastro de clientes, produtos, vendas e acompanhamento financeiro.

---

## Funcionalidades

### Gestão Financeira

* Controle de boletos
* Registro de pagamentos
* Relatórios financeiros
* Resumo de valores pagos e pendentes

### Gestão Comercial

* Cadastro de clientes
* Cadastro de produtos
* Controle de vendas
* Controle de encomendas

### Leitor Inteligente de Boletos

Suporte para:

* Imagens JPG
* Imagens PNG
* Arquivos PDF
* Captura por câmera

Extração automática de:

* Valor do boleto
* Data de vencimento
* Banco emissor
* Linha digitável

---

## Tecnologias Utilizadas

### Backend

* PHP 8.3
* Laravel
* Laravel Sanctum
* API REST

### Frontend

* Blade
* Tailwind CSS
* JavaScript
* Vite

### Banco de Dados

* MySQL 8

### Processamento de Arquivos

* ZBar
* Tesseract OCR
* Ghostscript
* Imagick

### Infraestrutura

* Docker
* Docker Compose
* phpMyAdmin

---

## Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/MiguelF89/SWEETMANEGER.git
cd SWEETMANEGER
```

### 2. Subir os containers

```bash
docker compose up -d --build
```

### 3. Iniciar o Vite

Atualmente o ambiente Docker executa a aplicação Laravel e o banco de dados, porém o servidor de desenvolvimento do Vite precisa ser iniciado manualmente.

Abra um segundo terminal e execute:

```bash
docker exec -it <nome_container_app> npm run dev
```

Para localizar o nome do container:

```bash
docker ps
```

Exemplo:

```bash
docker exec -it sweetmanager-app-1 npm run dev
```

### 4. Acessar o sistema

Aplicação:

```text
http://localhost:8000
```

phpMyAdmin:

```text
http://localhost:8080
```

---

## Estrutura do Projeto

```text
app/
├── Http/
├── Models/
├── Services/

resources/
├── views/

routes/
├── web.php
├── api.php

database/
├── migrations/
```

---

## Fluxo de Leitura de Boletos

```text
Arquivo (Imagem ou PDF)
        ↓
Conversão para imagem
        ↓
Leitura de código de barras (ZBar)
        ↓
OCR com Tesseract (Fallback)
        ↓
Normalização dos dados
        ↓
Extração das informações
```

---

## API

### Leitura de Boleto

Endpoint:

```http
POST /api/boleto/read
```

Exemplo:

```bash
curl -X POST http://localhost:8000/api/boleto/read \
  -F "file=@boleto.jpg"
```

Resposta:

```json
{
  "success": true,
  "data": {
    "amount": 100.00,
    "due_date": "2026-05-10",
    "bank": "341",
    "linha_digitavel": "..."
  }
}
```

---

## Limitações Conhecidas

* O servidor Vite ainda precisa ser iniciado manualmente após a subida dos containers.
* A leitura OCR pode apresentar limitações em imagens com baixa qualidade.
* PDFs digitalizados com baixa resolução podem reduzir a taxa de acerto da leitura.

---

## Roadmap

* Dashboard financeiro avançado
* Exportação de relatórios
* Categorização automática de despesas
* Melhorias no OCR
* Integração com APIs bancárias

---

## Status do Projeto

Projeto em desenvolvimento contínuo.

Funcionalidades principais operacionais e novas melhorias sendo implementadas.

---

## Autor

Miguel Francisco Barbosa Domingues

GitHub:
https://github.com/MiguelF89

