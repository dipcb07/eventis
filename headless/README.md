<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>API Documentation</title>
</head>
<body>
  <h1>API Documentation</h1>
  <p>All endpoints use the HTTP POST method and accept JSON-formatted request bodies.</p>
  
  <!-- 1. Test API -->
  <div class="endpoint">
    <h2>1. Test API</h2>
    <p><strong>Endpoint:</strong> <code>/test_api</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> A simple endpoint to verify that the API is running.</p>
    <p><strong>Request Body:</strong> None required.</p>
    <p class="example-title">Response Example:</p>
    <pre>{
  "status": "success",
  "message": "API test successful",
  "data": {}
}</pre>
  </div>
  
  <!-- 2. Create User -->
  <div class="endpoint">
    <h2>2. Create User</h2>
    <p><strong>Endpoint:</strong> <code>/user_create</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Creates a new user.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "name": "John Doe",
  "username": "johndoe",
  "password": "password123",
  "email": "john@example.com",
  "org": "Example Org"  /* Optional */
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "user created successfully",
  "data": {
    "user_id": "generated_unique_id"
  }
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 3. Update User -->
  <div class="endpoint">
    <h2>3. Update User</h2>
    <p><strong>Endpoint:</strong> <code>/user_update</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Updates an existing user’s details.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "unique_id": "user_unique_id",
  "name": "John Doe Updated",
  "email": "john.updated@example.com"
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "user updated successfully",
  "data": {}
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 4. Delete User -->
  <div class="endpoint">
    <h2>4. Delete User</h2>
    <p><strong>Endpoint:</strong> <code>/user_delete</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Deletes a user.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "unique_id": "user_unique_id"
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "user deleted successfully",
  "data": {}
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 5. User Login -->
  <div class="endpoint">
    <h2>5. User Login</h2>
    <p><strong>Endpoint:</strong> <code>/user_login</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Authenticates a user.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "username": "johndoe",
  "password": "password123"
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "user logged in successfully",
  "data": {
    "user_id": "user_unique_id",
    "session_id": "session_token"
  }
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 6. User Logout -->
  <div class="endpoint">
    <h2>6. User Logout</h2>
    <p><strong>Endpoint:</strong> <code>/user_logout</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Logs out the user.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "user_id": "user_unique_id",
  "session_id": "session_token"
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "user logged out successfully",
  "data": {}
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 7. Forgot Password -->
  <div class="endpoint">
    <h2>7. Forgot Password</h2>
    <p><strong>Endpoint:</strong> <code>/user_forgot_password</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Resets a user’s password.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "username": "johndoe",
  "password": "newpassword123"
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "password reset successful",
  "data": {}
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 8. Check Username -->
  <div class="endpoint">
    <h2>8. Check Username</h2>
    <p><strong>Endpoint:</strong> <code>/user_username_check</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Checks whether a username already exists.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "username": "johndoe"
}</pre>
    <p class="example-title">Response Examples:</p>
    <p><strong>Username exists:</strong></p>
    <pre>{
  "status": "success",
  "message": "username exist",
  "data": {
    "exist": true
  }
}</pre>
    <p><strong>Username not found:</strong></p>
    <pre>{
  "status": "failed",
  "message": "username not found",
  "data": {
    "exist": false
  }
}</pre>
    <p><strong>Error Response:</strong> 400: Missing required parameters</p>
  </div>
  
  <!-- 9. Check Email -->
  <div class="endpoint">
    <h2>9. Check Email</h2>
    <p><strong>Endpoint:</strong> <code>/user_email_check</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Checks whether an email already exists.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "email": "john@example.com"
}</pre>
    <p class="example-title">Response Examples:</p>
    <p><strong>Email exists:</strong></p>
    <pre>{
  "status": "success",
  "message": "email exist",
  "data": {
    "exist": true
  }
}</pre>
    <p><strong>Email not found:</strong></p>
    <pre>{
  "status": "failed",
  "message": "email not found",
  "data": {
    "exist": false
  }
}</pre>
    <p><strong>Error Response:</strong> 400: Missing required parameters</p>
  </div>
  
  <!-- 10. Check User Existence -->
  <div class="endpoint">
    <h2>10. Check User Existence</h2>
    <p><strong>Endpoint:</strong> <code>/user_exist_check</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Checks if a user exists (by username).</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "username": "johndoe"
}</pre>
    <p class="example-title">Response Examples:</p>
    <p><strong>User exists:</strong></p>
    <pre>{
  "status": "success",
  "message": "user exist",
  "data": {
    "exist": true
  }
}</pre>
    <p><strong>User not found:</strong></p>
    <pre>{
  "status": "failed",
  "message": "user not found",
  "data": {
    "exist": false
  }
}</pre>
    <p><strong>Error Response:</strong> 400: Missing required parameters</p>
  </div>
  
  <!-- 11. Create Event -->
  <div class="endpoint">
    <h2>11. Create Event</h2>
    <p><strong>Endpoint:</strong> <code>/event_create</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Creates a new event.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "user_id": "user_unique_id",
  "name": "Annual Meetup",
  "description": "Company annual meetup event",
  "start_date_time": "2025-06-01T10:00:00Z",
  "end_date_time": "2025-06-01T18:00:00Z",
  "max_capacity": 100
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "event created successfully",
  "data": {
    "event_id": "generated_unique_id"
  }
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 12. Update Event -->
  <div class="endpoint">
    <h2>12. Update Event</h2>
    <p><strong>Endpoint:</strong> <code>/event_update</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Updates event details.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "unique_id": "event_unique_id",
  "name": "Annual Meetup 2025",
  "max_capacity": 150
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "event updated successfully",
  "data": {}
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 13. Delete Event -->
  <div class="endpoint">
    <h2>13. Delete Event</h2>
    <p><strong>Endpoint:</strong> <code>/event_delete</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Deletes an event.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "event_id": "event_unique_id"
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "event deleted successfully",
  "data": {}
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 14. List Events -->
  <div class="endpoint">
    <h2>14. List Events</h2>
    <p><strong>Endpoint:</strong> <code>/event_list</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Lists events for a given user.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "user_id": "user_unique_id"
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "List of events",
  "data": [
    {
      "event_id": "event1",
      "name": "Event One"
    },
    {
      "event_id": "event2",
      "name": "Event Two"
    }
  ]
}</pre>
    <p><strong>Error Response:</strong> 400: Missing required parameters</p>
  </div>
  
  <!-- 15. Single Event Details -->
  <div class="endpoint">
    <h2>15. Single Event Details</h2>
    <p><strong>Endpoint:</strong> <code>/event_single_details</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Retrieves details for a specific event.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "event_id": "event_unique_id"
}</pre>
    <p><strong>Success Response:</strong> Returns event details in JSON format.</p>
    <p><strong>Error Response:</strong> 400: Missing required parameters</p>
  </div>
  
  <!-- 16. Change Event Status -->
  <div class="endpoint">
    <h2>16. Change Event Status</h2>
    <p><strong>Endpoint:</strong> <code>/event_status_change</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Changes the status of an event.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "event_id": "event_unique_id",
  "status": "disabled"  /* or other status values as applicable */
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "event disabled successfully",
  "data": {}
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 17. Register Attendee -->
  <div class="endpoint">
    <h2>17. Register Attendee</h2>
    <p><strong>Endpoint:</strong> <code>/attendee_register</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Registers an attendee for an event.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "event_id": "event_unique_id",
  "name": "Jane Doe",
  "email": "jane@example.com"
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "attendee registered successfully",
  "data": {
    "attendee_id": "generated_unique_id"
  }
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 18. Delete Attendee -->
  <div class="endpoint">
    <h2>18. Delete Attendee</h2>
    <p><strong>Endpoint:</strong> <code>/attendee_delete</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Deletes an attendee from an event.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "event_id": "event_unique_id",
  "unique_id": "attendee_unique_id"
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "attendee deleted successfully",
  "data": {}
}</pre>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 19. Change Attendee Status -->
  <div class="endpoint">
    <h2>19. Change Attendee Status</h2>
    <p><strong>Endpoint:</strong> <code>/attendee_status_change</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Updates the status of an attendee.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "event_id": "event_unique_id",
  "unique_id": "attendee_unique_id",
  "status": "confirmed"  /* or other applicable status */
}</pre>
    <p><strong>Note:</strong> In the provided PHP code, if no error occurs, the endpoint may fall through to listing attendees.</p>
    <p><strong>Error Responses:</strong></p>
    <ul>
      <li>400: Missing required parameters</li>
      <li>500: Internal server error</li>
    </ul>
  </div>
  
  <!-- 20. List Attendees -->
  <div class="endpoint">
    <h2>20. List Attendees</h2>
    <p><strong>Endpoint:</strong> <code>/attendee_list</code></p>
    <p><strong>Method:</strong> POST</p>
    <p><strong>Description:</strong> Lists all attendees for a given event.</p>
    <p class="example-title">Request Body Example:</p>
    <pre>{
  "event_id": "event_unique_id"
}</pre>
    <p class="example-title">Success Response Example:</p>
    <pre>{
  "status": "success",
  "message": "List of attendees",
  "data": [
    {
      "attendee_id": "attendee1",
      "name": "Attendee One",
      "email": "attendee1@example.com"
    },
    {
      "attendee_id": "attendee2",
      "name": "Attendee Two",
      "email": "attendee2@example.com"
    }
  ]
}</pre>
    <p><strong>Error Response:</strong> 400: Missing required parameters</p>
  </div>

</body>
</html>
