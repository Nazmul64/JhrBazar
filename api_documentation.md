# JhrBazar API Documentation (Flutter & Mobile Optimized)

Welcome to the **JhrBazar API Documentation**. This comprehensive reference guide is designed for frontend and **Flutter Mobile Application** developers integrating pages like Home, Categories, Sliders, Product Details, Wishlist, Cart, Checkout, Chat, and Customer Accounts.

---

## Table of Contents
1. [Base URL & Standards](#base-url--standards)
2. [Flutter/Dart Integration Blueprint](#flutterdart-integration-blueprint)
3. [Authentication API](#1-authentication-api)
4. [Customer Dashboard & Account API](#2-customer-dashboard--account-api)
5. [Category & Subcategory API](#3-category--subcategory-api)
6. [Banners & Sliders API](#4-banners--sliders-api)
7. [Product & Search API](#5-product--search-api)
8. [Wishlist API](#6-wishlist-api)
9. [Checkout & Orders API](#7-checkout--orders-api)
10. [Chat & Support API](#8-chat--support-api)
11. [Reviews API](#9-reviews-api)
12. [Other Content & Supporting APIs](#10-other-content--supporting-apis)
13. [API Version 1 (JWT) Routes](#11-api-version-1-jwt-routes)

---

## Base URL & Standards

### Base URL
- **Production Base URL**: `https://your-domain.com/api` (or `https://your-domain.com` if not using /api prefix reverse proxy)
- **Development/Local URL**: `http://127.0.0.1:8000` (Note: `bootstrap/app.php` sets `apiPrefix: ''`, so API endpoints are directly on the root. Example: `http://127.0.0.1:8000/settings` or `http://127.0.0.1:8000/v1/auth/login`)

### Dynamic Image URL Mapping
All API endpoints returning image paths (logo, banners, categories, products, wishlist, etc.) automatically rewrite their URLs to be absolute URLs based on the current request's scheme and host. For example:
- If requested via `http://10.0.2.2:8000/api/home-data` (Android Emulator), the image URLs in the response will point to `http://10.0.2.2:8000/uploads/...`.
- If requested via `http://127.0.0.1:8000/api/home-data` (Local browser), the image URLs in the response will point to `http://127.0.0.1:8000/uploads/...`.
This works dynamically even for cached responses, ensuring images always load correctly on any client or emulator.

### Headers
Every request must include the following headers:
```http
Accept: application/json
Content-Type: application/json
```

For authenticated endpoints, pass the bearer token:
```http
Authorization: Bearer <your_access_token>
```

For guest/session-based APIs (e.g., guest wishlist & live chat tracking), include:
```http
X-Session-Id: <unique-guest-uuid>
```
> **Tip**: Generate a persistent UUID on the Flutter client side and store it locally using `shared_preferences` to identify guest sessions.

---

## Flutter/Dart Integration Blueprint

Here is a template network client configuration in Flutter using the **Dio** library:

```dart
import 'package:dio/dio.dart';
import 'package:uuid/uuid.dart';

class ApiClient {
  final Dio _dio = Dio(BaseOptions(
    baseUrl: 'https://your-domain.com/api',
    connectTimeout: const Duration(seconds: 15),
    receiveTimeout: const Duration(seconds: 15),
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  ));

  ApiClient() {
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        // Load stored token
        String? token = await getStoredToken();
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        
        // Load or generate guest X-Session-Id
        String sessionId = await getOrGenerateSessionId();
        options.headers['X-Session-Id'] = sessionId;
        
        return handler.next(options);
      },
    ));
  }

  Dio get dio => _dio;
  
  // Implement token storage/retrieval & persistent UUID storage...
}
```

---

## 1. Authentication API

### Register Customer
Create a new customer account. When registered successfully, an API access token is returned.
* **Endpoint**: `POST /register`
* **Authorization**: None (Public)
* **Request Body**:
  ```json
  {
    "name": "Nazmul Hossain",
    "email": "nazmul@example.com",
    "phone": "01700000000",
    "password": "securepassword123",
    "password_confirmation": "securepassword123"
  }
  ```
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "access_token": "1|LaravelSanctumToken...",
    "token_type": "Bearer",
    "user": {
      "id": 12,
      "name": "Nazmul Hossain",
      "email": "nazmul@example.com",
      "phone": "01700000000",
      "role": "customer",
      "status": 1,
      "created_at": "2026-06-04T09:05:32.000000Z",
      "updated_at": "2026-06-04T09:05:32.000000Z"
    }
  }
  ```
* **Response (Validation Error - 422 Unprocessable Content)**:
  ```json
  {
    "success": false,
    "message": "The email has already been taken.",
    "errors": {
      "email": ["The email has already been taken."]
    }
  }
  ```

### Login Customer
Authenticates users using their registered **email address** OR **phone number**.
* **Endpoint**: `POST /login`
* **Authorization**: None (Public)
* **Request Body**:
  ```json
  {
    "email": "01700000000", 
    "password": "securepassword123"
  }
  ```
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "access_token": "2|LaravelSanctumToken...",
    "token_type": "Bearer",
    "user": {
      "id": 12,
      "name": "Nazmul Hossain",
      "email": "nazmul@example.com",
      "phone": "01700000000",
      "role": "customer",
      "status": 1
    }
  }
  ```
* **Response (Invalid Credentials - 401 Unauthorized)**:
  ```json
  {
    "success": false,
    "message": "Invalid credentials"
  }
  ```

### Logout Customer
Revokes the current active access token.
* **Endpoint**: `POST /logout`
* **Authorization**: Authenticated (`Bearer Sanctum Token`)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "message": "Successfully logged out"
  }
  ```

### Get Authenticated User
* **Endpoint**: `GET /user`
* **Authorization**: Authenticated (`Bearer Sanctum Token`)
* **Response (Success - 200 OK)**: Returns the current user model object.

---

## 2. Customer Dashboard & Account API

### Get Customer Dashboard Stats
Returns count of past orders and items in the wishlist for the authenticated customer.
* **Endpoint**: `GET /customer/dashboard`
* **Authorization**: Authenticated (`Bearer Sanctum Token`)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "order_count": 5,
      "wishlist_count": 3
    }
  }
  ```

### Get Customer Past Orders
Retrieve the order history for the authenticated customer.
* **Endpoint**: `GET /customer/orders`
* **Authorization**: Authenticated (`Bearer Sanctum Token`)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 87,
        "seller_id": null,
        "customer_id": 12,
        "sub_total": "2000.00",
        "discount": "200.00",
        "delivery_charge": "60.00",
        "grand_total": "1860.00",
        "payment_method": "cod",
        "payment_status": "unpaid",
        "status": "pending",
        "phone": "01700000000",
        "created_at": "2026-06-04T15:30:00.000000Z",
        "invoice": {
          "id": 92,
          "invoice_number": "26060401",
          "grand_total": "1860.00",
          "payment_method": "cod",
          "created_at": "2026-06-04T15:30:00.000000Z"
        }
      }
    ]
  }
  ```

### Update Profile Info
Updates customer contact information, delivery address, and profile image.
* **Endpoint**: `POST /customer/update-profile`
* **Content-Type**: `multipart/form-data`
* **Authorization**: Authenticated (`Bearer Sanctum Token`)
* **Request Parameters**:
  - `name` (Required - string)
  - `email` (Required - string, email)
  - `phone` (Optional - string)
  - `address` (Optional - string)
  - `profile_image` (Optional - file/image: jpeg, png, jpg, gif, webp, svg; Max 2MB)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "message": "প্রোফাইল সফলভাবে আপডেট করা হয়েছে।",
    "user": {
      "id": 12,
      "name": "Nazmul Hossain Updated",
      "email": "nazmul@example.com",
      "phone": "01700000000",
      "address": "Dhaka, Bangladesh",
      "profile_image": "1717532345_12.png",
      "role": "customer"
    }
  }
  ```

### Change Password
* **Endpoint**: `POST /customer/update-password`
* **Authorization**: Authenticated (`Bearer Sanctum Token`)
* **Request Body**:
  ```json
  {
    "current_password": "securepassword123",
    "password": "newpassword456",
    "password_confirmation": "newpassword456"
  }
  ```
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "message": "পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে।"
  }
  ```

---

## 3. Category & Subcategory API

### Get Categories List
Retrieve all categories with their active child subcategories.
* **Endpoint**: `GET /categories`
* **Authorization**: None (Public)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "name": "Electronics",
        "slug": "electronics",
        "thumbnail": "https://your-domain.com/uploads/category/electronics.png?v=1.0.4",
        "sub_categories": [
          {
            "id": 5,
            "category_id": 1,
            "name": "Smartphones",
            "slug": "smartphones",
            "thumbnail": "https://your-domain.com/uploads/subcategory/smartphones.png?v=1.0.4"
          }
        ]
      }
    ]
  }
  ```

### Get Category Hierarchy
* **Endpoint**: `GET /categories-with-sub`
* **Authorization**: None (Public)
* **Response**: Same format as `GET /categories`.

### Get Category Name by ID
* **Endpoint**: `GET /category/{id}/name`
* **Response**:
  ```json
  {
    "success": true,
    "name": "Electronics"
  }
  ```

### Get Subcategory Name by ID
* **Endpoint**: `GET /subcategory/{id}/name`
* **Response**:
  ```json
  {
    "success": true,
    "name": "Smartphones"
  }
  ```

---

## 4. Banners & Sliders API

### Get Active Sliders
Get all promotional sliders/banners for the Home page.
* **Endpoint**: `GET /banners`
* **Authorization**: None (Public)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "image": "https://your-domain.com/uploads/banners/slider1.png?v=1.0.4",
        "for_own_shop": true
      }
    ]
  }
  ```

---

## 5. Product & Search API

### Get Consolidated Home Data
Highly optimized endpoint delivering all components for the Home page in a single request. Excellent for initial mobile loading.
* **Endpoint**: `GET /home-data`
* **Authorization**: None (Public)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "settings": {
        "id": 1,
        "shop_name": "JHR Bazar",
        "logo": "https://your-domain.com/uploads/logo.png",
        "footer_logo": "https://your-domain.com/uploads/logo-footer.png",
        "favicon": "https://your-domain.com/uploads/favicon.png",
        "top_rated_shops_status": 1
      },
      "banners": [ ... ],
      "categories": [ ... ],
      "popularProducts": [
        {
          "id": 4,
          "name": "Premium Leather Wallet",
          "slug": "premium-leather-wallet",
          "thumbnail": "https://your-domain.com/uploads/product/wallet.png?v=1.0.4",
          "price": 1200.0,
          "discount_price": 1000.0,
          "cash_on_delivery": true,
          "online_payment": true,
          "stock": 45,
          "avg_rating": 4.8,
          "review_count": 5
        }
      ],
      "newArrivals": [ ... ],
      "justForYouProducts": [ ... ],
      "digitalProducts": [ ... ],
      "bestDeals": [ ... ],
      "topShops": [
        {
          "id": 2,
          "seller_id": 5,
          "name": "Apex Fashion",
          "logo": "https://your-domain.com/uploads/shops/logo.png",
          "banner": "https://your-domain.com/uploads/shops/banner.png",
          "item_count": 24,
          "rating": "5.0",
          "description": "Premium footwear and accessories"
        }
      ],
      "allProducts": [ ... ],
      "recentReviews": [ ... ],
      "frontendSections": [
        {
          "title": "Deal of the Day",
          "products": [ ... ]
        }
      ]
    }
  }
  ```

### Get All Products
Combined active products list from Administrator and Third-party Sellers.
* **Endpoint**: `GET /all-products`
* **Query Parameters**:
  - `limit` (Optional - integer or `all`, defaults to `10`)
* **Response**: List of product models.

### Get Popular Products
* **Endpoint**: `GET /popular-products`
* **Query Parameters**: `limit` (Optional)

### Get New Arrivals
* **Endpoint**: `GET /new-arrivals`
* **Query Parameters**: `limit` (Optional)

### Get Just For You
* **Endpoint**: `GET /just-for-you`
* **Query Parameters**: `limit` (Optional)

### Get Best Deals (Discounted Products)
* **Endpoint**: `GET /best-deals`
* **Query Parameters**: `limit` (Optional)

### Get Products by Category ID
Fetches products under a specific category, including all of its subcategories.
* **Endpoint**: `GET /products/category/{id}`
* **Authorization**: None (Public)
* **Response**: List of products.

### Get Products by Subcategory ID
* **Endpoint**: `GET /products/subcategory/{id}`
* **Authorization**: None (Public)

### Get Product Details by Slug (or ID)
Retrieves complete details for a product, including active reviews, customer rating, gallery images, and related products in the same category.
* **Endpoint**: `GET /product/{slug}`
* **Authorization**: None (Public)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "id": 4,
      "slug": "premium-leather-wallet",
      "uid": "admin_4",
      "product_type": "admin",
      "seller_id": null,
      "seller_name": "JHR Bazar",
      "seller_logo": null,
      "seller_rating": 5.0,
      "estimated_delivery": "2-5 days",
      "name": "Premium Leather Wallet",
      "short_description": "Handcrafted pure leather wallet",
      "description": "<p>Detailed HTML description here...</p>",
      "price": 1200.0,
      "discount_price": 1000.0,
      "old_price": 1200.0,
      "discount": 17,
      "stock": 45,
      "sku": "WL-004",
      "thumbnail": "https://your-domain.com/uploads/product/wallet.png?v=1.0.4",
      "gallery": [
        "https://your-domain.com/uploads/product/wallet_1.png",
        "https://your-domain.com/uploads/product/wallet_2.png"
      ],
      "category": "Fashion",
      "category_id": 3,
      "brand": "Apex",
      "color": ["black", "brown"],
      "size": ["standard"],
      "unit": "pcs",
      "video": null,
      "video_type": null,
      "cash_on_delivery": true,
      "online_payment": true,
      "is_shipping_charge": true,
      "avg_rating": 4.8,
      "review_count": 5,
      "reviews": [
        {
          "id": 1,
          "product_id": 4,
          "product_type": "admin",
          "rating": 5,
          "comment": "Outstanding quality!",
          "user": {
            "id": 3,
            "name": "Karim Rahman",
            "profile_image": "avatar.png"
          }
        }
      ],
      "related": [ ... ]
    }
  }
  ```

### Search Products
Fuzzy search products by name or price.
* **Endpoint**: `GET /products/search`
* **Query Parameters**:
  - `q` (Required - string, minimum 2 characters)
* **Response**: List of matching products.

---

## 6. Wishlist API

Supports both **authenticated customers** (linked to user account) and **guest visitors** (linked using the `X-Session-Id` header).

### Get Wishlist Items
* **Endpoint**: `GET /wishlist`
* **Headers**: `X-Session-Id` (Required if Guest/Unauthenticated)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "wishlist_id": 8,
        "id": 4,
        "uid": "admin_4",
        "title": "Premium Leather Wallet",
        "image": "https://your-domain.com/uploads/product/wallet.png",
        "price": 1000.0,
        "oldPrice": 1200.0,
        "product_type": "admin",
        "stock": 45,
        "brand": "Apex",
        "size": ["standard"],
        "color": ["black", "brown"],
        "unit": "pcs",
        "slug": "premium-leather-wallet"
      }
    ]
  }
  ```

### Toggle Product in Wishlist
Toggles (adds if absent, removes if present) a product in the wishlist.
* **Endpoint**: `POST /wishlist/toggle`
* **Headers**: `X-Session-Id` (Required if Guest/Unauthenticated)
* **Request Body**:
  ```json
  {
    "product_id": 4,
    "product_type": "admin"
  }
  ```
  > **Note**: `product_type` must be one of: `admin`, `seller`, or `digital`.
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "action": "added", // or "removed"
    "message": "Product added to wishlist"
  }
  ```

### Sync Guest Wishlist (On Login)
Call this immediately after a successful customer login to sync and transfer their local browser/device guest wishlist items to their permanent database user profile.
* **Endpoint**: `POST /wishlist/sync`
* **Authorization**: Authenticated (`Bearer Sanctum Token`)
* **Headers**:
  - `X-Session-Id` (Required - string): The guest session UUID containing the items.
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true
  }
  ```

---

## 7. Checkout & Orders API

### Get Shipping Zones & Charges
* **Endpoint**: `GET /shipping-charges`
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "shipping_location": "Inside Dhaka",
        "charge": "60.00",
        "status": 1
      },
      {
        "id": 2,
        "shipping_location": "Outside Dhaka",
        "charge": "120.00",
        "status": 1
      }
    ]
  }
  ```

### Get Active Payment Gateways
Retrieves configured gateway providers in the backend along with their official assets/logos.
* **Endpoint**: `GET /payment-gateways`
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "name": "bKash",
        "key": "bkash",
        "title": "bKash Checkout",
        "logo": "https://upload.wikimedia.org/wikipedia/commons/thumb/8/88/BKash_Logo.svg/512px-BKash_Logo.svg.png"
      },
      {
        "name": "SSLCommerz",
        "key": "sslcommerz",
        "title": "Online Card/Mobile Banking",
        "logo": "https://securepay.sslcommerz.com/gw/asset/img/sslcommerz-logo.png"
      }
    ]
  }
  ```

