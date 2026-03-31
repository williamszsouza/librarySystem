# LibrarySystem

Sistema de gerenciamento de biblioteca com CRUD de livros, controle de empréstimos, autenticação com níveis de acesso e notificações por e-mail.

**Stack:** Laravel 13 · Vue 3 · Tailwind CSS · SQLite/Mysql · Laravel Sanctum

---

## Requisitos

Antes de começar, certifique-se de ter instalado:

- PHP 8.3+
- Composer
- Node.js 18+ e npm
- SQLite (já incluso no PHP por padrão)

---

## Instalação

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd livraria_system
```

### 2. Instale as dependências PHP

```bash
composer install
```

### 3. Configure o ambiente

Copie o arquivo de exemplo e gere a chave da aplicação:

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure o banco de dados
O projeto utiliza MySQL por padrão, mas pode ser facilmente adaptado para rodar em SQLite para agilizar os testes.

### Opção A: MySQL (Padrão de Desenvolvimento)

1 - Crie um banco de dados no seu servidor (ex: library_system).


2 - No seu arquivo .env, configure as credenciais:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_system
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

## Opção B: Sqlite
Crie o arquivo do banco:

```bash
touch database/database.sqlite
```

No seu arquivo .env, altere a conexão e comente as linhas de host/porta
```bash
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1...
```

> Se quiser usar MySQL, edite o `.env` e descomente as variáveis `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD`, alterando `DB_CONNECTION=sqlite` para `DB_CONNECTION=mysql`.

### 5. Execute as migrations

```bash
php artisan migrate
```

Isso criará as tabelas: `users`, `books`, `loans`, `personal_access_tokens`, `jobs`, `cache` e `sessions`.

### 6. Popule o banco com dados iniciais

```bash
php artisan db:seed
```
# AVISO
O seeder de livros pode falhar caso o limite de requisições diaria seja ultrapassada, nesse caso sera necessario criar livros manualmente.

O seeder cria:
- **1 usuário administrador:** `admin@gmail.com` / `admin123`
- **10 usuários comuns** gerados automaticamente via factory
- **Livros** importados da Google Books API nas categorias Ficção Científica, Romance e Drama

> ⚠️ O `BookSeeder` faz requisições à API do Google Books. Certifique-se de ter conexão com a internet ao rodar o seed.

### 7. Instale as dependências JavaScript

```bash
npm install
```

---

## Rodando o projeto

### Modo desenvolvimento 

O projeto de dois terminais abertos, um para rodar o front e outro para rodar o back, o comando pra rodar cada um vai ficar abaixo:

```bash
npm run dev
php artisan serve
```
Acesse o sistema em: **http://localhost:8000/login**

---

## Configuração de e-mail

O projeto usa `MAIL_MAILER=log` por padrão — os e-mails são gravados no arquivo de log em vez de serem enviados de verdade. É a configuração ideal para desenvolvimento.

Para visualizar os e-mails gerados:

```bash
tail -f storage/logs/laravel.log
```

Para usar o **Mailtrap** (simula caixa de entrada real), edite o `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario_mailtrap
MAIL_PASSWORD=sua_senha_mailtrap
MAIL_FROM_ADDRESS="sistema@libraryms.com"
MAIL_FROM_NAME="LibrarySystem"
```

---

## Agendador de tarefas (notificações de prazo)

O sistema verifica automaticamente os empréstimos próximos do vencimento e envia e-mail quando faltam 12 horas ou menos.

Para que isso funcione em produção, adicione o agendador do Laravel ao cron do servidor:

```bash
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

Para testar manualmente o envio de lembretes:

```bash
php artisan loans:send-reminders
```

---

## Fila de jobs

O `QUEUE_CONNECTION=database` já está configurado no `.env.example`. A fila é necessária para o envio assíncrono de e-mails.

O É necessario rodar o comando para iniciar o worker manualmente!:

```bash
php artisan queue:listen --tries=1
```

---

## Credenciais padrão (após seed)

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | admin@gmail.com | admin123 |
| Usuário comum | *(gerado aleatoriamente)* | password |

Para ver os e-mails dos usuários gerados:

```bash
php artisan tinker
>>> App\Models\User::where('is_admin', false)->pluck('email', 'id')
```

---

## Funcionalidades

### Livros
- Listagem pública com busca por título e paginação (10 por página)
- Cadastro, edição e exclusão para usuários autenticados
- Usuário comum só edita/exclui os próprios livros
- Administrador pode editar e excluir qualquer livro

### Autenticação
- Registro e login via Laravel Sanctum (token Bearer)
- Dois níveis de acesso: administrador e usuário comum

### Empréstimos
- Usuário autenticado pode emprestar livros disponíveis
- Limite de 3 empréstimos simultâneos por usuário
- Prazo de devolução de 2 dias
- Livro emprestado fica indisponível até a devolução

### Painel administrativo
- Dashboard com estatísticas: total de livros, disponíveis, emprestados e atrasados
- Listagem de todos os empréstimos ativos com data de empréstimo e vencimento
- Registro de devolução de livros

### Meus Empréstimos (usuário comum)
- Visualização dos próprios empréstimos ativos
- Indicação visual de empréstimos em dia ou atrasados
- Somente leitura — sem ação de devolução

### Notificações
- E-mail automático enviado quando faltam 12 horas ou menos para o vencimento
- Cada empréstimo recebe no máximo um aviso (`notification_sent`)

---

## Estrutura do projeto

```
app/
  Console/Commands/    → SendLoanReminders (comando de notificação)
  Http/Controllers/    → AuthController, BookController, LoanController, UserController
  Http/Requests/       → BookRequest, LoginRequest, RegisterRequest, StoreLoanRequest
  Mail/                → LoanDueReminder (template de e-mail)
  Models/              → User, Book, Loan
  Policies/            → BookPolicy (autorização de edição/exclusão)
  Services/            → AuthService, BookService, LoanService

database/
  migrations/          → users, books, loans, tokens, jobs, cache
  seeders/             → DatabaseSeeder, BookSeeder

resources/js/src/
  views/               → Books, Loans, MyLoans, Login, Register, Users, Dashboard
  views/layouts/       → MainLayout (sidebar + navegação)
  router/              → index.js (rotas e guards)
  store/               → auth.js (Pinia)

routes/
  api.php              → todas as rotas da API REST
  console.php          → agendamento do loans:send-reminders
```

---

## Comandos úteis

```bash
# Resetar banco e rodar seeders do zero
php artisan migrate:fresh --seed

# Ver rotas da API
php artisan route:list --path=api

# Testar envio de lembretes manualmente
php artisan loans:send-reminders
```

## Diagramas do projeto:

Estruturas das tabelas:

<img width="1090" height="491" alt="Image" src="https://github.com/user-attachments/assets/34c039d1-03a4-4112-b889-ebc6fc28007d" />

Diagrama do projeto:

![Image](https://github.com/user-attachments/assets/8ccce386-1953-42d1-ae16-2d1154ab0559)

# Template do email de aviso de vencimentto do prazo de devolução
<img width="1691" height="734" alt="Image" src="https://github.com/user-attachments/assets/7f7be230-0e58-4fe2-b40a-c5e7d8808c11" />

# Acessar o tinker (console interativo)
```bash
php artisan tinker
```
