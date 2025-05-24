# API Documentation

## Base URL
```
http://your-domain.com/api
```

## Authentication
All protected routes require a Bearer token in the Authorization header:
```
Authorization: Bearer <your_token>
```

## Endpoints

### Authentication

#### Register
```http
POST /register
```
Request Body:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login
```http
POST /login
```
Request Body:
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

#### Logout
```http
POST /logout
```
Headers:
```
Authorization: Bearer <token>
```

### Events

#### List Events
```http
GET /events
```
Query Parameters:
- `search` (optional): Search in title, description, and location
- `status` (optional): Filter by event status
- `event_type` (optional): Filter by event type
- `date_from` (optional): Filter by start date (YYYY-MM-DD)
- `date_to` (optional): Filter by end date (YYYY-MM-DD)
- `sort_by` (optional): Sort by field (default: event_date)
- `sort_direction` (optional): Sort direction (asc/desc, default: desc)
- `per_page` (optional): Items per page (default: 10)

#### Create Event
```http
POST /events
```
Request Body:
```json
{
    "title": "Summer Charity Event",
    "description": "Annual summer charity event",
    "content": "Detailed content about the event",
    "image": "file",
    "event_date": "2024-06-15",
    "event_time": "14:00",
    "venue": "City Hall",
    "status": true,
    "featured": false
}
```

#### Get Event
```http
GET /events/{id}
```

#### Update Event
```http
PUT /events/{id}
```
Request Body: Same as Create Event

#### Delete Event
```http
DELETE /events/{id}
```

### Donations

#### List Donations
```http
GET /donations
```
Query Parameters:
- `search` (optional): Search in donor name, email, and transaction ID
- `payment_status` (optional): Filter by payment status
- `payment_method` (optional): Filter by payment method
- `min_amount` (optional): Filter by minimum amount
- `max_amount` (optional): Filter by maximum amount
- `date_from` (optional): Filter by start date (YYYY-MM-DD)
- `date_to` (optional): Filter by end date (YYYY-MM-DD)
- `sort_by` (optional): Sort by field (default: created_at)
- `sort_direction` (optional): Sort direction (asc/desc, default: desc)
- `per_page` (optional): Items per page (default: 10)

#### Create Donation
```http
POST /donations
```
Request Body:
```json
{
    "full_name": "John Doe",
    "email": "john@example.com",
    "mobile_number": "1234567890",
    "address": "123 Main St",
    "amount": 100.00,
    "payment_method": "credit_card",
    "status": "pending",
    "transaction_id": "txn_123456"
}
```

#### Get Donation
```http
GET /donations/{id}
```

#### Update Donation
```http
PUT /donations/{id}
```
Request Body: Same as Create Donation

#### Delete Donation
```http
DELETE /donations/{id}
```

#### Get Total Donations
```http
GET /donations/total
```
Response:
```json
{
    "total_amount": 5000.00,
    "total_donations": 50
}
```

#### Get Recent Donations
```http
GET /donations/recent
```

### Gallery

#### List Gallery Items
```http
GET /galleries
```
Query Parameters:
- `search` (optional): Search in title and description
- `category_id` (optional): Filter by category
- `status` (optional): Filter by status (active/inactive)
- `sort_by` (optional): Sort by field (default: created_at)
- `sort_direction` (optional): Sort direction (asc/desc, default: desc)
- `per_page` (optional): Items per page (default: 10)

#### Create Gallery Item
```http
POST /galleries
```
Request Body:
```json
{
    "title": "Summer Event Photos",
    "description": "Photos from summer charity event",
    "image": "file",
    "category_id": 1,
    "status": "active"
}
```

#### Get Gallery Item
```http
GET /galleries/{id}
```

#### Update Gallery Item
```http
PUT /galleries/{id}
```
Request Body: Same as Create Gallery Item

#### Delete Gallery Item
```http
DELETE /galleries/{id}
```

### Categories

#### List Categories
```http
GET /categories
```
Query Parameters:
- `search` (optional): Search in name and description
- `sort_by` (optional): Sort by field (default: created_at)
- `sort_direction` (optional): Sort direction (asc/desc, default: desc)
- `per_page` (optional): Items per page (default: 10)

#### Create Category
```http
POST /categories
```
Request Body:
```json
{
    "name": "Events",
    "description": "Event photos"
}
```

#### Get Category
```http
GET /categories/{id}
```

#### Update Category
```http
PUT /categories/{id}
```
Request Body: Same as Create Category

#### Delete Category
```http
DELETE /categories/{id}
```

### Suggestions

#### Submit Suggestion
```http
POST /suggestions
```
Request Body:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "1234567890",
    "description": "I have a suggestion for improving the website..."
}
```

#### List Suggestions (Admin)
```http
GET /suggestions
```
Query Parameters:
- `search` (optional): Search in name, email, phone, and description
- `is_read` (optional): Filter by read status (true/false)
- `date_from` (optional): Filter by start date (YYYY-MM-DD)
- `date_to` (optional): Filter by end date (YYYY-MM-DD)
- `sort_by` (optional): Sort by field (default: created_at)
- `sort_direction` (optional): Sort direction (asc/desc, default: desc)
- `per_page` (optional): Items per page (default: 10)

#### Get Suggestion (Admin)
```http
GET /suggestions/{id}
```

#### Mark Suggestion as Read (Admin)
```http
POST /suggestions/{id}/mark-as-read
```

#### Mark Suggestion as Unread (Admin)
```http
POST /suggestions/{id}/mark-as-unread
```

#### Delete Suggestion (Admin)
```http
DELETE /suggestions/{id}
```

## Response Format

All API responses follow this format:
```json
{
    "data": {}, // Response data
    "message": "Success message", // Optional success message
    "errors": {} // Optional error messages
}
```

## Error Codes

- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error

## Pagination

Paginated responses include:
```json
{
    "data": [],
    "current_page": 1,
    "per_page": 10,
    "total": 100,
    "last_page": 10,
    "from": 1,
    "to": 10
}
```

## File Upload

For endpoints requiring file uploads:
- Use `multipart/form-data` content type
- Maximum file size: 2MB
- Allowed image types: jpeg, png, jpg, gif 
