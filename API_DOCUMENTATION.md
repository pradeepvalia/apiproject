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
    "name": "Test User",         // required, string, max:255
    "email": "test@example.com", // required, valid email, unique
    "password": "password123",   // required, min:8
    "password_confirmation": "password123", // required, must match password
    "phone": "1234567890",      // required, string, max:20
    "address": "123 Test St"    // optional, string, max:255
}
```

Response (201 Created):

```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "phone": "1234567890",
        "address": "123 Test St",
        "is_active": true,
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    },
    "token": "1|example_token_string"
}
```

#### Login

```http
POST /login
```

Request Body:

```json
{
    "email": "test@example.com",    // required, valid email
    "password": "password123"       // required
}
```

Response (200 OK):

```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "phone": "1234567890",
        "address": "123 Test St",
        "is_active": true,
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    },
    "token": "1|example_token_string"
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

Response (200 OK):
```json
{
    "message": "Successfully logged out"
}
```

#### Get User Details

```http
GET /user
```

Headers:
```
Authorization: Bearer <token>
```

Response (200 OK):
```json
{
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "phone": "1234567890",
    "address": "123 Test St",
    "is_active": true,
    "created_at": "2024-05-25T11:00:00.000000Z",
    "updated_at": "2024-05-25T11:00:00.000000Z"
}
```

#### Forgot Password

```http
POST /forgot-password
```

Request Body:
```json
{
    "email": "test@example.com"    // required, valid email that exists in the system
}
```

Response (200 OK):
```json
{
    "message": "Password reset link sent to your email"
}
```

### Events

#### List Events

```http
GET /events
```

Query Parameters:

-   `search` (optional): Search in title, description, and location
-   `status` (optional): Filter by event status (active/inactive)
-   `event_type` (optional): Filter by event type (conference, workshop, etc.)
-   `date_from` (optional): Filter by start date (YYYY-MM-DD)
-   `date_to` (optional): Filter by end date (YYYY-MM-DD)
-   `sort_by` (optional): Sort by field (default: event_date)
-   `sort_direction` (optional): Sort direction (asc/desc, default: desc)
-   `per_page` (optional): Items per page (default: 10)