### Apply Coupon Code
* **Endpoint**: `POST /apply-coupon`
* **Request Body**:
  ```json
  {
    "coupon_code": "EAD2026",
    "subtotal": 1500.0
  }
  ```
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "message": "Coupon applied successfully!",
    "discount": 150.0,
    "code": "EAD2026"
  }
  ```
* **Response (Invalid/Expired)**:
  ```json
  {
    "success": false,
    "message": "Invalid or expired coupon code."
  }
  ```

### Place Order
Places an order. Supports both COD and Online Gateways. If an online payment method is chosen, the backend generates and returns a `payment_url` which the mobile app must open in a WebView to complete validation.
* **Endpoint**: `POST /place-order`
* **Authorization**: Optional (`Bearer Sanctum Token` or guest)
* **Request Body**:
  ```json
  {
    "name": "Nazmul Hossain",
    "phone": "01700000000",
    "address": "House 45, Road 2",
    "city": "Dhaka",
    "shipping_id": 1,
    "payment_method": "online", // "cod" or "online"
    "online_gateway": "sslcommerz", // Required if payment_method is online
    "coupon_code": "EAD2026",
    "device_fingerprint": "a90dfg8a9a...", 
    "browser": "Mobile App Client",
    "os": "Android",
    "device_type": "Mobile",
    "items": [
      {
        "id": 4,
        "qty": 2,
        "product_type": "admin", // "admin" or "seller"
        "uid": "admin_4",
        "color": "black",
        "size": "standard"
      }
    ]
  }
  ```
* **Response (Success - Cash on Delivery)**:
  ```json
  {
    "success": true,
    "message": "Order placed successfully!",
    "orders": [
      {
        "id": 87,
        "invoice_number": "26060401", // Unique 8-digit invoice number
        "sub_total": 2000.0,
        "discount": 200.0,
        "delivery_charge": 60.0,
        "grand_total": 1860.0,
        "payment_method": "cod",
        "status": "pending"
      }
    ],
    "payment_url": null
  }
  ```
* **Response (Success - Online Gateway selected)**:
  ```json
  {
    "success": true,
    "message": "Order placed successfully!",
    "orders": [ ... ],
    "payment_url": "https://sandbox.sslcommerz.com/gwprocess/v4/api.php?..." // WebView Target URL
  }
  ```

### Track Order by Invoice Number
* **Endpoint**: `GET /track-order/{invoice_no}`
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": {
      "invoice_number": "26060401",
      "status": "pending", // pending, processing, shipped, delivered, cancelled
      "created_at": "04 Jun 2026, 03:05 PM",
      "grand_total": "1860.00",
      "payment_method": "cod"
    }
  }
  ```

