# Prompt — Criar um pacote TUS completo para Hyperf 3.2

Você é um desenvolvedor sênior especialista em Hyperf 3.2, PHP 8.4, Swoole/OpenSwoole, PSR-7, PSR-15, PSR-11 e no protocolo TUS 1.0.0.

Sua missão é desenvolver um pacote profissional chamado:

**lazarini/hyperf-tus**

O objetivo é criar uma implementação completa do protocolo TUS para Hyperf 3.2, sem depender de bibliotecas existentes como `ankitpokhrel/tus-php`, `he426100/tus-php-hyperf` ou qualquer outro servidor TUS.

Todo o código deve ser desenvolvido do zero seguindo rigorosamente a especificação oficial do protocolo.

---

# Objetivos

O pacote deve permitir uploads extremamente grandes (20GB, 50GB, 100GB+) de forma resumível.

Deve funcionar em:

* Hyperf 3.2
* PHP 8.4+
* Swoole
* OpenSwoole

Arquitetura totalmente orientada a serviços.

Sem Facades.

Sem código legado.

Sem dependências desnecessárias.

---

# Estrutura esperada

```
src/

Config/

Controller/

Driver/

Event/

Exception/

Listener/

Middleware/

Repository/

Storage/

Contracts/

Http/

DTO/

Factory/

Command/

Utils/

Traits/

```

---

# Recursos obrigatórios

Implementar completamente:

* Creation Extension
* Creation With Upload
* Checksum Extension
* Expiration Extension
* Concatenation Extension
* Termination Extension

---

# Endpoints

POST

Cria upload

HEAD

Retorna Upload-Offset

PATCH

Recebe chunks

DELETE

Cancela upload

OPTIONS

Informa capacidades do servidor

GET

Opcional para informações do upload

---

# Headers

Implementar suporte completo para:

```
Tus-Resumable
Upload-Length
Upload-Defer-Length
Upload-Metadata
Upload-Offset
Upload-Checksum
Upload-Concat
Upload-Expires
```

Responder exatamente conforme especificação.

---

# Middleware

Criar middleware responsável por:

Validar:

Tus-Resumable

Retornar:

```
412 Precondition Failed
```

caso inválido.

---

# Storage Drivers

Criar interface:

```
StorageDriverInterface
```

Métodos:

```
create()

append()

offset()

exists()

delete()

finish()

metadata()

length()

expires()

```

Implementações:

```
LocalStorageDriver

MemoryStorageDriver
```

Permitir criação de drivers customizados.

---

# Repository

Criar abstração para persistência dos uploads.

Interface:

```
UploadRepositoryInterface
```

Implementações:

Filesystem

Redis

Banco de dados

Cada upload deve armazenar:

```
id

offset

length

metadata

expires

completed

created_at

updated_at

```

---

# Configuração

Arquivo:

```
config/autoload/tus.php
```

Exemplo:

```php
return [

    'route' => '/files',

    'driver' => 'local',

    'storage_path' => BASE_PATH . '/runtime/tus',

    'expiration' => 86400,

    'chunk_size' => null,

    'max_size' => null,

    'repository' => 'filesystem',

];
```

---

# Publicação

Criar comando:

```
php bin/hyperf.php vendor:publish hyperf/tus
```

---

# Auto Discovery

Registrar automaticamente:

Config

Rotas

Middlewares

Listeners

Publish

Sem necessidade de configuração manual.

---

# Controller

Criar controller completo.

Cada método separado.

```
create()

head()

patch()

delete()

options()

info()

```

---

# Serviço principal

Criar:

```
TusServer
```

Responsável por toda lógica.

Controller apenas delega.

---

# Eventos

Criar eventos:

```
UploadCreated

ChunkReceived

UploadCompleted

UploadDeleted

UploadExpired

```

---

# Listeners

Permitir registrar listeners normalmente pelo Hyperf.

---

# Checksum

Implementar:

SHA1

MD5

SHA256

Conforme especificação.

Retornar erro caso checksum inválido.

---

# Expiração

Implementar limpeza automática.

Criar Command:

```
tus:clean
```

Remover uploads expirados.

---

# Concat

Implementar uploads paralelos.

```
partial

final
```

Conforme protocolo.

---

# Metadata

Implementar codificação Base64.

Converter automaticamente.

Disponibilizar helper para acesso.

---

# Validação

Validar:

Upload-Length

Upload-Offset

Metadata

Checksum

Offset

Content-Type

Content-Length

Todos os erros devem seguir exatamente a especificação TUS.

---

# Exceções

Criar exceções específicas.

Exemplos:

```
InvalidOffsetException

UploadNotFoundException

ChecksumException

UploadExpiredException

UploadLengthException

ProtocolException

```

---

# HTTP Responses

Implementar exatamente:

```
201

204

400

404

409

410

412

413

415

460

500
```

Com headers corretos.

---

# Rotas

Registrar automaticamente:

```
POST /files

HEAD /files/{id}

PATCH /files/{id}

DELETE /files/{id}

OPTIONS /files

```

---

# Cliente

Não desenvolver cliente JavaScript.

O pacote será apenas servidor.

Compatível com:

* tus-js-client
* Uppy
* FilePond
* qualquer cliente TUS

---

# Qualidade

Todo código deve seguir:

PSR-12

SOLID

DDD leve

Injeção de Dependência

Sem métodos gigantes

Sem código duplicado

Sem lógica dentro de Controllers

Sem Helpers globais

Sem Singletons

---

# Testes

Criar testes para:

Criação

Upload

Resume

HEAD

PATCH

DELETE

Checksum

Expiration

Concat

Eventos

Drivers

Repository

Falhas

Offsets inválidos

---

# Documentação

Gerar documentação completa contendo:

Instalação

Configuração

Uso

Eventos

Drivers

Middlewares

Exemplos

Integração com tus-js-client

Integração com Uppy

Criação de drivers personalizados

Criação de repositories personalizados

FAQ

---

# Organização da entrega

Desenvolva o pacote em etapas.

Cada etapa deve estar funcional antes da próxima.

Nunca entregue código incompleto.

Sempre forneça arquivos completos.

Nunca utilize pseudocódigo.

Nunca escreva "faça algo como".

Nunca omita implementações.

Sempre informe o caminho completo do arquivo antes do código.

Todas as implementações devem estar prontas para produção.
