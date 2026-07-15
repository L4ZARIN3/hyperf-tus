# Hyperf TUS - Tutorial Completo / Complete Tutorial / 完整教程

## Índice / Table of Contents / 目錄

1. [Introdução / Introduction / 介紹](#introdução--introduction--介紹)
2. [Instalação / Installation / 安裝](#instalação--installation--安裝)
3. [Configuração / Configuration / 配置](#configuração--configuration--配置)
4. [Uso Básico / Basic Usage / 基本使用](#uso-básico--basic-usage--基本使用)
5. [Protocolo TUS / TUS Protocol / TUS 協議](#protocolo-tus--tus-protocol--tus-協議)
6. [Eventos / Events / 事件](#eventos--events--事件)
7. [Exceções / Exceptions / 異常](#exceções--exceptions--異常)
8. [Comandos CLI / CLI Commands / CLI 命令](#comandos-cli--cli-commands--cli-命令)
9. [Personalização / Customization / 自定義](#personalização--customization--自定義)
10. [Exemplos de Cliente / Client Examples / 客戶端範例](#exemplos-de-cliente--client-examples--客戶端範例)

---

## Introdução / Introduction / 介紹

### Português (BR) 🇧🇷

O **Hyperf TUS** é uma implementação completa do protocolo TUS para o framework Hyperf 3.2. O protocolo TUS permite uploads de arquivos retomáveis, ou seja, se uma conexão falhar, o upload pode ser continuado de onde parou, sem precisar recomeçar do início.

**Principais recursos:**
- ✅ Uploads retomáveis (resumable uploads)
- ✅ Suporte a chunks (partes do arquivo)
- ✅ Validação de checksum (MD5, SHA1, SHA256)
- ✅ Expiração de uploads
- ✅ Concatenação de múltiplos uploads
- ✅ Cancelamento de uploads
- ✅ Eventos para integração
- ✅ Drivers de armazenamento (Local, Memory)
- ✅ Repositórios de metadados (Filesystem, Redis, Database)

### English 🇺🇸

**Hyperf TUS** is a complete implementation of the TUS protocol for the Hyperf 3.2 framework. The TUS protocol enables resumable file uploads, meaning if a connection fails, the upload can be resumed from where it left off, without having to start over from the beginning.

**Key features:**
- ✅ Resumable uploads
- ✅ Chunk support (file parts)
- ✅ Checksum validation (MD5, SHA1, SHA256)
- ✅ Upload expiration
- ✅ Concatenation of multiple uploads
- ✅ Upload termination/cancellation
- ✅ Events for integration
- ✅ Storage drivers (Local, Memory)
- ✅ Metadata repositories (Filesystem, Redis, Database)

### 繁體中文 🇹🇼

**Hyperf TUS** 是針對 Hyperf 3.2 框架的完整 TUS 協議實現。TUS 協議支援可恢復的文件上傳，這意味著如果連接失敗，上傳可以從中斷處繼續，無需從頭開始。

**主要功能：**
- ✅ 可恢復的上傳（Resumable uploads）
- ✅ 支援分塊上傳（Chunks）
- ✅ 校驗和驗證（MD5、SHA1、SHA256）
- ✅ 上傳過期機制
- ✅ 多個上傳的串接（Concatenation）
- ✅ 取消上傳
- ✅ 事件整合
- ✅ 儲存驅動程式（本地、記憶體）
- ✅ 中繼資料儲存庫（檔案系統、Redis、資料庫）

---

## Instalação / Installation / 安裝

### Português (BR) 🇧🇷

#### Passo 1: Instalar via Composer

```bash
composer require lazarini/hyperf-tus
```

#### Passo 2: Publicar o arquivo de configuração

```bash
php bin/hyperf.php vendor:publish lazarini/hyperf-tus
```

Isso criará o arquivo `config/autoload/tus.php`.

#### Passo 3: Configurar o Middleware

Adicione o middleware TUS ao seu servidor HTTP no arquivo `config/autoload/middlewares.php`:

```php
return [
    'http' => [
        \Lazarini\HyperfTus\Middleware\TusMiddleware::class,
    ],
];
```

#### Passo 4: Criar diretórios de armazenamento

Certifique-se de que os diretórios de armazenamento existam e tenham permissões adequadas:

```bash
mkdir -p runtime/tus
chmod -R 755 runtime/tus
```

### English 🇺🇸

#### Step 1: Install via Composer

```bash
composer require lazarini/hyperf-tus
```

#### Step 2: Publish the configuration file

```bash
php bin/hyperf.php vendor:publish lazarini/hyperf-tus
```

This will create the file `config/autoload/tus.php`.

#### Step 3: Configure the Middleware

Add the TUS middleware to your HTTP server in the `config/autoload/middlewares.php` file:

```php
return [
    'http' => [
        \Lazarini\HyperfTus\Middleware\TusMiddleware::class,
    ],
];
```

#### Step 4: Create storage directories

Ensure that the storage directories exist and have appropriate permissions:

```bash
mkdir -p runtime/tus
chmod -R 755 runtime/tus
```

### 繁體中文 🇹🇼

#### 步驟 1：透過 Composer 安裝

```bash
composer require lazarini/hyperf-tus
```

#### 步驟 2：發布配置文件

```bash
php bin/hyperf.php vendor:publish lazarini/hyperf-tus
```

這將創建文件 `config/autoload/tus.php`。

#### 步驟 3：配置中間件

在 `config/autoload/middlewares.php` 文件中將 TUS 中間件添加到您的 HTTP 服務器：

```php
return [
    'http' => [
        \Lazarini\HyperfTus\Middleware\TusMiddleware::class,
    ],
];
```

#### 步驟 4：創建存儲目錄

確保存儲目錄存在並具有適當的權限：

```bash
mkdir -p runtime/tus
chmod -R 755 runtime/tus
```

---

## Configuração / Configuration / 配置

### Português (BR) 🇧🇷

O arquivo de configuração `config/autoload/tus.php` contém todas as opções personalizáveis:

```php
<?php

return [
    // Rota base para uploads TUS
    'route' => '/files',

    // Driver de armazenamento: 'local', 'memory' ou classe personalizada
    'driver' => 'local',

    // Caminho onde os arquivos serão armazenados (para driver local)
    'storage_path' => BASE_PATH . '/runtime/tus',

    // Tempo de expiração do upload em segundos (null = sem expiração)
    'expiration' => 86400, // 24 horas

    // Tamanho máximo do chunk por requisição PATCH (null = ilimitado)
    'chunk_size' => null,

    // Tamanho máximo total do upload em bytes (null = ilimitado)
    'max_size' => null,

    // Repositório para metadados: 'filesystem', 'redis', 'database' ou classe personalizada
    'repository' => 'filesystem',

    // Configuração do repositório
    'repository_config' => [
        'filesystem' => [
            'path' => BASE_PATH . '/runtime/tus/uploads',
        ],
        'redis' => [
            'connection' => 'default',
            'prefix' => 'tus:',
        ],
        'database' => [
            'connection' => 'default',
            'table' => 'tus_uploads',
        ],
    ],

    // Algoritmos de checksum suportados
    'checksum_algorithms' => ['sha1', 'md5', 'sha256'],

    // Extensões TUS habilitadas
    'extensions' => [
        'creation' => true,              // Criação de uploads
        'creation_with_upload' => true,  // Criação com dados
        'checksum' => true,              // Validação de checksum
        'expiration' => true,            // Expiração de uploads
        'concatenation' => true,         // Concatenação de uploads
        'termination' => true,           // Cancelamento de uploads
    ],
];
```

### Opções de Configuração Detalhadas

| Opção | Tipo | Padrão | Descrição |
|-------|------|--------|-----------|
| `route` | string | `/files` | Rota base para todos os endpoints TUS |
| `driver` | string | `local` | Driver de armazenamento dos arquivos |
| `storage_path` | string | `runtime/tus` | Caminho para armazenar arquivos (driver local) |
| `expiration` | int\|null | `86400` | Tempo em segundos para expiração automática |
| `chunk_size` | int\|null | `null` | Tamanho máximo de cada chunk enviado |
| `max_size` | int\|null | `null` | Tamanho máximo total permitido para uploads |
| `repository` | string | `filesystem` | Tipo de repositório para metadados |
| `checksum_algorithms` | array | `['sha1', 'md5', 'sha256']` | Algoritmos de hash suportados |

### English 🇺🇸

The configuration file `config/autoload/tus.php` contains all customizable options:

```php
<?php

return [
    // Base route for TUS uploads
    'route' => '/files',

    // Storage driver: 'local', 'memory' or custom class
    'driver' => 'local',

    // Path where files will be stored (for local driver)
    'storage_path' => BASE_PATH . '/runtime/tus',

    // Upload expiration time in seconds (null = no expiration)
    'expiration' => 86400, // 24 hours

    // Maximum chunk size per PATCH request (null = unlimited)
    'chunk_size' => null,

    // Maximum total upload size in bytes (null = unlimited)
    'max_size' => null,

    // Repository for metadata: 'filesystem', 'redis', 'database' or custom class
    'repository' => 'filesystem',

    // Repository configuration
    'repository_config' => [
        'filesystem' => [
            'path' => BASE_PATH . '/runtime/tus/uploads',
        ],
        'redis' => [
            'connection' => 'default',
            'prefix' => 'tus:',
        ],
        'database' => [
            'connection' => 'default',
            'table' => 'tus_uploads',
        ],
    ],

    // Supported checksum algorithms
    'checksum_algorithms' => ['sha1', 'md5', 'sha256'],

    // Enabled TUS extensions
    'extensions' => [
        'creation' => true,              // Upload creation
        'creation_with_upload' => true,  // Creation with data
        'checksum' => true,              // Checksum validation
        'expiration' => true,            // Upload expiration
        'concatenation' => true,         // Upload concatenation
        'termination' => true,           // Upload cancellation
    ],
];
```

### Detailed Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `route` | string | `/files` | Base route for all TUS endpoints |
| `driver` | string | `local` | Storage driver for files |
| `storage_path` | string | `runtime/tus` | Path to store files (local driver) |
| `expiration` | int\|null | `86400` | Time in seconds for automatic expiration |
| `chunk_size` | int\|null | `null` | Maximum size of each chunk sent |
| `max_size` | int\|null | `null` | Maximum total size allowed for uploads |
| `repository` | string | `filesystem` | Repository type for metadata |
| `checksum_algorithms` | array | `['sha1', 'md5', 'sha256']` | Supported hash algorithms |

### 繁體中文 🇹🇼

配置文件 `config/autoload/tus.php` 包含所有可自定義的選項：

```php
<?php

return [
    // TUS 上傳的基础路由
    'route' => '/files',

    // 存儲驅動程式：'local'、'memory' 或自定義類
    'driver' => 'local',

    // 文件存儲路徑（適用於本地驅動）
    'storage_path' => BASE_PATH . '/runtime/tus',

    // 上傳過期時間（秒）（null = 無過期）
    'expiration' => 86400, // 24 小時

    // 每個 PATCH 請求的最大 chunk 大小（null = 無限制）
    'chunk_size' => null,

    // 最大上傳總大小（字節）（null = 無限制）
    'max_size' => null,

    // 中繼資料存儲庫：'filesystem'、'redis'、'database' 或自定義類
    'repository' => 'filesystem',

    // 存儲庫配置
    'repository_config' => [
        'filesystem' => [
            'path' => BASE_PATH . '/runtime/tus/uploads',
        ],
        'redis' => [
            'connection' => 'default',
            'prefix' => 'tus:',
        ],
        'database' => [
            'connection' => 'default',
            'table' => 'tus_uploads',
        ],
    ],

    // 支援的校驗和算法
    'checksum_algorithms' => ['sha1', 'md5', 'sha256'],

    // 啟用的 TUS 擴展功能
    'extensions' => [
        'creation' => true,              // 創建上傳
        'creation_with_upload' => true,  // 創建時帶數據
        'checksum' => true,              // 校驗和驗證
        'expiration' => true,            // 上傳過期
        'concatenation' => true,         // 上傳串接
        'termination' => true,           // 取消上傳
    ],
];
```

### 詳細配置選項

| 選項 | 類型 | 預設值 | 描述 |
|------|------|--------|------|
| `route` | string | `/files` | 所有 TUS 端點的基础路由 |
| `driver` | string | `local` | 文件存儲驅動程式 |
| `storage_path` | string | `runtime/tus` | 文件存儲路徑（本地驅動） |
| `expiration` | int\|null | `86400` | 自動過期的時間（秒） |
| `chunk_size` | int\|null | `null` | 每個 chunk 的最大大小 |
| `max_size` | int\|null | `null` | 允許的最大上傳總大小 |
| `repository` | string | `filesystem` | 中繼資料的存儲庫類型 |
| `checksum_algorithms` | array | `['sha1', 'md5', 'sha256']` | 支援的哈希算法 |

---

## Uso Básico / Basic Usage / 基本使用

### Português (BR) 🇧🇷

### Endpoints Disponíveis

Após a instalação, os seguintes endpoints estarão disponíveis (considerando a rota padrão `/files`):

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `OPTIONS` | `/files` | Obter capacidades do servidor |
| `POST` | `/files` | Criar novo upload |
| `HEAD` | `/files/{id}` | Obter informações do upload |
| `PATCH` | `/files/{id}` | Enviar dados do upload |
| `DELETE` | `/files/{id}` | Cancelar upload |
| `GET` | `/files/info/{id}` | Obter informações em JSON |

### Fluxo Básico de Upload

#### 1. Criar um Upload

```http
POST /files HTTP/1.1
Host: seu-servidor.com
Content-Type: application/json
Tus-Resumable: 1.0.0
Upload-Length: 1048576
Upload-Metadata: filename dGVzdGUucGRm,filetype YXBwbGljYXRpb24vcGRm

```

Resposta de sucesso:
```http
HTTP/1.1 201 Created
Location: http://seu-servidor.com/files/abc123def456
Upload-Offset: 0
Tus-Resumable: 1.0.0
```

#### 2. Verificar Status do Upload

```http
HEAD /files/abc123def456 HTTP/1.1
Host: seu-servidor.com
Tus-Resumable: 1.0.0
```

Resposta:
```http
HTTP/1.1 200 OK
Upload-Offset: 524288
Upload-Length: 1048576
Content-Type: application/offset+file
Tus-Resumable: 1.0.0
```

#### 3. Enviar Dados (Chunk)

```http
PATCH /files/abc123def456 HTTP/1.1
Host: seu-servidor.com
Content-Type: application/offset+file
Upload-Offset: 524288
Tus-Resumable: 1.0.0
Content-Length: 524288

[... dados binários ...]
```

Resposta:
```http
HTTP/1.1 204 No Content
Upload-Offset: 1048576
Tus-Resumable: 1.0.0
```

### English 🇺🇸

### Available Endpoints

After installation, the following endpoints will be available (assuming default route `/files`):

| Method | Endpoint | Description |
|--------|----------|-------------|
| `OPTIONS` | `/files` | Get server capabilities |
| `POST` | `/files` | Create new upload |
| `HEAD` | `/files/{id}` | Get upload information |
| `PATCH` | `/files/{id}` | Send upload data |
| `DELETE` | `/files/{id}` | Cancel upload |
| `GET` | `/files/info/{id}` | Get information in JSON |

### Basic Upload Flow

#### 1. Create an Upload

```http
POST /files HTTP/1.1
Host: your-server.com
Content-Type: application/json
Tus-Resumable: 1.0.0
Upload-Length: 1048576
Upload-Metadata: filename dGVzdGUucGRm,filetype YXBwbGljYXRpb24vcGRm

```

Success response:
```http
HTTP/1.1 201 Created
Location: http://your-server.com/files/abc123def456
Upload-Offset: 0
Tus-Resumable: 1.0.0
```

#### 2. Check Upload Status

```http
HEAD /files/abc123def456 HTTP/1.1
Host: your-server.com
Tus-Resumable: 1.0.0
```

Response:
```http
HTTP/1.1 200 OK
Upload-Offset: 524288
Upload-Length: 1048576
Content-Type: application/offset+file
Tus-Resumable: 1.0.0
```

#### 3. Send Data (Chunk)

```http
PATCH /files/abc123def456 HTTP/1.1
Host: your-server.com
Content-Type: application/offset+file
Upload-Offset: 524288
Tus-Resumable: 1.0.0
Content-Length: 524288

[... binary data ...]
```

Response:
```http
HTTP/1.1 204 No Content
Upload-Offset: 1048576
Tus-Resumable: 1.0.0
```

### 繁體中文 🇹🇼

### 可用端點

安裝後，以下端點將可用（假設預設路由為 `/files`）：

| 方法 | 端點 | 描述 |
|------|------|------|
| `OPTIONS` | `/files` | 獲取服務器功能 |
| `POST` | `/files` | 創建新上傳 |
| `HEAD` | `/files/{id}` | 獲取上傳信息 |
| `PATCH` | `/files/{id}` | 發送上傳數據 |
| `DELETE` | `/files/{id}` | 取消上傳 |
| `GET` | `/files/info/{id}` | 以 JSON 格式獲取信息 |

### 基本上传流程

#### 1. 創建上傳

```http
POST /files HTTP/1.1
Host: your-server.com
Content-Type: application/json
Tus-Resumable: 1.0.0
Upload-Length: 1048576
Upload-Metadata: filename dGVzdGUucGRm,filetype YXBwbGljYXRpb24vcGRm

```

成功響應：
```http
HTTP/1.1 201 Created
Location: http://your-server.com/files/abc123def456
Upload-Offset: 0
Tus-Resumable: 1.0.0
```

#### 2. 檢查上傳狀態

```http
HEAD /files/abc123def456 HTTP/1.1
Host: your-server.com
Tus-Resumable: 1.0.0
```

響應：
```http
HTTP/1.1 200 OK
Upload-Offset: 524288
Upload-Length: 1048576
Content-Type: application/offset+file
Tus-Resumable: 1.0.0
```

#### 3. 發送數據（Chunk）

```http
PATCH /files/abc123def456 HTTP/1.1
Host: your-server.com
Content-Type: application/offset+file
Upload-Offset: 524288
Tus-Resumable: 1.0.0
Content-Length: 524288

[... 二進制數據 ...]
```

響應：
```http
HTTP/1.1 204 No Content
Upload-Offset: 1048576
Tus-Resumable: 1.0.0
```

---

## Protocolo TUS / TUS Protocol / TUS 協議

### Português (BR) 🇧🇷

### Headers do Protocolo TUS

#### Headers de Requisição

| Header | Descrição | Obrigatório |
|--------|-----------|-------------|
| `Tus-Resumable` | Versão do protocolo TUS (sempre `1.0.0`) | Sim (exceto OPTIONS) |
| `Upload-Length` | Tamanho total do arquivo em bytes | Para criação (exceto com defer) |
| `Upload-Defer-Length` | Adiar o conhecimento do tamanho (`1`) | Alternativa ao Upload-Length |
| `Upload-Metadata` | Metadados codificados em Base64 | Não |
| `Upload-Offset` | Posição atual no arquivo (para PATCH) | Para PATCH |
| `Upload-Concat` | Modo de concatenação (`partial` ou `final;ids`) | Para concatenação |
| `Upload-Checksum` | Checksum do chunk (`algoritmo base64`) | Não |
| `Content-Type` | Deve ser `application/offset+file` | Para PATCH |

#### Headers de Resposta

| Header | Descrição |
|--------|-----------|
| `Tus-Resumable` | Versão do protocolo suportada |
| `Tus-Version` | Versões suportadas |
| `Tus-Extension` | Extensões suportadas (separadas por vírgula) |
| `Tus-Max-Size` | Tamanho máximo permitido |
| `Tus-Checksum-Algorithm` | Algoritmos de checksum suportados |
| `Location` | URL do upload criado |
| `Upload-Offset` | Offset atual após operação |
| `Upload-Length` | Tamanho total do upload |
| `Upload-Complete` | Se upload está completo (`true`) |
| `Upload-Expires` | Data de expiração do upload |

### Codificação de Metadados

Os metadados são codificados como pares chave-valor em Base64, separados por vírgula:

```javascript
// JavaScript exemplo
const metadata = {
    filename: 'documento.pdf',
    filetype: 'application/pdf',
    usuario: '123'
};

const encoded = Object.entries(metadata)
    .map(([key, value]) => 
        btoa(key) + ' ' + btoa(value)
    )
    .join(',');

// Resultado: "ZmlsZW5hbWU= ZG9jdW1lbnRvLnBkZg==,ZmlsZXR5cGU= YXBwbGljYXRpb24vcGRm,dXN1YXJpbz= MTIz"
```

### English 🇺🇸

### TUS Protocol Headers

#### Request Headers

| Header | Description | Required |
|--------|-------------|----------|
| `Tus-Resumable` | TUS protocol version (always `1.0.0`) | Yes (except OPTIONS) |
| `Upload-Length` | Total file size in bytes | For creation (except with defer) |
| `Upload-Defer-Length` | Defer length knowledge (`1`) | Alternative to Upload-Length |
| `Upload-Metadata` | Base64 encoded metadata | No |
| `Upload-Offset` | Current position in file (for PATCH) | For PATCH |
| `Upload-Concat` | Concatenation mode (`partial` or `final;ids`) | For concatenation |
| `Upload-Checksum` | Chunk checksum (`algorithm base64`) | No |
| `Content-Type` | Must be `application/offset+file` | For PATCH |

#### Response Headers

| Header | Description |
|--------|-------------|
| `Tus-Resumable` | Supported protocol version |
| `Tus-Version` | Supported versions |
| `Tus-Extension` | Supported extensions (comma-separated) |
| `Tus-Max-Size` | Maximum allowed size |
| `Tus-Checksum-Algorithm` | Supported checksum algorithms |
| `Location` | URL of created upload |
| `Upload-Offset` | Current offset after operation |
| `Upload-Length` | Total upload size |
| `Upload-Complete` | If upload is complete (`true`) |
| `Upload-Expires` | Upload expiration date |

### Metadata Encoding

Metadata is encoded as Base64 key-value pairs, separated by commas:

```javascript
// JavaScript example
const metadata = {
    filename: 'document.pdf',
    filetype: 'application/pdf',
    user: '123'
};

const encoded = Object.entries(metadata)
    .map(([key, value]) => 
        btoa(key) + ' ' + btoa(value)
    )
    .join(',');

// Result: "ZmlsZW5hbWU= ZG9jdW1lbnQucGRm,..."
```

### 繁體中文 🇹🇼

### TUS 協議標頭

#### 請求標頭

| 標頭 | 描述 | 是否必需 |
|------|------|----------|
| `Tus-Resumable` | TUS 協議版本（始終為 `1.0.0`） | 是（OPTIONS 除外） |
| `Upload-Length` | 文件總大小（字節） | 創建時需要（defer 除外） |
| `Upload-Defer-Length` | 延遲獲知大小（`1`） | Upload-Length 的替代方案 |
| `Upload-Metadata` | Base64 編碼的中繼資料 | 否 |
| `Upload-Offset` | 文件中的當前位置（用於 PATCH） | PATCH 需要 |
| `Upload-Concat` | 串接模式（`partial` 或 `final;ids`） | 串接時需要 |
| `Upload-Checksum` | Chunk 的校驗和（`算法 base64`） | 否 |
| `Content-Type` | 必須是 `application/offset+file` | PATCH 需要 |

#### 響應標頭

| 標頭 | 描述 |
|------|------|
| `Tus-Resumable` | 支援的協議版本 |
| `Tus-Version` | 支援的版本 |
| `Tus-Extension` | 支援的擴展（逗號分隔） |
| `Tus-Max-Size` | 允許的最大大小 |
| `Tus-Checksum-Algorithm` | 支援的校驗和算法 |
| `Location` | 創建的上傳 URL |
| `Upload-Offset` | 操作後的當前偏移量 |
| `Upload-Length` | 上傳總大小 |
| `Upload-Complete` | 上傳是否完成（`true`） |
| `Upload-Expires` | 上傳過期日期 |

### 中繼資料編碼

中繼資料被編碼為 Base64 鍵值對，用逗號分隔：

```javascript
// JavaScript 範例
const metadata = {
    filename: 'document.pdf',
    filetype: 'application/pdf',
    user: '123'
};

const encoded = Object.entries(metadata)
    .map(([key, value]) => 
        btoa(key) + ' ' + btoa(value)
    )
    .join(',');

// 結果："ZmlsZW5hbWU= ZG9jdW1lbnQucGRm,..."
```

---

## Eventos / Events / 事件

### Português (BR) 🇧🇷

O pacote dispara eventos que podem ser ouvidos para integrar com seu sistema:

### Eventos Disponíveis

| Evento | Descrição | Dados |
|--------|-----------|-------|
| `UploadCreated` | Quando um upload é criado | `UploadDTO $upload` |
| `ChunkReceived` | Quando um chunk é recebido | `UploadDTO $upload`, `int $chunkSize`, `int $newOffset` |
| `UploadCompleted` | Quando um upload é completado | `UploadDTO $upload` |
| `UploadDeleted` | Quando um upload é deletado | `UploadDTO $upload` |
| `UploadExpired` | Quando uploads expirados são limpos | `array $ids`, `int $count` |

### Exemplo: Listener para Upload Completed

```php
<?php

declare(strict_types=1);

namespace App\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Lazarini\HyperfTus\Event\UploadCompleted;

#[Listener]
class UploadCompletedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UploadCompleted::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof UploadCompleted) {
            $upload = $event->upload;
            
            // Fazer algo quando o upload estiver completo
            // Ex: mover arquivo, processar imagem, enviar notificação
            
            echo "Upload completado: {$upload->id}\n";
            echo "Tamanho: {$upload->length} bytes\n";
            echo "Metadados: " . json_encode($upload->metadata) . "\n";
        }
    }
}
```

### Exemplo: Listener para Upload Created

```php
<?php

declare(strict_types=1);

namespace App\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Lazarini\HyperfTus\Event\UploadCreated;

#[Listener]
class UploadCreatedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UploadCreated::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof UploadCreated) {
            $upload = $event->upload;
            
            // Logar criação de upload
            // Enviar notificação
            // Validar metadados
            
            echo "Novo upload criado: {$upload->id}\n";
        }
    }
}
```

### English 🇺🇸

The package dispatches events that can be listened to for system integration:

### Available Events

| Event | Description | Data |
|-------|-------------|------|
| `UploadCreated` | When an upload is created | `UploadDTO $upload` |
| `ChunkReceived` | When a chunk is received | `UploadDTO $upload`, `int $chunkSize`, `int $newOffset` |
| `UploadCompleted` | When an upload is completed | `UploadDTO $upload` |
| `UploadDeleted` | When an upload is deleted | `UploadDTO $upload` |
| `UploadExpired` | When expired uploads are cleaned | `array $ids`, `int $count` |

### Example: Listener for Upload Completed

```php
<?php

declare(strict_types=1);

namespace App\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Lazarini\HyperfTus\Event\UploadCompleted;

#[Listener]
class UploadCompletedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UploadCompleted::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof UploadCompleted) {
            $upload = $event->upload;
            
            // Do something when upload is complete
            // E.g.: move file, process image, send notification
            
            echo "Upload completed: {$upload->id}\n";
            echo "Size: {$upload->length} bytes\n";
            echo "Metadata: " . json_encode($upload->metadata) . "\n";
        }
    }
}
```

### Example: Listener for Upload Created

```php
<?php

declare(strict_types=1);

namespace App\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Lazarini\HyperfTus\Event\UploadCreated;

#[Listener]
class UploadCreatedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UploadCreated::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof UploadCreated) {
            $upload = $event->upload;
            
            // Log upload creation
            // Send notification
            // Validate metadata
            
            echo "New upload created: {$upload->id}\n";
        }
    }
}
```

### 繁體中文 🇹🇼

該包會觸發事件，可以監聽這些事件以與您的系統整合：

### 可用事件

| 事件 | 描述 | 數據 |
|------|------|------|
| `UploadCreated` | 當上傳被創建時 | `UploadDTO $upload` |
| `ChunkReceived` | 當收到 chunk 時 | `UploadDTO $upload`, `int $chunkSize`, `int $newOffset` |
| `UploadCompleted` | 當上傳完成時 | `UploadDTO $upload` |
| `UploadDeleted` | 當上傳被刪除時 | `UploadDTO $upload` |
| `UploadExpired` | 當過期的上傳被清理時 | `array $ids`, `int $count` |

### 範例：Upload Completed 的監聽器

```php
<?php

declare(strict_types=1);

namespace App\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Lazarini\HyperfTus\Event\UploadCompleted;

#[Listener]
class UploadCompletedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UploadCompleted::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof UploadCompleted) {
            $upload = $event->upload;
            
            // 當上傳完成時執行操作
            // 例如：移動文件、處理圖片、發送通知
            
            echo "上傳完成：{$upload->id}\n";
            echo "大小：{$upload->length} 字節\n";
            echo "中繼資料：" . json_encode($upload->metadata) . "\n";
        }
    }
}
```

### 範例：Upload Created 的監聽器

```php
<?php

declare(strict_types=1);

namespace App\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Lazarini\HyperfTus\Event\UploadCreated;

#[Listener]
class UploadCreatedListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UploadCreated::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof UploadCreated) {
            $upload = $event->upload;
            
            // 記錄上傳創建
            // 發送通知
            // 驗證中繼資料
            
            echo "新上傳已創建：{$upload->id}\n";
        }
    }
}
```

---

## Exceções / Exceptions / 異常

### Português (BR) 🇧🇷

O pacote define exceções específicas para diferentes erros do protocolo TUS:

### Classes de Exceção

| Exceção | Código HTTP | Descrição |
|---------|-------------|-----------|
| `TusException` | 500 | Exceção base para todos os erros TUS |
| `ProtocolException` | 400 | Violação do protocolo TUS |
| `UploadNotFoundException` | 404 | Upload não encontrado |
| `InvalidOffsetException` | 409 | Offset inválido (conflito) |
| `UploadLengthException` | 400 | Erro no tamanho declarado do upload |
| `InvalidContentTypeException` | 400 | Content-Type inválido |
| `ChecksumException` | 400 | Falha na validação do checksum |
| `UploadExpiredException` | 410 | Upload expirado |
| `SizeLimitExceededException` | 413 | Tamanho excede limite máximo |
| `ConcatenationException` | 400 | Erro na concatenação de uploads |

### Exemplo de Tratamento de Exceções

```php
<?php

use Lazarini\HyperfTus\Exception\TusException;
use Lazarini\HyperfTus\Exception\UploadNotFoundException;
use Lazarini\HyperfTus\Exception\InvalidOffsetException;

try {
    // Tentar obter informações do upload
    $upload = $tusServer->getUploadInfo('upload-id-invalido');
} catch (UploadNotFoundException $e) {
    // Upload não existe
    echo "Upload não encontrado: " . $e->getMessage();
} catch (InvalidOffsetException $e) {
    // Offset incorreto - cliente deve ajustar
    echo "Offset inválido. Esperado: " . $e->getMessage();
} catch (TusException $e) {
    // Outros erros TUS
    echo "Erro TUS: " . $e->getMessage();
    echo "Código HTTP: " . $e->getHttpStatusCode();
}
```

### TusExceptionHandler

O pacote inclui um handler de exceções que automaticamente converte exceções TUS em respostas HTTP apropriadas:

```php
// Registrar em config/autoload/exceptions.php
return [
    'handler' => [
        'http' => [
            \Lazarini\HyperfTus\Exception\TusExceptionHandler::class,
        ],
    ],
];
```

### English 🇺🇸

The package defines specific exceptions for different TUS protocol errors:

### Exception Classes

| Exception | HTTP Code | Description |
|-----------|-----------|-------------|
| `TusException` | 500 | Base exception for all TUS errors |
| `ProtocolException` | 400 | TUS protocol violation |
| `UploadNotFoundException` | 404 | Upload not found |
| `InvalidOffsetException` | 409 | Invalid offset (conflict) |
| `UploadLengthException` | 400 | Error in declared upload size |
| `InvalidContentTypeException` | 400 | Invalid Content-Type |
| `ChecksumException` | 400 | Checksum validation failure |
| `UploadExpiredException` | 410 | Upload has expired |
| `SizeLimitExceededException` | 413 | Size exceeds maximum limit |
| `ConcatenationException` | 400 | Error in upload concatenation |

### Exception Handling Example

```php
<?php

use Lazarini\HyperfTus\Exception\TusException;
use Lazarini\HyperfTus\Exception\UploadNotFoundException;
use Lazarini\HyperfTus\Exception\InvalidOffsetException;

try {
    // Try to get upload information
    $upload = $tusServer->getUploadInfo('invalid-upload-id');
} catch (UploadNotFoundException $e) {
    // Upload doesn't exist
    echo "Upload not found: " . $e->getMessage();
} catch (InvalidOffsetException $e) {
    // Incorrect offset - client should adjust
    echo "Invalid offset. Expected: " . $e->getMessage();
} catch (TusException $e) {
    // Other TUS errors
    echo "TUS Error: " . $e->getMessage();
    echo "HTTP Code: " . $e->getHttpStatusCode();
}
```

### TusExceptionHandler

The package includes an exception handler that automatically converts TUS exceptions to appropriate HTTP responses:

```php
// Register in config/autoload/exceptions.php
return [
    'handler' => [
        'http' => [
            \Lazarini\HyperfTus\Exception\TusExceptionHandler::class,
        ],
    ],
];
```

### 繁體中文 🇹🇼

該包為不同的 TUS 協議錯誤定義了特定的異常：

### 異常類

| 異常 | HTTP 代碼 | 描述 |
|------|-----------|------|
| `TusException` | 500 | 所有 TUS 錯誤的基礎異常 |
| `ProtocolException` | 400 | TUS 協議違規 |
| `UploadNotFoundException` | 404 | 找不到上傳 |
| `InvalidOffsetException` | 409 | 無效的偏移量（衝突） |
| `UploadLengthException` | 400 | 聲明的上傳大小錯誤 |
| `InvalidContentTypeException` | 400 | 無效的 Content-Type |
| `ChecksumException` | 400 | 校驗和驗證失敗 |
| `UploadExpiredException` | 410 | 上傳已過期 |
| `SizeLimitExceededException` | 413 | 大小超過最大限制 |
| `ConcatenationException` | 400 | 上傳串接錯誤 |

### 異常處理範例

```php
<?php

use Lazarini\HyperfTus\Exception\TusException;
use Lazarini\HyperfTus\Exception\UploadNotFoundException;
use Lazarini\HyperfTus\Exception\InvalidOffsetException;

try {
    // 嘗試獲取上傳信息
    $upload = $tusServer->getUploadInfo('invalid-upload-id');
} catch (UploadNotFoundException $e) {
    // 上傳不存在
    echo "找不到上傳：" . $e->getMessage();
} catch (InvalidOffsetException $e) {
    // 偏移量不正確 - 客戶端應調整
    echo "無效的偏移量。預期：" . $e->getMessage();
} catch (TusException $e) {
    // 其他 TUS 錯誤
    echo "TUS 錯誤：" . $e->getMessage();
    echo "HTTP 代碼：" . $e->getHttpStatusCode();
}
```

### TusExceptionHandler

該包包含一個異常處理器，可自動將 TUS 異常轉換為適當的 HTTP 響應：

```php
// 在 config/autoload/exceptions.php 中註冊
return [
    'handler' => [
        'http' => [
            \Lazarini\HyperfTus\Exception\TusExceptionHandler::class,
        ],
    ],
];
```

---

## Comandos CLI / CLI Commands / CLI 命令

### Português (BR) 🇧🇷

### Comando de Limpeza

O pacote fornece um comando para limpar uploads expirados:

```bash
# Executar limpeza manual
php bin/hyperf.php tus:clean

# Saída esperada:
# Cleaning up expired uploads...
# Cleaned 5 expired upload(s).
```

### Agendar Limpeza Automática (Cron)

Para limpar uploads expirados automaticamente, adicione uma tarefa cron:

```bash
# Editar crontab
crontab -e

# Adicionar linha para executar a cada hora
0 * * * * cd /caminho/para/projeto && php bin/hyperf.php tus:clean >> /var/log/tus-clean.log 2>&1
```

### English 🇺🇸

### Clean Command

The package provides a command to clean up expired uploads:

```bash
# Run manual cleanup
php bin/hyperf.php tus:clean

# Expected output:
# Cleaning up expired uploads...
# Cleaned 5 expired upload(s).
```

### Schedule Automatic Cleanup (Cron)

To automatically clean expired uploads, add a cron job:

```bash
# Edit crontab
crontab -e

# Add line to run every hour
0 * * * * cd /path/to/project && php bin/hyperf.php tus:clean >> /var/log/tus-clean.log 2>&1
```

### 繁體中文 🇹🇼

### 清理命令

該包提供了一個命令來清理過期的上傳：

```bash
# 執行手動清理
php bin/hyperf.php tus:clean

# 預期輸出：
# Cleaning up expired uploads...
# Cleaned 5 expired upload(s).
```

### 安排自動清理（Cron）

要自動清理過期的上傳，添加一個 cron 任務：

```bash
# 編輯 crontab
crontab -e

# 添加每小時運行的行
0 * * * * cd /path/to/project && php bin/hyperf.php tus:clean >> /var/log/tus-clean.log 2>&1
```

---

## Personalização / Customization / 自定義

### Português (BR) 🇧🇷

### Criar Driver de Armazenamento Personalizado

Para criar um driver personalizado (ex: S3, Google Cloud Storage):

```php
<?php

declare(strict_types=1);

namespace App\Storage\Drivers;

use Lazarini\HyperfTus\Contracts\StorageDriverInterface;

class S3StorageDriver implements StorageDriverInterface
{
    public function __construct(
        private readonly \Aws\S3\S3Client $s3Client,
        private readonly string $bucket
    ) {}

    public function create(string $uploadId, ?int $length): bool
    {
        // Criar objeto vazio no S3
        $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => 'tus/' . $uploadId . '.part',
            'Body' => '',
        ]);
        
        return true;
    }

    public function append(string $uploadId, string $data, int $offset): int
    {
        // Obter dados existentes
        $existing = $this->s3Client->getObject([
            'Bucket' => $this->bucket,
            'Key' => 'tus/' . $uploadId . '.part',
        ]);
        
        $currentData = $existing['Body']->getContents();
        
        // Preencher com null bytes se necessário
        if ($offset > strlen($currentData)) {
            $currentData = str_pad($currentData, $offset, "\0");
        }
        
        // Escrever dados no offset
        $before = substr($currentData, 0, $offset);
        $after = substr($currentData, $offset + strlen($data));
        $newData = $before . $data . $after;
        
        // Salvar no S3
        $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => 'tus/' . $uploadId . '.part',
            'Body' => $newData,
        ]);
        
        return strlen($data);
    }

    public function offset(string $uploadId): int
    {
        try {
            $result = $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key' => 'tus/' . $uploadId . '.part',
            ]);
            return $result['ContentLength'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function exists(string $uploadId): bool
    {
        return $this->s3Client->doesObjectExist($this->bucket, 'tus/' . $uploadId . '.part');
    }

    public function delete(string $uploadId): bool
    {
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => 'tus/' . $uploadId . '.part',
        ]);
        
        return true;
    }

    public function finish(string $uploadId): bool
    {
        // Renomear objeto (copy + delete)
        $this->s3Client->copyObject([
            'Bucket' => $this->bucket,
            'Key' => 'tus/' . $uploadId,
            'CopySource' => urlencode($this->bucket . '/tus/' . $uploadId . '.part'),
        ]);
        
        $this->s3Client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => 'tus/' . $uploadId . '.part',
        ]);
        
        return true;
    }

    public function metadata(string $uploadId, array $metadata): bool
    {
        // S3 suporta metadados personalizados
        $this->s3Client->copyObject([
            'Bucket' => $this->bucket,
            'Key' => 'tus/' . $uploadId . '.part',
            'CopySource' => urlencode($this->bucket . '/tus/' . $uploadId . '.part'),
            'Metadata' => $metadata,
            'MetadataDirective' => 'REPLACE',
        ]);
        
        return true;
    }

    public function length(string $uploadId): ?int
    {
        // Implementar conforme necessário
        return null;
    }

    public function expires(string $uploadId, \DateTimeInterface $expiresAt): bool
    {
        // Implementar lifecycle policy se necessário
        return true;
    }

    public function getPath(string $uploadId): ?string
    {
        return "s3://{$this->bucket}/tus/{$uploadId}";
    }
}
```

### Registrar Driver Personalizado

No arquivo de configuração ou Provider:

```php
// config/autoload/dependencies.php
return [
    \Lazarini\HyperfTus\Contracts\StorageDriverInterface::class => \App\Storage\Drivers\S3StorageDriver::class,
];
```

### Criar Repositório Personalizado

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use Lazarini\HyperfTus\Contracts\UploadRepositoryInterface;

class RedisRepository implements UploadRepositoryInterface
{
    public function __construct(
        private readonly \Hyperf\Redis\RedisFactory $redisFactory
    ) {}

    private function getRedis(): \Hyperf\Redis\Redis
    {
        return $this->redisFactory->get('default');
    }

    private function getKey(string $id): string
    {
        return 'tus:upload:' . $id;
    }

    public function create(string $id, ?int $length, array $metadata = [], ?\DateTimeInterface $expiresAt = null): bool
    {
        $redis = $this->getRedis();
        $key = $this->getKey($id);
        
        if ($redis->exists($key)) {
            return false;
        }
        
        $data = [
            'id' => $id,
            'length' => $length,
            'offset' => 0,
            'metadata' => json_encode($metadata),
            'expires_at' => $expiresAt?->getTimestamp(),
            'completed' => false,
            'created_at' => time(),
            'updated_at' => time(),
        ];
        
        return $redis->set($key, json_encode($data));
    }

    public function find(string $id): ?array
    {
        $redis = $this->getRedis();
        $key = $this->getKey($id);
        
        $data = $redis->get($key);
        if (!$data) {
            return null;
        }
        
        $decoded = json_decode($data, true);
        if ($decoded && isset($decoded['metadata'])) {
            $decoded['metadata'] = json_decode($decoded['metadata'], true);
        }
        
        return $decoded;
    }

    public function updateOffset(string $id, int $offset): bool
    {
        $data = $this->find($id);
        if (!$data) {
            return false;
        }
        
        $data['offset'] = $offset;
        $data['updated_at'] = time();
        
        return $this->getRedis()->set($this->getKey($id), json_encode($data));
    }

    public function updateLength(string $id, int $length): bool
    {
        $data = $this->find($id);
        if (!$data) {
            return false;
        }
        
        $data['length'] = $length;
        $data['updated_at'] = time();
        
        return $this->getRedis()->set($this->getKey($id), json_encode($data));
    }

    public function markCompleted(string $id): bool
    {
        $data = $this->find($id);
        if (!$data) {
            return false;
        }
        
        $data['completed'] = true;
        $data['updated_at'] = time();
        
        return $this->getRedis()->set($this->getKey($id), json_encode($data));
    }

    public function delete(string $id): bool
    {
        return $this->getRedis()->del($this->getKey($id)) > 0;
    }

    public function exists(string $id): bool
    {
        return $this->getRedis()->exists($this->getKey($id)) > 0;
    }

    public function getExpired(): array
    {
        // Implementar lógica para encontrar uploads expirados no Redis
        return [];
    }

    public function getPartials(string $finalId): array
    {
        $data = $this->find($finalId);
        return $data['partial_ids'] ?? [];
    }

    public function setConcatParts(string $finalId, array $partialIds): bool
    {
        $data = $this->find($finalId);
        if (!$data) {
            return false;
        }
        
        $data['partial_ids'] = $partialIds;
        
        return $this->getRedis()->set($this->getKey($finalId), json_encode($data));
    }

    public function cleanExpired(int $olderThan): int
    {
        // Implementar limpeza de expirados
        return 0;
    }
}
```

### English 🇺🇸

### Create Custom Storage Driver

To create a custom driver (e.g., S3, Google Cloud Storage):

```php
<?php

declare(strict_types=1);

namespace App\Storage\Drivers;

use Lazarini\HyperfTus\Contracts\StorageDriverInterface;

class S3StorageDriver implements StorageDriverInterface
{
    public function __construct(
        private readonly \Aws\S3\S3Client $s3Client,
        private readonly string $bucket
    ) {}

    // ... implement all interface methods ...
}
```

### Register Custom Driver

In configuration file or Provider:

```php
// config/autoload/dependencies.php
return [
    \Lazarini\HyperfTus\Contracts\StorageDriverInterface::class => \App\Storage\Drivers\S3StorageDriver::class,
];
```

### Create Custom Repository

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use Lazarini\HyperfTus\Contracts\UploadRepositoryInterface;

class RedisRepository implements UploadRepositoryInterface
{
    // ... implement all interface methods ...
}
```

### 繁體中文 🇹🇼

### 創建自定義存儲驅動

要創建自定義驅動（例如 S3、Google Cloud Storage）：

```php
<?php

declare(strict_types=1);

namespace App\Storage\Drivers;

use Lazarini\HyperfTus\Contracts\StorageDriverInterface;

class S3StorageDriver implements StorageDriverInterface
{
    public function __construct(
        private readonly \Aws\S3\S3Client $s3Client,
        private readonly string $bucket
    ) {}

    // ... 實現所有接口方法 ...
}
```

### 註冊自定義驅動

在配置文件或 Provider 中：

```php
// config/autoload/dependencies.php
return [
    \Lazarini\HyperfTus\Contracts\StorageDriverInterface::class => \App\Storage\Drivers\S3StorageDriver::class,
];
```

### 創建自定義存儲庫

```php
<?php

declare(strict_types=1);

namespace App\Repository;

use Lazarini\HyperfTus\Contracts\UploadRepositoryInterface;

class RedisRepository implements UploadRepositoryInterface
{
    // ... 實現所有接口方法 ...
}
```

---

## Exemplos de Cliente / Client Examples / 客戶端範例

### Português (BR) 🇧🇷

### JavaScript (Browser)

```javascript
class TusUploader {
    constructor(endpoint, file) {
        this.endpoint = endpoint;
        this.file = file;
        this.uploadUrl = null;
        this.chunkSize = 5 * 1024 * 1024; // 5MB
    }

    async upload() {
        // Passo 1: Criar upload
        await this.createUpload();
        
        // Passo 2: Enviar dados em chunks
        await this.sendChunks();
    }

    async createUpload() {
        const response = await fetch(this.endpoint, {
            method: 'POST',
            headers: {
                'Tus-Resumable': '1.0.0',
                'Upload-Length': this.file.size.toString(),
                'Upload-Metadata': this.encodeMetadata({
                    filename: this.file.name,
                    filetype: this.file.type
                })
            }
        });

        if (!response.ok) {
            throw new Error(`Failed to create upload: ${response.status}`);
        }

        this.uploadUrl = response.headers.get('Location');
        console.log('Upload created:', this.uploadUrl);
    }

    async sendChunks() {
        let offset = await this.getOffset();
        
        while (offset < this.file.size) {
            const chunk = this.file.slice(offset, offset + this.chunkSize);
            await this.sendChunk(chunk, offset);
            offset = await this.getOffset();
            
            console.log(`Uploaded: ${offset}/${this.file.size} bytes`);
        }
        
        console.log('Upload completed!');
    }

    async sendChunk(chunk, offset) {
        const checksum = await this.calculateChecksum(chunk);
        
        const response = await fetch(this.uploadUrl, {
            method: 'PATCH',
            headers: {
                'Tus-Resumable': '1.0.0',
                'Upload-Offset': offset.toString(),
                'Content-Type': 'application/offset+file',
                'Upload-Checksum': checksum
            },
            body: chunk
        });

        if (!response.ok) {
            throw new Error(`Failed to send chunk: ${response.status}`);
        }
    }

    async getOffset() {
        const response = await fetch(this.uploadUrl, {
            method: 'HEAD',
            headers: {
                'Tus-Resumable': '1.0.0'
            }
        });

        if (!response.ok) {
            throw new Error(`Failed to get offset: ${response.status}`);
        }

        return parseInt(response.headers.get('Upload-Offset'));
    }

    encodeMetadata(metadata) {
        return Object.entries(metadata)
            .map(([key, value]) => 
                btoa(key) + ' ' + btoa(value)
            )
            .join(',');
    }

    async calculateChecksum(data) {
        const buffer = await data.arrayBuffer();
        const hashBuffer = await crypto.subtle.digest('SHA-256', buffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashBase64 = btoa(String.fromCharCode(...hashArray));
        return `sha256 ${hashBase64}`;
    }
}

// Uso
const uploader = new TusUploader('https://seu-servidor.com/files', fileInput.files[0]);
uploader.upload().catch(console.error);
```

### Python

```python
import requests
import base64
import hashlib

class TusUploader:
    def __init__(self, endpoint, file_path, chunk_size=5*1024*1024):
        self.endpoint = endpoint
        self.file_path = file_path
        self.chunk_size = chunk_size
        self.upload_url = None
        
    def create_upload(self):
        file_size = os.path.getsize(self.file_path)
        filename = os.path.basename(self.file_path)
        
        metadata = self.encode_metadata({
            'filename': filename,
            'filetype': 'application/octet-stream'
        })
        
        headers = {
            'Tus-Resumable': '1.0.0',
            'Upload-Length': str(file_size),
            'Upload-Metadata': metadata
        }
        
        response = requests.post(self.endpoint, headers=headers)
        response.raise_for_status()
        
        self.upload_url = response.headers.get('Location')
        print(f'Upload created: {self.upload_url}')
        
    def upload(self):
        self.create_upload()
        
        offset = self.get_offset()
        
        with open(self.file_path, 'rb') as f:
            f.seek(offset)
            while True:
                chunk = f.read(self.chunk_size)
                if not chunk:
                    break
                    
                self.send_chunk(chunk, offset)
                offset = self.get_offset()
                print(f'Uploaded: {offset} bytes')
                
        print('Upload completed!')
        
    def send_chunk(self, chunk, offset):
        checksum = self.calculate_checksum(chunk)
        
        headers = {
            'Tus-Resumable': '1.0.0',
            'Upload-Offset': str(offset),
            'Content-Type': 'application/offset+file',
            'Upload-Checksum': checksum
        }
        
        response = requests.patch(self.upload_url, headers=headers, data=chunk)
        response.raise_for_status()
        
    def get_offset(self):
        headers = {'Tus-Resumable': '1.0.0'}
        response = requests.head(self.upload_url, headers=headers)
        response.raise_for_status()
        return int(response.headers.get('Upload-Offset'))
        
    def encode_metadata(self, metadata):
        parts = []
        for key, value in metadata.items():
            encoded_key = base64.b64encode(key.encode()).decode()
            encoded_value = base64.b64encode(value.encode()).decode()
            parts.append(f'{encoded_key} {encoded_value}')
        return ','.join(parts)
        
    def calculate_checksum(self, data):
        sha256_hash = hashlib.sha256(data).digest()
        checksum = base64.b64encode(sha256_hash).decode()
        return f'sha256 {checksum}'

# Uso
uploader = TusUploader('https://seu-servidor.com/files', 'arquivo.zip')
uploader.upload()
```

### English 🇺🇸

### JavaScript (Browser)

```javascript
class TusUploader {
    constructor(endpoint, file) {
        this.endpoint = endpoint;
        this.file = file;
        this.uploadUrl = null;
        this.chunkSize = 5 * 1024 * 1024; // 5MB
    }

    async upload() {
        // Step 1: Create upload
        await this.createUpload();
        
        // Step 2: Send data in chunks
        await this.sendChunks();
    }

    async createUpload() {
        const response = await fetch(this.endpoint, {
            method: 'POST',
            headers: {
                'Tus-Resumable': '1.0.0',
                'Upload-Length': this.file.size.toString(),
                'Upload-Metadata': this.encodeMetadata({
                    filename: this.file.name,
                    filetype: this.file.type
                })
            }
        });

        if (!response.ok) {
            throw new Error(`Failed to create upload: ${response.status}`);
        }

        this.uploadUrl = response.headers.get('Location');
        console.log('Upload created:', this.uploadUrl);
    }

    async sendChunks() {
        let offset = await this.getOffset();
        
        while (offset < this.file.size) {
            const chunk = this.file.slice(offset, offset + this.chunkSize);
            await this.sendChunk(chunk, offset);
            offset = await this.getOffset();
            
            console.log(`Uploaded: ${offset}/${this.file.size} bytes`);
        }
        
        console.log('Upload completed!');
    }

    async sendChunk(chunk, offset) {
        const checksum = await this.calculateChecksum(chunk);
        
        const response = await fetch(this.uploadUrl, {
            method: 'PATCH',
            headers: {
                'Tus-Resumable': '1.0.0',
                'Upload-Offset': offset.toString(),
                'Content-Type': 'application/offset+file',
                'Upload-Checksum': checksum
            },
            body: chunk
        });

        if (!response.ok) {
            throw new Error(`Failed to send chunk: ${response.status}`);
        }
    }

    async getOffset() {
        const response = await fetch(this.uploadUrl, {
            method: 'HEAD',
            headers: {
                'Tus-Resumable': '1.0.0'
            }
        });

        if (!response.ok) {
            throw new Error(`Failed to get offset: ${response.status}`);
        }

        return parseInt(response.headers.get('Upload-Offset'));
    }

    encodeMetadata(metadata) {
        return Object.entries(metadata)
            .map(([key, value]) => 
                btoa(key) + ' ' + btoa(value)
            )
            .join(',');
    }

    async calculateChecksum(data) {
        const buffer = await data.arrayBuffer();
        const hashBuffer = await crypto.subtle.digest('SHA-256', buffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashBase64 = btoa(String.fromCharCode(...hashArray));
        return `sha256 ${hashBase64}`;
    }
}

// Usage
const uploader = new TusUploader('https://your-server.com/files', fileInput.files[0]);
uploader.upload().catch(console.error);
```

### Python

```python
import requests
import base64
import hashlib
import os

class TusUploader:
    def __init__(self, endpoint, file_path, chunk_size=5*1024*1024):
        self.endpoint = endpoint
        self.file_path = file_path
        self.chunk_size = chunk_size
        self.upload_url = None
        
    def create_upload(self):
        file_size = os.path.getsize(self.file_path)
        filename = os.path.basename(self.file_path)
        
        metadata = self.encode_metadata({
            'filename': filename,
            'filetype': 'application/octet-stream'
        })
        
        headers = {
            'Tus-Resumable': '1.0.0',
            'Upload-Length': str(file_size),
            'Upload-Metadata': metadata
        }
        
        response = requests.post(self.endpoint, headers=headers)
        response.raise_for_status()
        
        self.upload_url = response.headers.get('Location')
        print(f'Upload created: {self.upload_url}')
        
    def upload(self):
        self.create_upload()
        
        offset = self.get_offset()
        
        with open(self.file_path, 'rb') as f:
            f.seek(offset)
            while True:
                chunk = f.read(self.chunk_size)
                if not chunk:
                    break
                    
                self.send_chunk(chunk, offset)
                offset = self.get_offset()
                print(f'Uploaded: {offset} bytes')
                
        print('Upload completed!')
        
    def send_chunk(self, chunk, offset):
        checksum = self.calculate_checksum(chunk)
        
        headers = {
            'Tus-Resumable': '1.0.0',
            'Upload-Offset': str(offset),
            'Content-Type': 'application/offset+file',
            'Upload-Checksum': checksum
        }
        
        response = requests.patch(self.upload_url, headers=headers, data=chunk)
        response.raise_for_status()
        
    def get_offset(self):
        headers = {'Tus-Resumable': '1.0.0'}
        response = requests.head(self.upload_url, headers=headers)
        response.raise_for_status()
        return int(response.headers.get('Upload-Offset'))
        
    def encode_metadata(self, metadata):
        parts = []
        for key, value in metadata.items():
            encoded_key = base64.b64encode(key.encode()).decode()
            encoded_value = base64.b64encode(value.encode()).decode()
            parts.append(f'{encoded_key} {encoded_value}')
        return ','.join(parts)
        
    def calculate_checksum(self, data):
        sha256_hash = hashlib.sha256(data).digest()
        checksum = base64.b64encode(sha256_hash).decode()
        return f'sha256 {checksum}'

# Usage
uploader = TusUploader('https://your-server.com/files', 'file.zip')
uploader.upload()
```

### 繁體中文 🇹🇼

### JavaScript (瀏覽器)

```javascript
class TusUploader {
    constructor(endpoint, file) {
        this.endpoint = endpoint;
        this.file = file;
        this.uploadUrl = null;
        this.chunkSize = 5 * 1024 * 1024; // 5MB
    }

    async upload() {
        // 步驟 1：創建上傳
        await this.createUpload();
        
        // 步驟 2：分塊發送數據
        await this.sendChunks();
    }

    async createUpload() {
        const response = await fetch(this.endpoint, {
            method: 'POST',
            headers: {
                'Tus-Resumable': '1.0.0',
                'Upload-Length': this.file.size.toString(),
                'Upload-Metadata': this.encodeMetadata({
                    filename: this.file.name,
                    filetype: this.file.type
                })
            }
        });

        if (!response.ok) {
            throw new Error(`創建上傳失敗：${response.status}`);
        }

        this.uploadUrl = response.headers.get('Location');
        console.log('上傳已創建：', this.uploadUrl);
    }

    async sendChunks() {
        let offset = await this.getOffset();
        
        while (offset < this.file.size) {
            const chunk = this.file.slice(offset, offset + this.chunkSize);
            await this.sendChunk(chunk, offset);
            offset = await this.getOffset();
            
            console.log(`已上傳：${offset}/${this.file.size} 字節`);
        }
        
        console.log('上傳完成！');
    }

    async sendChunk(chunk, offset) {
        const checksum = await this.calculateChecksum(chunk);
        
        const response = await fetch(this.uploadUrl, {
            method: 'PATCH',
            headers: {
                'Tus-Resumable': '1.0.0',
                'Upload-Offset': offset.toString(),
                'Content-Type': 'application/offset+file',
                'Upload-Checksum': checksum
            },
            body: chunk
        });

        if (!response.ok) {
            throw new Error(`發送 chunk 失敗：${response.status}`);
        }
    }

    async getOffset() {
        const response = await fetch(this.uploadUrl, {
            method: 'HEAD',
            headers: {
                'Tus-Resumable': '1.0.0'
            }
        });

        if (!response.ok) {
            throw new Error(`獲取偏移量失敗：${response.status}`);
        }

        return parseInt(response.headers.get('Upload-Offset'));
    }

    encodeMetadata(metadata) {
        return Object.entries(metadata)
            .map(([key, value]) => 
                btoa(key) + ' ' + btoa(value)
            )
            .join(',');
    }

    async calculateChecksum(data) {
        const buffer = await data.arrayBuffer();
        const hashBuffer = await crypto.subtle.digest('SHA-256', buffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashBase64 = btoa(String.fromCharCode(...hashArray));
        return `sha256 ${hashBase64}`;
    }
}

// 使用範例
const uploader = new TusUploader('https://your-server.com/files', fileInput.files[0]);
uploader.upload().catch(console.error);
```

### Python

```python
import requests
import base64
import hashlib
import os

class TusUploader:
    def __init__(self, endpoint, file_path, chunk_size=5*1024*1024):
        self.endpoint = endpoint
        self.file_path = file_path
        self.chunk_size = chunk_size
        self.upload_url = None
        
    def create_upload(self):
        file_size = os.path.getsize(self.file_path)
        filename = os.path.basename(self.file_path)
        
        metadata = self.encode_metadata({
            'filename': filename,
            'filetype': 'application/octet-stream'
        })
        
        headers = {
            'Tus-Resumable': '1.0.0',
            'Upload-Length': str(file_size),
            'Upload-Metadata': metadata
        }
        
        response = requests.post(self.endpoint, headers=headers)
        response.raise_for_status()
        
        self.upload_url = response.headers.get('Location')
        print(f'上傳已創建：{self.upload_url}')
        
    def upload(self):
        self.create_upload()
        
        offset = self.get_offset()
        
        with open(self.file_path, 'rb') as f:
            f.seek(offset)
            while True:
                chunk = f.read(self.chunk_size)
                if not chunk:
                    break
                    
                self.send_chunk(chunk, offset)
                offset = self.get_offset()
                print(f'已上傳：{offset} 字節')
                
        print('上傳完成！')
        
    def send_chunk(self, chunk, offset):
        checksum = self.calculate_checksum(chunk)
        
        headers = {
            'Tus-Resumable': '1.0.0',
            'Upload-Offset': str(offset),
            'Content-Type': 'application/offset+file',
            'Upload-Checksum': checksum
        }
        
        response = requests.patch(self.upload_url, headers=headers, data=chunk)
        response.raise_for_status()
        
    def get_offset(self):
        headers = {'Tus-Resumable': '1.0.0'}
        response = requests.head(self.upload_url, headers=headers)
        response.raise_for_status()
        return int(response.headers.get('Upload-Offset'))
        
    def encode_metadata(self, metadata):
        parts = []
        for key, value in metadata.items():
            encoded_key = base64.b64encode(key.encode()).decode()
            encoded_value = base64.b64encode(value.encode()).decode()
            parts.append(f'{encoded_key} {encoded_value}')
        return ','.join(parts)
        
    def calculate_checksum(self, data):
        sha256_hash = hashlib.sha256(data).digest()
        checksum = base64.b64encode(sha256_hash).decode()
        return f'sha256 {checksum}'

# 使用範例
uploader = TusUploader('https://your-server.com/files', 'file.zip')
uploader.upload()
```

---

## Dicas de Produção / Production Tips / 生產環境建議

### Português (BR) 🇧🇷

### Segurança

1. **Validar Metadados**: Sempre valide os metadados recebidos para evitar injeção de caminhos ou outros ataques.
2. **Limitar Tamanhos**: Configure `max_size` apropriadamente para seu caso de uso.
3. **Autenticação**: Implemente autenticação antes de permitir uploads.
4. **HTTPS**: Use sempre HTTPS em produção.

### Performance

1. **Chunk Size Apropriado**: Ajuste `chunk_size` baseado na sua rede (5-10MB é comum).
2. **Redis para Metadados**: Use Redis para melhor performance em ambientes distribuídos.
3. **CDN**: Considere usar CDN para servir arquivos completos.
4. **Limpeza Automática**: Configure o comando `tus:clean` para rodar periodicamente.

### Monitoramento

1. **Logs de Eventos**: Monitore os eventos `UploadCompleted` e `UploadExpired`.
2. **Métricas**: Acompanhe taxas de sucesso/falha de uploads.
3. **Alertas**: Configure alertas para falhas consecutivas.

### English 🇺🇸

### Security

1. **Validate Metadata**: Always validate received metadata to prevent path injection or other attacks.
2. **Limit Sizes**: Configure `max_size` appropriately for your use case.
3. **Authentication**: Implement authentication before allowing uploads.
4. **HTTPS**: Always use HTTPS in production.

### Performance

1. **Appropriate Chunk Size**: Adjust `chunk_size` based on your network (5-10MB is common).
2. **Redis for Metadata**: Use Redis for better performance in distributed environments.
3. **CDN**: Consider using CDN to serve completed files.
4. **Automatic Cleanup**: Configure `tus:clean` command to run periodically.

### Monitoring

1. **Event Logs**: Monitor `UploadCompleted` and `UploadExpired` events.
2. **Metrics**: Track upload success/failure rates.
3. **Alerts**: Configure alerts for consecutive failures.

### 繁體中文 🇹🇼

### 安全性

1. **驗證中繼資料**：始終驗證收到的中繼資料以防止路徑注入或其他攻擊。
2. **限制大小**：根據您的使用案例適當配置 `max_size`。
3. **身份驗證**：在允許上傳之前實施身份驗證。
4. **HTTPS**：在生產環境中始終使用 HTTPS。

### 性能

1. **適當的 Chunk 大小**：根據您的網絡調整 `chunk_size`（5-10MB 是常見的）。
2. **Redis 存儲中繼資料**：在分佈式環境中使用 Redis 以獲得更好的性能。
3. **CDN**：考慮使用 CDN 來提供完成的文件。
4. **自動清理**：配置 `tus:clean` 命令定期運行。

### 監控

1. **事件日誌**：監控 `UploadCompleted` 和 `UploadExpired` 事件。
2. **指標**：追蹤上傳成功/失敗率。
3. **警報**：為連續失敗配置警報。

---

## Licença / License / 授權

MIT License - Consulte o arquivo LICENSE para mais detalhes.

MIT License - See LICENSE file for more details.

MIT 授權 - 查看 LICENSE 文件了解更多詳情。

---

## Suporte / Support / 支援

Para issues e contribuições, visite o repositório no GitHub.

For issues and contributions, visit the GitHub repository.

如有問題和貢獻，請訪問 GitHub 倉庫。