---

## 8. Chat & Support API

Supports real-time customer service chat between visitors/customers and the shop administrators.

### Send Message
Sends a support message. Supports text messages and image attachments.
* **Endpoint**: `POST /chat/send`
* **Content-Type**: `multipart/form-data`
* **Headers**: `X-Session-Id` (Required - string UUID to associate the chat session)
* **Request Body**:
  - `session_id` (Required - string UUID matching header)
  - `receiver_id` (Optional - integer; defaults to `null` to message the Shop Administrator)
  - `message` (Optional if image present - string)
  - `image` (Optional - file/image: jpeg, png, jpg, gif, webp; Max 5MB)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "message": "Message sent",
    "data": {
      "id": 43,
      "chat_session_id": 8,
      "sender_type": "user",
      "message": "Hello, is this premium wallet in stock?",
      "image": null,
      "created_at": "2026-06-04T09:05:32.000000Z"
    }
  }
  ```

### Get Chat Messages
Retrieve full chat message history for the active session.
* **Endpoint**: `GET /chat/messages`
* **Query Parameters**:
  - `session_id` (Required - string UUID)
  - `receiver_id` (Optional - integer)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 42,
        "chat_session_id": 8,
        "sender_type": "user",
        "message": "Hello",
        "image": null,
        "created_at": "2026-06-04T09:05:00.000000Z"
      },
      {
        "id": 43,
        "chat_session_id": 8,
        "sender_type": "admin",
        "message": "Hello! How can we help you today?",
        "image": null,
        "created_at": "2026-06-04T09:05:15.000000Z"
      }
    ]
  }
  ```

