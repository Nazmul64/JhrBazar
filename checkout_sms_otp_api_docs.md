# Checkout SMS OTP API Documentation

এই ডকুমেন্টেশনে অর্ডার করার সময় মোবাইল নম্বরের ওটিপি (OTP) ভেরিফিকেশন এবং এর জন্য তৈরি করা এপিআই (API)-গুলোর বিস্তারিত আলোচনা করা হয়েছে।

## Common Request Headers
```http
Accept: application/json
Content-Type: application/json
```

---

## API Endpoints Summary

| Method | Endpoint | Description | Auth Required |
|:---|:---|:---|:---|
| **GET** | `/api/checkout/otp-settings` | Check if OTP is required for checkout & get phone length settings | No |
| **POST** | `/api/checkout/send-otp` | Send/Resend OTP Code to customer's phone | No |
| **POST** | `/api/checkout/verify-otp` | Verify the OTP Code and set temporary verification status | No |
| **POST** | `/api/place-order` | Place order (supports both direct OTP and pre-verified phone) | Optional |

---

## Detailed Endpoints Specification

### 1. Get Checkout OTP Settings
অর্ডার প্লেসমেন্ট পেজে ঢোকার পর, ওটিপি ভেরিফিকেশন প্যানেল দেখানোর প্রয়োজন আছে কি না তা জানতে এই API কল করতে হবে।

- **URL:** `/api/checkout/otp-settings`
- **Method:** `GET`

#### Responses:
- **Success (200 OK):**
  ```json
  {
      "success": true,
      "otp_required": true,
      "min_phone_length": 11,
      "max_phone_length": 11,
      "sms_gateway_active": true
  }
  ```
  *(যদি `otp_required` এর মান `true` আসে, তবে গ্রাহকের ফোন নম্বরটি অবশ্যই ভেরিফাই করাতে হবে)*

---

### 2. Send/Resend Checkout OTP
অর্ডার প্রসেস সম্পন্ন করার জন্য অথবা ওটিপি রিসেন্ড (Resend OTP) বাটনে ক্লিক করা হলে এই API কল করুন।

- **URL:** `/api/checkout/send-otp`
- **Method:** `POST`
- **Body Params:**
  ```json
  {
      "phone": "017XXXXXXXX"
  }
  ```

#### Responses:
- **Success (200 OK):**
  ```json
  {
      "success": true,
      "message": "আপনার মোবাইলে ওটিপি (OTP) কোড পাঠানো হয়েছে।"
  }
  ```
- **Error (400 Bad Request):**
  ```json
  {
      "success": false,
      "message": "এসএমএস গেটওয়ে কনফিগার করা নেই বা নিষ্ক্রিয় রয়েছে।"
  }
  ```
- **Error (500 Server Error):**
  ```json
  {
      "success": false,
      "message": "ওটিপি পাঠাতে ব্যর্থ হয়েছে। অনুগ্রহ করে মোবাইল নম্বরটি পরীক্ষা করুন।"
  }
  ```

---

### 3. Verify Checkout OTP
গ্রাহক ওটিপি কোডটি দেওয়ার পর ভেরিফাই করার জন্য এই API ব্যবহার করুন। ওটিপি সফলভাবে ভেরিফাইড হলে সার্ভারের ক্যাশে একটি টেম্পোরারি ভেরিফিকেশন ফ্ল্যাগ সেট হবে (যার মেয়াদ ১০ মিনিট)।

- **URL:** `/api/checkout/verify-otp`
- **Method:** `POST`
- **Body Params:**
  ```json
  {
      "phone": "017XXXXXXXX",
      "otp_code": "123456"
  }
  ```

#### Responses:
- **Success (200 OK):**
  ```json
  {
      "success": true,
      "message": "ওটিপি (OTP) সফলভাবে যাচাই করা হয়েছে।"
  }
  ```
- **Validation Error (422 Unprocessable Entity):**
  ```json
  {
      "success": false,
      "message": "অবৈধ বা মেয়াদোত্তীর্ণ ওটিপি (OTP) কোড।"
  }
  ```

---

### 4. Place Order Interaction with OTP
চেকআউট ওটিপি ভেরিফিকেশনের জন্য ডেভেলপার চাইলে দুটি ফ্লো ব্যবহার করতে পারেন:

#### ফ্লো ১ (Two-Step Flow - Recommended):
1. অর্ডার প্লেস করার পূর্বে গ্রাহকের নম্বরটিতে `/api/checkout/send-otp` এ রিকোয়েস্ট পাঠান।
2. কোড ইনপুট নেওয়ার পর `/api/checkout/verify-otp` এ ভেরিফাই করুন।
3. ভেরিফিকেশন সফল হলে কোনো অতিরিক্ত ওটিপি প্যারামিটার ছাড়াই সরাসরি `/api/place-order` এ অর্ডারের মেইন পেলোডটি সাবমিট করুন। সার্ভারটি ইতিমধ্যেই মনে রাখবে যে নম্বরটি ভেরিফাইড।

#### ফ্লো ২ (One-Step Flow):
1. সরাসরি `/api/place-order` এ রিকোয়েস্ট পাঠান।
2. যদি ওটিপি দরকার হয়, সার্ভার নিচের রেসপন্সটি দিবে:
   ```json
   {
       "success": false,
       "otp_required": true,
       "message": "আপনার মোবাইলে ওটিপি (OTP) কোড পাঠানো হয়েছে। অর্ডার সম্পন্ন করতে কোডটি দিন।"
   }
   ```
3. ওটিপি কোডটি গ্রাহক থেকে ইনপুট নিয়ে মেইন পেলোডের সাথে `"otp_code": "123456"` প্যারামিটার যুক্ত করে পুনরায় `/api/place-order` এ রিকোয়েস্ট পাঠান। অর্ডার কনফার্ম হবে।
