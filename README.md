# 🎟️ Laravel Prize Draw System

A backend API built with **Laravel 12** for managing prize draws. Users can buy multiple tickets to increase their chances of winning. The system supports random winner selection, draw summaries, and API authentication with **Sanctum**.  

---

## ⚡ Features

- Create **draws** and assign tickets to users  
- Users can have **multiple tickets**, increasing their winning probability  
- Randomly pick a **winner**, weighted by ticket count  
- View a **full summary** of a draw, including participants, total tickets, and winner  
- Authentication via **Laravel Sanctum** for secure API access  
- Optimized for **large datasets** (10,000+ tickets)  

---

## 🏗️ Installation

```bash
git clone https://github.com/yourusername/laravel-prize-draw.git
cd laravel-prize-draw
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

Update .env with your database credentials before running migrations.

🔑 Authentication (Sanctum)
Register
curl -X POST http://127.0.0.1:8000/api/auth/register \
-H "Content-Type: application/json" \
-d '{"name":"John Doe","email":"john@example.com","password":"password"}'


Response:

{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "1|abcdef1234567890"
}

Login
curl -X POST http://127.0.0.1:8000/api/auth/login \
-H "Content-Type: application/json" \
-d '{"email":"john@example.com","password":"password"}'


Response:

{
  "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
  "token": "1|abcdef1234567890"
}

```
---

## 🎯 API Endpoints
1. List All Draws

```bash
curl -H "Authorization: Bearer <token>" http://127.0.0.1:8000/api/draws


Response:

[
  { "id": 1, "name": "Weekly Draw", "status": "open" },
  { "id": 2, "name": "Monthly Draw", "status": "completed" }
]

2. Draw Full Summary
curl -H "Authorization: Bearer <token>" \
http://127.0.0.1:8000/api/draws/1/full-summary


Response:

{
  "draw": { "id": 1, "name": "Weekly Draw", "status": "completed" },
  "winner": {
    "id": 10,
    "name": "Alice Smith",
    "tickets_in_draw": 7
  },
  "total_tickets": 1000,
  "participants": [
    { "id": 10, "name": "Alice Smith", "email": "alice@example.com", "tickets_count": 7 },
    { "id": 11, "name": "Bob Lee", "email": "bob@example.com", "tickets_count": 5 }
  ]
}
```

3. Pick a Winner

```bash
curl -X POST -H "Authorization: Bearer <token>" \
http://127.0.0.1:8000/api/draws/1/pick-winner


Response:

{
  "message": "Winner selected successfully",
  "winner": "Alice Smith",
  "ticket_code": "TKQ8MCZ5"
}

4. Get User Info
curl -H "Authorization: Bearer <token>" http://127.0.0.1:8000/api/users/10


Response:

{
  "id": 10,
  "name": "Alice Smith",
  "email": "alice@example.com",
  "tickets": [
    { "id": 101, "draw_id": 1, "code": "TKQ8MCZ5" },
    { "id": 102, "draw_id": 2, "code": "ABCD1234" }
  ]
}
```

---

## 🧩 Models & Relationships

```bash
Model	Relationship	        Related Model
Draw	tickets() → hasMany	    Ticket
Draw	winner() → belongsTo	User
Ticket	user() → belongsTo	    User
Ticket	draw() → belongsTo	    Draw
User	tickets() → hasMany	    Ticket
```

### ⚙️ Seeder Example

```bash
$users = User::factory(10000)->create();
$draws = Draw::factory(1)->create();

foreach ($draws as $draw) {
    foreach ($users as $user) {
        $ticketCount = rand(1, 500);
        Ticket::factory($ticketCount)->create([
            'user_id' => $user->id,
            'draw_id' => $draw->id,
        ]);
    }
}
```
---

## 🚀 Performance Tips

Aggregate tickets per user instead of loading all tickets for large draws.

Use pagination for ticket-heavy draws.

Consider caching draw summaries to speed up repeated requests.

---

## 📝 Contribution

Fork the repository

Create a feature branch (git checkout -b feature/new-feature)

Commit your changes (git commit -am 'Add new feature')

Push to the branch (git push origin feature/new-feature)

Open a Pull Request

⚖️ License

MIT License. See LICENSE
 file for details.