### Get Unread Chat Count
Checks if there are new unread replies from support admins.
* **Endpoint**: `GET /chat/unread-count`
* **Query Parameters**:
  - `session_id` (Required - string UUID)
  - `receiver_id` (Optional - integer)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "count": 0
  }
  ```

---

## 9. Reviews API

### Submit a Product Review
Submit a star rating and comment for a product.
* **Endpoint**: `POST /reviews`
* **Authorization**: Authenticated (`Bearer Sanctum Token`)
* **Request Body**:
  ```json
  {
    "product_id": 4,
    "product_type": "admin", // "admin", "seller", "digital_admin", or "digital_seller"
    "rating": 5, // Integer between 1 and 5
    "comment": "Outstanding product quality!"
  }
  ```
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "message": "আপনার রিভিউটি সফলভাবে জমা দেওয়া হয়েছে।",
    "data": {
      "id": 12,
      "user_id": 12,
      "shop_id": null,
      "product_id": 4,
      "product_type": "admin",
      "rating": 5,
      "comment": "Outstanding product quality!",
      "status": 1,
      "created_at": "2026-06-05T14:30:00.000000Z"
    }
  }
  ```
* **Response (Already Reviewed - 422 Unprocessable Content)**:
  ```json
  {
    "success": false,
    "message": "আপনি ইতিমধ্যে এই প্রোডাক্টটিতে রিভিউ দিয়েছেন।"
  }
  ```

