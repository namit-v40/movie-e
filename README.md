# Filterable Trait for Laravel

## 🚀 Introduction
The `Filterable` trait provides a convenient way to filter and sort data dynamically via query parameters in Laravel applications. It supports:

- Filtering by conditions (`=, >, <, >=, <=`)
- Filtering by relations (`profile.age, orders.total_price`)
- Date range filters (`today, this_week, this_month`)
- Null and Not Null checks
- Full-text search across multiple columns (including relations)
- Sorting by multiple columns (including relations)

## 🛠 Installation

1. Copy the `Filterable.php` trait into your `app/Traits` directory.
2. Use the trait inside your Eloquent models.

## 📌 Usage

### 1️⃣ Enable Filtering & Sorting in Model
```php
use App\Traits\Filterable;

class User extends Model
{
    use Filterable;

    protected $searchable = ['name', 'email', 'profile.age'];
}
```

### 2️⃣ Apply Filtering & Sorting in Controller
```php
use App\Models\User;

public function index(Request $request)
{
    $users = User::filter($request->all())
                 ->sort($request->get('sort', 'created_at'))
                 ->paginate(10);

    return response()->json($users);
}
```

## 🎯 Filtering

### 🔹 Basic Filtering
| Query Parameter | SQL Equivalent |
|---------------|----------------|
| `?age__gt=18` | `WHERE age > 18` |
| `?profile__age__lte=30` | `WHERE profile.age <= 30` |

### 🔹 Filtering with `whereIn`

| Query Parameter               | SQL Equivalent                           |
| ----------------------------- | ---------------------------------------- |
| `?status__in=active,inactive` | `WHERE status IN ('active', 'inactive')` |
| `?role_id__in=1,2,3`          | `WHERE role_id IN (1, 2, 3)`             |

### 🔹 Date Range Filtering
| Query Parameter | SQL Equivalent |
|---------------|----------------|
| `?created_at__range=today` | `WHERE created_at BETWEEN start_of_day AND end_of_day` |
| `?created_at__range=this_week` | `WHERE created_at BETWEEN start_of_week AND end_of_week` |

### 🔹 NULL & NOT NULL Filters
| Query Parameter | SQL Equivalent |
|---------------|----------------|
| `?is_null=profile.avatar` | `WHERE profile.avatar IS NULL` |
| `?is_not_null=email` | `WHERE email IS NOT NULL` |

### 🔹 Search (Supports Relations)
| Query Parameter | SQL Equivalent |
|---------------|----------------|
| `?search=John` | `WHERE name LIKE '%John%' OR email LIKE '%John%' OR profile.age LIKE '%John%'` |

### 🔹 Special Function Filters
| Query Parameter | SQL Equivalent |
|---------------|----------------|
| `?email__lower=john@gmail.com` | `WHERE LOWER(email) = 'john@gmail.com'` |
| `?name__length=4` | `WHERE LENGTH(name) = 4` |
| `?created_at__year=2024` | `WHERE YEAR(created_at) = 2024` |

## 🔄 Sorting

| Query Parameter | SQL Equivalent |
|---------------|----------------|
| `?sort=name` | `ORDER BY name ASC` |
| `?sort=-name` | `ORDER BY name DESC` |
| `?sort=email,-created_at` | `ORDER BY email ASC, created_at DESC` |
| `?sort=profile__age` | `ORDER BY (JOIN profile.age) ASC` |

## Example

📌 Filter Users Older Than 18 Years Old
```
GET /users?profile__age__gt=18
```
➡ SQL:
```sql
SELECT * FROM users 
JOIN profiles ON profiles.user_id = users.id
WHERE profiles.age > 18;
```

📌 Filter Users by Status List (whereIn)
```
GET /users?status__in=active,inactive
```
➡ SQL:
```sql
SELECT * FROM users WHERE status IN ('active', 'inactive');
```

📌  Filter Users by Today’s Creation Date (range)
```
GET /users?created_at__range=today
```
➡ SQL:
```sql
SELECT * FROM users 
WHERE created_at BETWEEN '2024-02-13 00:00:00' AND '2024-02-13 23:59:59';
```

📌 Search for Users with name or email containing “John”
```
GET /users?search=John
```
➡ SQL:
```sql
SELECT * FROM users 
WHERE name LIKE '%John%' 
   OR email LIKE '%John%';
```

📌 Combine multiple conditions
```
GET /users?profile__age__gte=20&status__in=active,inactive&search=John&sort=-created_at
```
➡ SQL:
```sql
SELECT * FROM users 
JOIN profiles ON profiles.user_id = users.id
WHERE profiles.age >= 20
  AND status IN ('active', 'inactive')
  AND (name LIKE '%John%' OR email LIKE '%John%')
ORDER BY created_at DESC;
```

## ✅ Features
- 🔥 **Flexible Filtering** (Supports conditions, null checks, and date ranges)
- 🔍 **Deep Search** (Search in multiple fields, including relations)
- 📊 **Dynamic Sorting** (Sort by multiple columns, including relations)
- 📌 **Easy to Integrate** (Works seamlessly with Laravel models & controllers)

---

🚀 **Try it now in your Laravel project!** 😎
