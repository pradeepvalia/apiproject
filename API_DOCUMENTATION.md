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
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "1234567890",
    "address": "123 Test Street"
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

-   `search` (optional): Search in title, description, and location
-   `status` (optional): Filter by event status
-   `event_type` (optional): Filter by event type
-   `date_from` (optional): Filter by start date (YYYY-MM-DD)
-   `date_to` (optional): Filter by end date (YYYY-MM-DD)
-   `sort_by` (optional): Sort by field (default: event_date)
-   `sort_direction` (optional): Sort direction (asc/desc, default: desc)
-   `per_page` (optional): Items per page (default: 10)

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

-   `search` (optional): Search in donor name, email, and transaction ID
-   `payment_status` (optional): Filter by payment status
-   `payment_method` (optional): Filter by payment method
-   `min_amount` (optional): Filter by minimum amount
-   `max_amount` (optional): Filter by maximum amount
-   `date_from` (optional): Filter by start date (YYYY-MM-DD)
-   `date_to` (optional): Filter by end date (YYYY-MM-DD)
-   `sort_by` (optional): Sort by field (default: created_at)
-   `sort_direction` (optional): Sort direction (asc/desc, default: desc)
-   `per_page` (optional): Items per page (default: 10)

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
    "amount": 100.0,
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
    "total_amount": 5000.0,
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

-   `search` (optional): Search in title and description
-   `category_id` (optional): Filter by category
-   `status` (optional): Filter by status (active/inactive)
-   `sort_by` (optional): Sort by field (default: created_at)
-   `sort_direction` (optional): Sort direction (asc/desc, default: desc)
-   `per_page` (optional): Items per page (default: 10)

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