Response (200 OK):
```json
{
    "data": [
        {
            "id": 1,
            "title": "Event Title",
            "description": "Event Description",
            "content": "Event Content",
            "image": "events/image.jpg",
            "start_date": "2024-06-15",
            "end_date": "2024-06-16",
            "event_time": "14:00:00",
            "venue": "Event Venue",
            "status": 1,
            "featured": 0,
            "event_type": "conference",
            "created_at": "2024-05-25T11:00:00.000000Z",
            "updated_at": "2024-05-25T11:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

#### Create Event

```http
POST /events
```

Headers:
```
Authorization: Bearer <token>
```

Request Body:

```json
{
    "title": "Event Title",           // required, string, max:255
    "description": "Event Description", // required, string
    "content": "Event Content",       // required, string
    "image": "events/image.jpg",      // required, string
    "start_date": "2024-06-15",      // required, date (YYYY-MM-DD)
    "end_date": "2024-06-16",        // required, date (YYYY-MM-DD)
    "event_time": "14:00:00",        // required, time (HH:MM:SS)
    "venue": "Event Venue",          // required, string, max:255
    "status": 1,                     // required, integer (0 or 1)
    "featured": 0,                   // required, integer (0 or 1)
    "event_type": "conference"       // required, string
}
```

Response (201 Created):
```json
{
    "message": "Event created successfully",
    "data": {
        "id": 1,
        "title": "Event Title",
        "description": "Event Description",
        "content": "Event Content",
        "image": "events/image.jpg",
        "start_date": "2024-06-15",
        "end_date": "2024-06-16",
        "event_time": "14:00:00",
        "venue": "Event Venue",
        "status": 1,
        "featured": 0,
        "event_type": "conference",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Get Event

```http
GET /events/{id}
```

Response (200 OK):
```json
{
    "data": {
        "id": 1,
        "title": "Event Title",
        "description": "Event Description",
        "content": "Event Content",
        "image": "events/image.jpg",
        "start_date": "2024-06-15",
        "end_date": "2024-06-16",
        "event_time": "14:00:00",
        "venue": "Event Venue",
        "status": 1,
        "featured": 0,
        "event_type": "conference",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Update Event

```http
PUT /events/{id}
```

Headers:
```
Authorization: Bearer <token>
```

Request Body: Same as Create Event

Response (200 OK):
```json
{
    "message": "Event updated successfully",
    "data": {
        "id": 1,
        "title": "Updated Event Title",
        "description": "Updated Event Description",
        "content": "Updated Event Content",
        "image": "events/updated-image.jpg",
        "start_date": "2024-06-15",
        "end_date": "2024-06-16",
        "event_time": "14:00:00",
        "venue": "Updated Event Venue",
        "status": 1,
        "featured": 0,
        "event_type": "conference",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Delete Event

```http
DELETE /events/{id}
```

Headers:
```
Authorization: Bearer <token>
```

Response (200 OK):
```json
{
    "message": "Event deleted successfully"
}
```

### Donations

#### List Donations

```http
GET /donations
```

Headers:
```
Authorization: Bearer <token>
```

Query Parameters:

-   `search` (optional): Search in donor name, email, and transaction ID
-   `payment_status` (optional): Filter by payment status (pending, completed, failed)
-   `payment_method` (optional): Filter by payment method (credit_card, bank_transfer, etc.)
-   `min_amount` (optional): Filter by minimum amount
-   `max_amount` (optional): Filter by maximum amount
-   `date_from` (optional): Filter by start date (YYYY-MM-DD)
-   `date_to` (optional): Filter by end date (YYYY-MM-DD)
-   `sort_by` (optional): Sort by field (default: created_at)
-   `sort_direction` (optional): Sort direction (asc/desc, default: desc)
-   `per_page` (optional): Items per page (default: 10)

Response (200 OK):
```json
{
    "data": [
        {
            "id": 1,
            "full_name": "John Doe",
            "email": "john@example.com",
            "mobile_number": "1234567890",
            "address": "123 Main St",
            "amount": 100.0,
            "payment_method": "credit_card",
            "status": "completed",
            "transaction_id": "txn_123456",
            "created_at": "2024-05-25T11:00:00.000000Z",
            "updated_at": "2024-05-25T11:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

#### Create Donation

```http
POST /donations
```

Headers:
```
Authorization: Bearer <token>
```

Request Body:

```json
{
    "full_name": "John Doe",         // required, string, max:255
    "email": "john@example.com",     // required, valid email
    "mobile_number": "1234567890",   // required, string, max:20
    "address": "123 Main St",        // required, string, max:255
    "amount": 100.0,                 // required, numeric, min:1
    "payment_method": "credit_card", // required, string
    "status": "pending",             // required, string
    "transaction_id": "txn_123456"   // required, string, unique
}
```

Response (201 Created):
```json
{
    "message": "Donation created successfully",
    "data": {
        "id": 1,
        "full_name": "John Doe",
        "email": "john@example.com",
        "mobile_number": "1234567890",
        "address": "123 Main St",
        "amount": 100.0,
        "payment_method": "credit_card",
        "status": "pending",
        "transaction_id": "txn_123456",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Get Donation

```http
GET /donations/{id}
```

Headers:
```
Authorization: Bearer <token>
```

Response (200 OK):
```json
{
    "data": {
        "id": 1,
        "full_name": "John Doe",
        "email": "john@example.com",
        "mobile_number": "1234567890",
        "address": "123 Main St",
        "amount": 100.0,
        "payment_method": "credit_card",
        "status": "completed",
        "transaction_id": "txn_123456",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Update Donation

```http
PUT /donations/{id}
```

Headers:
```
Authorization: Bearer <token>
```

Request Body: Same as Create Donation

Response (200 OK):
```json
{
    "message": "Donation updated successfully",
    "data": {
        "id": 1,
        "full_name": "John Doe",
        "email": "john@example.com",
        "mobile_number": "1234567890",
        "address": "123 Main St",
        "amount": 100.0,
        "payment_method": "credit_card",
        "status": "completed",
        "transaction_id": "txn_123456",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Delete Donation

```http
DELETE /donations/{id}
```

Headers:
```
Authorization: Bearer <token>
```

Response (200 OK):
```json
{
    "message": "Donation deleted successfully"
}
```

#### Get Total Donations

```http
GET /donations/total
```

Headers:
```
Authorization: Bearer <token>
```

Response (200 OK):
```json
{
    "total_amount": 5000.0,
    "total_donations": 50,
    "currency": "USD"
}
```

#### Get Recent Donations

```http
GET /donations/recent
```

Headers:
```
Authorization: Bearer <token>
```

Query Parameters:
-   `limit` (optional): Number of recent donations to return (default: 5)

Response (200 OK):
```json
{
    "data": [
        {
            "id": 1,
            "full_name": "John Doe",
            "email": "john@example.com",
            "amount": 100.0,
            "payment_method": "credit_card",
            "status": "completed",
            "created_at": "2024-05-25T11:00:00.000000Z"
        }
    ]
}
```

### Gallery

#### List Gallery Items

```http
GET /galleries
```

Query Parameters:

-   `search` (optional): Search in title and description
-   `category_id` (optional): Filter by category ID
-   `status` (optional): Filter by status (active/inactive)
-   `sort_by` (optional): Sort by field (default: created_at)
-   `sort_direction` (optional): Sort direction (asc/desc, default: desc)
-   `per_page` (optional): Items per page (default: 10)

Response (200 OK):
```json
{
    "data": [
        {
            "id": 1,
            "title": "Summer Event Photos",
            "description": "Photos from summer charity event",
            "image": "galleries/summer-event.jpg",
            "category_id": 1,
            "status": "active",
            "created_at": "2024-05-25T11:00:00.000000Z",
            "updated_at": "2024-05-25T11:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

#### Create Gallery Item

```http
POST /galleries
```

Headers:
```
Authorization: Bearer <token>
```

Request Body:

```json
{
    "title": "Summer Event Photos",     // required, string, max:255
    "description": "Photos from summer charity event", // required, string
    "image": "file",                   // required, image file (jpg, jpeg, png)
    "category_id": 1,                  // required, integer, exists in categories table
    "status": "active"                 // required, string (active/inactive)
}
```

Response (201 Created):
```json
{
    "message": "Gallery item created successfully",
    "data": {
        "id": 1,
        "title": "Summer Event Photos",
        "description": "Photos from summer charity event",
        "image": "galleries/summer-event.jpg",
        "category_id": 1,
        "status": "active",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Get Gallery Item

```http
GET /galleries/{id}
```

Response (200 OK):
```json
{
    "data": {
        "id": 1,
        "title": "Summer Event Photos",
        "description": "Photos from summer charity event",
        "image": "galleries/summer-event.jpg",
        "category_id": 1,
        "status": "active",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Update Gallery Item

```http
PUT /galleries/{id}
```

Headers:
```
Authorization: Bearer <token>
```

Request Body: Same as Create Gallery Item

Response (200 OK):
```json
{
    "message": "Gallery item updated successfully",
    "data": {
        "id": 1,
        "title": "Updated Summer Event Photos",
        "description": "Updated photos from summer charity event",
        "image": "galleries/updated-summer-event.jpg",
        "category_id": 1,
        "status": "active",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Delete Gallery Item

```http
DELETE /galleries/{id}
```

Headers:
```
Authorization: Bearer <token>
```

Response (200 OK):
```json
{
    "message": "Gallery item deleted successfully"
}
```

#### Get Gallery Categories

```http
GET /gallery-categories
```

Response (200 OK):
```json
{
    "data": [
        {
            "id": 1,
            "name": "Events",
            "description": "Event photos",
            "status": "active",
            "created_at": "2024-05-25T11:00:00.000000Z",
            "updated_at": "2024-05-25T11:00:00.000000Z"
        }
    ]
}
```

#### Create Gallery Category

```http
POST /gallery-categories
```

Headers:
```
Authorization: Bearer <token>
```

Request Body:
```json
{
    "name": "Events",              // required, string, max:255
    "description": "Event photos", // required, string
    "status": "active"            // required, string (active/inactive)
}
```

Response (201 Created):
```json
{
    "message": "Gallery category created successfully",
    "data": {
        "id": 1,
        "name": "Events",
        "description": "Event photos",
        "status": "active",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Update Gallery Category

```http
PUT /gallery-categories/{id}
```

Headers:
```
Authorization: Bearer <token>
```

Request Body: Same as Create Gallery Category

Response (200 OK):
```json
{
    "message": "Gallery category updated successfully",
    "data": {
        "id": 1,
        "name": "Updated Events",
        "description": "Updated event photos",
        "status": "active",
        "created_at": "2024-05-25T11:00:00.000000Z",
        "updated_at": "2024-05-25T11:00:00.000000Z"
    }
}
```

#### Delete Gallery Category

```http
DELETE /gallery-categories/{id}
```

Headers:
```
Authorization: Bearer <token>
```

Response (200 OK):
```json
{
    "message": "Gallery category deleted successfully"
}
```

### Categories

#### List Categories

```http
GET /categories
```

Query Parameters:

-   `search` (optional): Search in name and description
-   `sort_by` (optional): Sort by field (default: created_at)
-   `sort_direction` (optional): Sort direction (asc/desc, default: desc)
-   `per_page` (optional): Items per page (default: 10)

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

-   `search` (optional): Search in name, email, phone, and description
-   `is_read` (optional): Filter by read status (true/false)
-   `date_from` (optional): Filter by start date (YYYY-MM-DD)
-   `date_to` (optional): Filter by end date (YYYY-MM-DD)
-   `sort_by` (optional): Sort by field (default: created_at)
-   `sort_direction` (optional): Sort direction (asc/desc, default: desc)
-   `per_page` (optional): Items per page (default: 10)

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

-   200: Success
-   201: Created
-   400: Bad Request
-   401: Unauthorized
-   403: Forbidden
-   404: Not Found
-   422: Validation Error
-   500: Server Error

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

-   Use `multipart/form-data` content type
-   Maximum file size: 2MB
-   Allowed image types: jpeg, png, jpg, gif

## Authentication Endpoints

### Register User

-   **URL**: `/api/register`
-   **Method**: `POST`
-   **Description**: Register a new user in the system
-   **Request Body**:
    ```json
    {
        "name": "string (required)",
        "email": "string (required, valid email)",
        "password": "string (required, min: 8 characters)",
        "password_confirmation": "string (required, must match password)",
        "phone": "string (required, max: 20 characters)",
        "address": "string (optional, max: 255 characters)"
    }
    ```
-   **Success Response (201)**:
    ```json
    {
        "message": "User registered successfully",
        "user": {
            "id": "integer",
            "name": "string",
            "email": "string",
            "phone": "string",
            "address": "string|null",
            "is_active": true,
            "created_at": "timestamp",
            "updated_at": "timestamp"
        },
        "token": "string"
    }
    ```
-   **Error Responses**:
    -   **422 Validation Failed**:
        ```json
        {
            "message": "Validation failed",
            "errors": {
                "field": ["error message"]
            }
        }
        ```
    -   **500 Server Error**:
        ```json
        {
            "message": "Registration failed",
            "error": "error message"
        }
        ```

### Login

-   **URL**: `/api/login`
-   **Method**: `POST`
-   **Description**: Authenticate user and get access token
-   **Request Body**:
    ```json
    {
        "email": "string (required, valid email)",
        "password": "string (required)"
    }
    ```
-   **Success Response (200)**:
    ```json
    {
      "message": "Login successful",
      "user": {
        "id": "integer",
        "name": "string",
        "email": "string",
        "phone": "string",
        "address": "string|null",
        "is_active": boolean,
        "created_at": "timestamp",
        "updated_at": "timestamp"
      },
      "token": "string"
    }
    ```
-   **Error Responses**:
    -   **422 Validation Failed**:
        ```json
        {
            "message": "Validation failed",
            "errors": {
                "email": ["The provided credentials are incorrect."]
            }
        }
        ```
    -   **500 Server Error**:
        ```json
        {
            "message": "Login failed",
            "error": "error message"
        }
        ```

### Logout

-   **URL**: `/api/logout`
-   **Method**: `POST`
-   **Description**: Invalidate user's access token
-   **Headers Required**:
    -   `Authorization: Bearer {token}`
-   **Success Response (200)**:
    ```json
    {
        "message": "Successfully logged out"
    }
    ```
-   **Error Response (500)**:
    ```json
    {
        "message": "Logout failed",
        "error": "error message"
    }
    ```

### Get User Details

-   **URL**: `/api/user`
-   **Method**: `GET`
-   **Description**: Get authenticated user's details
-   **Headers Required**:
    -   `Authorization: Bearer {token}`
-   **Success Response (200)**:
    ```json
    {
      "id": "integer",
      "name": "string",
      "email": "string",
      "phone": "string",
      "address": "string|null",
      "is_active": boolean,
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
    ```
-   **Error Response (500)**:
    ```json
    {
        "message": "Failed to fetch user details",
        "error": "error message"
    }
    ```

## Important Notes

1. **Authentication**:

    - All endpoints except `register` and `login` require authentication
    - Use the Bearer token received from login/register in the Authorization header

2. **Validation Rules**:

    - Name: Required, maximum 255 characters
    - Email: Required, valid email format, must be unique
    - Password: Required, minimum 8 characters
    - Phone: Required, maximum 20 characters
    - Address: Optional, maximum 255 characters

3. **Error Handling**:

    - All endpoints return appropriate HTTP status codes
    - Validation errors return 422 status code with detailed error messages
    - Server errors return 500 status code with error details
    - Authentication errors return 401 status code

4. **Response Format**:
    - All responses are in JSON format
    - Success responses include relevant data and success message
    - Error responses include error message and details

## Public Events

### List Public Events
- **URL**: `/api/public/events`
- **Method**: `GET`
- **Query Parameters**: Same as List Events
- **Note**: Only returns active events by default
