# Firebase Push Notification API Documentation

এই ডকুমেন্টেশনে ফায়ারবেস পুশ নোটিফিকেশন সিস্টেমের কাস্টমার/মোবাইল অ্যাপ ইন্টিগ্রেশনের জন্য তৈরি করা API গুলোর বিস্তারিত আলোচনা করা হয়েছে। সব এপিআই-ই অথেনটিকেটেড (Sanctum) এবং এদের রিকোয়েস্টে নিচের হেডারগুলো পাঠানো আবশ্যক:

## Common Request Headers
```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer <your_sanctum_token>
```

---

## API Endpoints Summary

| Method | Endpoint | Description | Auth Required |
|:---|:---|:---|:---|
| **POST** | `/api/user/save-fcm-token` | Save/Update Customer Device FCM Token | Yes (Sanctum) |
| **GET** | `/api/notifications` | Get Customer's Notification History (Paginated) | Yes (Sanctum) |
| **POST** | `/api/notifications/{id}/read` | Mark a specific notification as read | Yes (Sanctum) |
| **POST** | `/api/notifications/read-all` | Mark all notifications as read | Yes (Sanctum) |
| **DELETE** | `/api/notifications/{id}` | Delete a specific notification | Yes (Sanctum) |
| **DELETE** | `/api/notifications/clear-all` | Delete all notifications (Inbox clear) | Yes (Sanctum) |

---

## Detailed Endpoints Specification

### 1. Save/Update FCM Token
কাস্টমার যখন মোবাইল অ্যাপে লগইন করবে বা অ্যাপ ওপেন করবে, তখন তার ডিভাইসের ফায়ারবেস টোকেন (FCM Token) সার্ভারে সংরক্ষণ করতে এই API ব্যবহার করুন।

- **URL:** `/api/user/save-fcm-token`
- **Method:** `POST`
- **Body Params:**
  ```json
  {
      "fcm_token": "your_device_firebase_fcm_token_string_here"
  }
  ```

#### Responses:
- **Success (200 OK):**
  ```json
  {
      "success": true,
      "message": "FCM Token registered successfully."
  }
  ```
- **Validation Error (422 Unprocessable Entity):**
  ```json
  {
      "success": false,
      "message": "Validation error.",
      "errors": {
          "fcm_token": [
              "The fcm_token field is required."
          ]
      }
  }
  ```
- **Auth Error (401 Unauthenticated):**
  ```json
  {
      "message": "Unauthenticated."
  }
  ```

---

### 2. Get Notifications List
কাস্টমারের নোটিফিকেশন ইনবক্স দেখানোর জন্য এই API ব্যবহার করুন। এটি পেজিনেটেড ডেটা রিটার্ন করে এবং অপঠিত (Unread) নোটিফিকেশনের সংখ্যাও (`unread_count`) প্রদান করে।

- **URL:** `/api/notifications`
- **Method:** `GET`
- **Query Params (Optional):**
  - `page`: Page number (Default: 1)
  - `per_page`: Items per page (Default: 15)

#### Responses:
- **Success (200 OK):**
  ```json
  {
      "success": true,
      "message": "Notifications retrieved successfully.",
      "unread_count": 2,
      "data": {
          "current_page": 1,
          "data": [
              {
                  "id": 5,
                  "title": "ধামাকা অফার! 🔥",
                  "body": "আজকের সব অর্ডারে পাবেন ২০% ডিসকাউন্ট। এখনই অর্ডার করুন!",
                  "image_url": "https://yourdomain.com/uploads/promo.jpg",
                  "read_at": null,
                  "created_at": "2026-06-11T17:25:00.000000Z"
              },
              {
                  "id": 4,
                  "title": "অর্ডার আপডেট",
                  "body": "আপনার অর্ডার নং #4582 সফলভাবে ডেলিভারি করা হয়েছে।",
                  "image_url": null,
                  "read_at": "2026-06-11T17:20:00.000000Z",
                  "created_at": "2026-06-11T17:00:00.000000Z"
              }
          ],
          "first_page_url": "http://yourdomain.com/api/notifications?page=1",
          "from": 1,
          "last_page": 1,
          "last_page_url": "http://yourdomain.com/api/notifications?page=1",
          "next_page_url": null,
          "path": "http://yourdomain.com/api/notifications",
          "per_page": 15,
          "prev_page_url": null,
          "to": 2,
          "total": 2
      }
  }
  ```

---

### 3. Mark Single Notification as Read
কাস্টমার যখন নির্দিষ্ট কোনো নোটিফিকেশনে ক্লিক করবে, তখন সেটিকে Read (পঠিত) হিসেবে মার্ক করতে এই API কল করুন।

- **URL:** `/api/notifications/{id}/read`  *(এখানে `{id}` হলো নোটিফিকেশনের ID)*
- **Method:** `POST`

#### Responses:
- **Success (200 OK):**
  ```json
  {
      "success": true,
      "message": "Notification marked as read.",
      "data": {
          "id": 5,
          "title": "ধামাকা অফার! 🔥",
          "body": "আজকের সব অর্ডারে পাবেন ২০% ডিসকাউন্ট। এখনই অর্ডার করুন!",
          "image_url": "https://yourdomain.com/uploads/promo.jpg",
          "read_at": "2026-06-11T17:26:00.000000Z",
          "created_at": "2026-06-11T17:25:00.000000Z"
      }
  }
  ```
- **Not Found (404 Not Found):**
  ```json
  {
      "success": false,
      "message": "Notification not found."
  }
  ```

---

### 4. Mark All Notifications as Read
নোটিফিকেশন স্ক্রিনে থাকা অবস্থায় সব নোটিফিকেশনকে একসাথে Read হিসেবে মার্ক করতে এই API ব্যবহার করুন।

- **URL:** `/api/notifications/read-all`
- **Method:** `POST`

#### Responses:
- **Success (200 OK):**
  ```json
  {
      "success": true,
      "message": "All notifications marked as read successfully."
  }
  ```

---

### 5. Delete Single Notification
নোটিফিকেশন লিস্ট থেকে কোনো নির্দিষ্ট নোটিফিকেশন ডিলিট বা রিমুভ করার জন্য এই API ব্যবহার করুন।

- **URL:** `/api/notifications/{id}` *(এখানে `{id}` হলো নোটিফিকেশনের ID)*
- **Method:** `DELETE`

#### Responses:
- **Success (200 OK):**
  ```json
  {
      "success": true,
      "message": "Notification deleted successfully."
  }
  ```
- **Not Found (404 Not Found):**
  ```json
  {
      "success": false,
      "message": "Notification not found."
  }
  ```

---

### 6. Clear All Notifications (Inbox Clear)
কাস্টমার যদি তার নোটিফিকেশন ইনবক্সের সমস্ত নোটিফিকেশন একসাথে ডিলিট করে দিতে চান, তবে এই API ব্যবহার করুন।

- **URL:** `/api/notifications/clear-all`
- **Method:** `DELETE`

#### Responses:
- **Success (200 OK):**
  ```json
  {
      "success": true,
      "message": "All notifications cleared successfully."
  }
  ```