### Get Reviews by Product
Fetch reviews for a specific product by product ID and product type.
* **Endpoints**: 
  - `GET /product/{type}/{id}/reviews` (Public)
  - `GET /reviews/{product_id}/{product_type}` (Public)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "product_id": 4,
        "product_type": "admin",
        "rating": 5,
        "comment": "Outstanding quality!",
        "user": {
          "id": 3,
          "name": "Karim Rahman"
        }
      }
    ]
  }
  ```

### Get Recent Reviews
Retrieve recent reviews for the homepage.
* **Endpoint**: `GET /recent-reviews`
* **Authorization**: None (Public)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": [ ... ]
  }
  ```

---

## 10. Other Content & Supporting APIs

### Save Leads (Incomplete Orders Tracking)
Saves contact/checkout information for incomplete checkouts to enable the administrator to follow up and assist in completing the order.
* **Endpoints**: 
  * `POST /leads/save` (Public)
  * `POST /v1/leads/save` (Public versioned)
* **Integration Trigger (Flutter/Mobile)**:
  * To capture leads in real-time: monitor the phone number input field. As soon as the input reaches exactly 11 digits (standard Bangladeshi mobile phone length), immediately trigger this API.
  * If the customer continues filling out other fields (Name, Email, Address, Shipping Area), implement a debounce timer (e.g., 1.5 seconds) to submit the updated information to the admin panel.
