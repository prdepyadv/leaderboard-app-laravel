# API Documentation

This API allows management of users and their scores in a leaderboard system. It provides functionalities to add, delete, update users' scores, and fetch various reports.

## Base URL

All endpoints are prefixed with `/api`.

## Endpoints

### 1. Get All Users

- **Endpoint:** `GET /api/users`
- **Description:** Fetch all users ordered by their score (default order is descending).
- **Query Parameters:**
  - `sortby` (optional): Field to sort by (default is points).
  - `order` (optional): Sorting order (asc or desc).
- **Response:**
  - 200 OK: List of users and their details (name, age, points, address).

### 2. Get User by ID

- **Endpoint:** `GET /api/user/{id}`
- **Description:** Fetch details of a specific user by their ID.
- **Response:**
  - 200 OK: User details.
  - 404 Not Found: If user is not found.

### 3. Add a New User

- **Endpoint:** `POST /api/users`
- **Description:** Add a new user to the leaderboard with 0 points.
- **Request Body:**
  - `name` (string, required): Name of the user.
  - `age` (integer, required): Age of the user (between 1 and 60).
  - `address` (string, required): Address of the user.
- **Response:**
  - 201 Created: User added successfully.
  - 422 Unprocessable Entity: Validation error.

### 4. Increment User Points

- **Endpoint:** `PUT /api/user/{id}/increment`
- **Description:** Increment the points of a specific user by 1.
- **Response:**
  - 200 OK: User points incremented.
  - 404 Not Found: If user is not found.

### 5. Decrement User Points

- **Endpoint:** `PUT /api/user/{id}/decrement`
- **Description:** Decrement the points of a specific user by 1, if the points are greater than 0.
- **Response:**
  - 200 OK: User points decremented.
  - 404 Not Found: If user is not found.

### 6. Delete a User

- **Endpoint:** `DELETE /api/user/{id}`
- **Description:** Delete a user from the leaderboard by their ID.
- **Response:**
  - 200 OK: User deleted successfully.
  - 404 Not Found: If user is not found.

### 7. Reset All User Scores

- **Endpoint:** `POST /api/users/scores/reset`
- **Description:** Reset all user scores to 0.
- **Response:**
  - 200 OK: All user scores reset to 0.

### 8. Get Score Report

- **Endpoint:** `GET /api/users/scores/report`
- **Description:** Retrieve a report of users grouped by score, along with the average age for each score group.
- **Response:**
  - 200 OK: JSON object with score groups, corresponding user names, and average age.

Example response:

```json
{
  "25": {
    "names": ["Emma"],
    "average_age": 18
  },
  "18": {
    "names": ["Noah"],
    "average_age": 17
  }
}
```

## Error Responses

- **400 Bad Request:** Invalid request or query parameters.
- **404 Not Found:** Resource not found.
- **422 Unprocessable Entity:** Validation errors (e.g., adding a user with missing or invalid data).
- **500 Internal Server Error:** Unexpected server errors.