* **Request Body**:
  ```json
  {
    "phone": "01700000000",          // Required. Must be at least 11 digits.
    "name": "Nazmul Hossain",         // Optional.
    "email": "nazmul@example.com",     // Optional.
    "total": 1200.0,                  // Optional. Estimated cart total.
    "payment_method": "cod",          // Optional. Mapped to checkout choice ("cod" or "online").
    "area": "1",                      // Optional. Selected shipping zone ID.
    "address": "Uttara, House 4",     // Optional. Detailed customer delivery address.
    "cart_items": [                   // Optional. List of items in cart.
      {
        "id": 4,
        "qty": 2,
        "product_type": "admin",
        "color": "black",
        "size": "standard"
      }
    ],
    "url": "/checkout",               // Optional. Page URL context.
    "seller_id": null,                // Optional. Shop/Seller ID (null or 0 indicates admin).
    "shop_id": null,                  // Optional. Shop ID (alias for seller_id).
    "device": "Mobile",               // Optional. "Mobile" | "Tablet" | "Desktop".
    "browser": "Flutter App v1.0",    // Optional. Client/User Agent description.
    "os": "Android"                   // Optional. "Android" | "iOS".
  }
  ```
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "id": 142
  }
  ```
* **Response (Failure/Invalid Length - 200 OK)**:
  ```json
  {
    "success": false
  }
  ```

### Blocked Status & Cyber Security Check
Checks if the client's current IP address, device fingerprint, or user account (via token or query parameters) has been blocked. If blocked, it records a new attempt in the security log and returns location metadata so the mobile application can display a Cyber Security Block page.
* **Endpoints**: 
  * `GET /check-ip-blocked`
  * `GET /api/check-ip-blocked` (Depends on routing prefix)
* **Authorization**: Optional Bearer token (`Authorization: Bearer <token>`) to check if the authenticated account is blocked.
* **Query Parameters**:
  * `fingerprint` (Optional - string): Device fingerprint hash.
  * `phone` (Optional - string): Phone number to check.
  * `email` (Optional - string): Email address to check.
  * `user_id` (Optional - integer): User DB ID to check.
* **Response (Not Blocked - 200 OK)**:
  ```json
  {
    "blocked": false
  }
  ```
* **Response (Blocked - 200 OK)**:
  ```json
  {
    "blocked": true,
    "data": {
      "ip": "103.145.74.82",
      "wifi_provider": "Dot Internet (Broadband WiFi)",
      "location": "Dhaka, Uttara, Bangladesh",
      "lat": 23.8759,
      "lon": 90.3795,
      "device_agent": "Dart/3.0 (dart:io)",
      "device_type": "Mobile",
      "time": "2026-06-06 14:37:33"
    }
  }
  ```

> **Note on App Security Interception**: 
> For any authenticated API request (`Bearer Token`), if the user is blocked, the API will automatically respond with a `403 Forbidden` JSON payload:
> ```json
> {
>   "success": false,
>   "blocked": true,
>   "message": "Your account has been blocked. Please contact support."
> }
> ```
> The Flutter application should catch this response in a global HTTP interceptor and redirect the user to the Cyber Security warning view.

### Get Custom Static Pages (Company Info/Privacy Policy)
* **About Company**: `GET /about-company`
* **Privacy Policy**: `GET /privacy-policy`
* **Custom Page by Slug**: `GET /page/{slug}`

---

## 11. API Version 1 (JWT) Routes

In addition to the public Sanctum API, the backend exposes version 1 endpoints utilizing JSON Web Token (JWT) credentials.

* **Version 1 Base Prefix**: `/api/api/v1`

### Authentication
* **Login with JWT**: `POST /api/v1/auth/login`
  - Body: `{"email": "...", "password": "..."}`
  - Response: `{"token": "JWT_TOKEN", "user": { ... }}`
* **Register with JWT**: `POST /api/v1/auth/register`
* **Logout (JWT)**: `POST /api/v1/auth/logout`

### Catalog APIs
* **Get Products**: `GET /api/v1/products`
* **Get Single Product**: `GET /api/v1/products/{id}`

### Cart Management (JWT Protected)
* **Show Cart**: `GET /api/v1/cart`
* **Add to Cart**: `POST /api/v1/cart`
  - Body: `{"product_id": 4, "quantity": 1}`
* **Remove from Cart**: `DELETE /api/v1/cart/{itemId}`

### Wishlist Management (JWT Protected)
* **Show Wishlist**: `GET /api/v1/wishlist`
* **Toggle Wishlist Item**: `POST /api/v1/wishlist/toggle`
  - Body: `{"product_id": 4, "product_type": "admin"}`
* **Sync Guest Wishlist**: `POST /api/v1/wishlist/sync`
  - Headers: `X-Session-Id: <guest-uuid>`

### Checkout & Payments (JWT Protected)
* **Process Checkout**: `POST /api/v1/checkout`
* **List Past Orders**: `GET /api/v1/orders`
* **Show Order Details**: `GET /api/v1/orders/{id}`
* **Process Payment**: `POST /api/v1/payments`
* **User Profile**: `GET /api/v1/user/profile`
