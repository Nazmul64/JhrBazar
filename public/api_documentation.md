# JhrBazar API Documentation

Welcome to the **JhrBazar API Documentation**. This document outlines all the available API endpoints in the backend, structured for frontend developers to integrate pages like Home, Categories, Sliders, Product Details, Wishlist, Cart, Checkout, and Customer Account/Chat.

---

## Table of Contents
1. [Base URL & Standards](#base-url--standards)
2. [Authentication API](#1-authentication-api)
3. [Category & Subcategory API](#2-category--subcategory-api)
4. [Banners & Sliders API](#3-banners--sliders-api)
5. [Product & Search API](#4-product--search-api)
6. [Wishlist API](#5-wishlist-api)
7. [Checkout & Orders API](#6-checkout--orders-api)
8. [Chat API](#7-chat-api)
9. [Other Content & Support APIs](#8-other-content--support-apis)

---

## Base URL & Standards

- **Base URL**: `http://your-domain.com/api` (or local counterpart `http://127.0.0.1:8000/api`)
- **Headers**:
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>` (for authenticated requests)
  - `X-Session-Id: <guest-session-uuid>` (for guest/session-based wishlist tracking)

---

## 1. Authentication API

### Register Customer
* **Endpoint**: `POST /register`
* **Authorization**: None (Public)
* **Request Body**:
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "017XXXXXXXX",
    "password": "secretpassword",
    "password_confirmation": "secretpassword"
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
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "017XXXXXXXX",
      "role": "customer",
      "status": 1,
      "created_at": "2026-06-04T09:05:32.000000Z",
      "updated_at": "2026-06-04T09:05:32.000000Z"
    }
  }
  ```

### Login Customer
* **Endpoint**: `POST /login`
* **Authorization**: None (Public)
* **Request Body**:
  ```json
  {
    "email": "john@example.com", 
    "password": "secretpassword"
  }
  ```
  > **Note**: The `email` field supports login using either the user's **email address** or **phone number**.
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "access_token": "2|LaravelSanctumToken...",
    "token_type": "Bearer",
    "user": { ... }
  }
  ```

### Logout Customer
* **Endpoint**: `POST /logout`
* **Authorization**: Authenticated (`Sanctum Token`)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "message": "Successfully logged out"
  }
  ```

### Get Authenticated User Details
* **Endpoint**: `GET /user`
* **Authorization**: Authenticated (`Sanctum Token`)
* **Response (Success - 200 OK)**: Returns the current user model object.

---

## 2. Category & Subcategory API

### Get Categories list
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
        "thumbnail": "/uploads/category/electronics.png",
        "sub_categories": [
          {
            "id": 5,
            "category_id": 1,
            "name": "Smartphones",
            "slug": "smartphones",
            "thumbnail": "/placeholder.jpg"
          }
        ]
      }
    ]
  }
  ```

### Get Category Hierarchy (with active subcategories)
* **Endpoint**: `GET /categories-with-sub`
* **Authorization**: None (Public)
* **Response (Success - 200 OK)**: Returns categories list matching the response structure of `GET /categories`.

### Get Category Name by ID
* **Endpoint**: `GET /category/{id}/name`
* **Authorization**: None (Public)
* **Response**:
  ```json
  {
    "success": true,
    "name": "Electronics"
  }
  ```

### Get Subcategory Name by ID
* **Endpoint**: `GET /subcategory/{id}/name`
* **Authorization**: None (Public)
* **Response**:
  ```json
  {
    "success": true,
    "name": "Smartphones"
  }
  ```

---

## 3. Banners & Sliders API

### Get Active Banners (Sliders)
* **Endpoint**: `GET /banners`
* **Authorization**: None (Public)
* **Response (Success - 200 OK)**:
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "image": "/uploads/banners/slider1.png?v=1.0.4",
        "for_own_shop": true
      }
    ]
  }
  ```

---

## 4. Product & Search API

### Get Consolidated Home Data
* **Endpoint**: `GET /home-data`
* **Authorization**: None (Public)
* **Description**: Returns all home page components (banners, categories, popular products, new arrivals, just-for-you products, digital products, best deals, top rated shops, footer logo/settings) in a single request to optimize page load speeds.
* **Response Structure**:
  ```json
  {
    "success": true,
    "data": {
      "settings": { ... },
      "banners": [ ... ],
      "categories": [ ... ],
      "popularProducts": [ ... ],
      "newArrivals": [ ... ],
      "justForYouProducts": [ ... ],
      "digitalProducts": [ ... ],
      "bestDeals": [ ... ],
      "topShops": [ ... ],
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
* **Endpoint**: `GET /all-products`
* **Authorization**: None (Public)
* **Query Parameters**:
  - `limit` (optional): Default `10`. Pass `all` to retrieve all active products without limit.
* **Response**: Combined collection of active Admin and Seller products.

### Get Popular Products
* **Endpoint**: `GET /popular-products`
* **Authorization**: None (Public)
* **Query Parameters**: `limit` (optional, default `10` or `all`)

### Get New Arrivals
* **Endpoint**: `GET /new-arrivals`
* **Authorization**: None (Public)
* **Query Parameters**: `limit` (optional, default `10` or `all`)

### Get Just For You Products
* **Endpoint**: `GET /just-for-you`
* **Authorization**: None (Public)
* **Query Parameters**: `limit` (optional, default `10` or `all`)

### Get Best Deals (Discounted Products)
* **Endpoint**: `GET /best-deals`
* **Authorization**: None (Public)
* **Query Parameters**: `limit` (optional, default `10` or `all`)

### Get Products By Category ID
* **Endpoint**: `GET /products/category/{id}`
* **Authorization**: None (Public)
* **Description**: Returns all active products under the specified category, including products belonging to its child subcategories.

### Get Products By Subcategory ID
* **Endpoint**: `GET /products/subcategory/{id}`
* **Authorization**: None (Public)

### Get Product Details by Slug (or ID)
* **Endpoint**: `GET /product/{slug}`
* **Authorization**: None (Public)
* **Description**: Resolves a product by its slug/ID across Admin, Seller, and Digital product tables. Includes active reviews, average rating, and related products in the same category.
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
      "description": "<p>Detailed description...</p>",
      "price": 1200.00,
      "discount_price": 1000.00,
      "old_price": 1200.00,
      "discount": 17,
      "stock": 45,
      "sku": "WL-004",
      "thumbnail": "/uploads/product/wallet.png?v=1.0.4",
      "gallery": [
        "/uploads/product/wallet_angle1.png?v=1717532345",
        "/uploads/product/wallet_angle2.png?v=1717532345"
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
            "profile_image": "default-avatar.png"
          }
        }
      ],
      "related": [ ... ]
    }
  }
  ```

### Search Products
* **Endpoint**: `GET /products/search`
* **Authorization**: None (Public)
* **Query Parameters**:
  - `q` (required): Search query string (minimum 2 characters).
* **Description**: Performs a fuzzy search on product names and prices across all catalog tables (Admin, Seller, and Digital products).

---

## 5. Wishlist API

### Get Wishlist Items
* **Endpoint**: `GET /wishlist`
* **Authorization**: None or Authenticated (`Sanctum Token`)
* **Headers**:
  - `X-Session-Id` (required if Guest/Unauthenticated): To track local browser-session wishlists.
* **Response**:
  ```json
  {
    "success": true,
    "data": [
      {
        "wishlist_id": 8,
        "id": 4,
        "uid": "admin_4",
        "title": "Premium Leather Wallet",
        "image": "/uploads/product/wallet.png",
        "price": 1000.00,
        "oldPrice": 1200.00,
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

### Toggle Product In Wishlist
* **Endpoint**: `POST /wishlist/toggle`
* **Authorization**: None or Authenticated (`Sanctum Token`)
* **Headers**:
  - `X-Session-Id` (required if Guest/Unauthenticated): Matches the active session token.
* **Request Body**:
  ```json
  {
    "product_id": 4,
    "product_type": "admin"
  }
  ```
  > **Note**: `product_type` can be `admin`, `seller`, or `digital`.
* **Response**:
  ```json
  {
    "success": true,
    "action": "added", // or 'removed'
    "message": "Product added to wishlist"
  }
  ```

### Sync Guest Wishlist (On Login)
* **Endpoint**: `POST /wishlist/sync`
* **Authorization**: Authenticated (`Sanctum Token`)
* **Headers**:
  - `X-Session-Id` (required): The session ID holding the guest's wishlist items.
* **Description**: Transfers all products saved in the browser session's wishlist to the user's permanent database profile upon successful customer login.
* **Response**:
  ```json
  {
    "success": true
  }
  ```

---

## 6. Checkout & Orders API

### Get Shipping Zones & Charges
* **Endpoint**: `GET /shipping-charges`
* **Authorization**: None (Public)
* **Response**:
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
* **Endpoint**: `GET /payment-gateways`
* **Authorization**: None (Public)
* **Description**: Returns all active payment configurations configured in the admin backend (e.g. SSLCommerz, Stripe, bKash, PayPal, etc.) along with their titles and public-facing asset logos.

### Apply Coupon Promo-Code
* **Endpoint**: `POST /apply-coupon`
* **Authorization**: None (Public)
* **Request Body**:
  ```json
  {
    "coupon_code": "EAD2026",
    "subtotal": 1500.00
  }
  ```
* **Response (Success)**:
  ```json
  {
    "success": true,
    "message": "Coupon applied successfully!",
    "discount": 150.00,
    "code": "EAD2026"
  }
  ```

### Place Order
* **Endpoint**: `POST /place-order`
* **Authorization**: None or Authenticated (`Sanctum Token`)
* **Request Body**:
  ```json
  {
    "name": "Jane Doe",
    "phone": "018XXXXXXXX",
    "address": "House 45, Road 2",
    "city": "Dhaka",
    "shipping_id": 1,
    "payment_method": "cod", // 'cod' or 'online'
    "online_gateway": null, // 'sslcommerz', 'stripe', 'bkash', etc. (required if payment_method is online)
    "coupon_code": "EAD2026",
    "device_fingerprint": "a90dfg8a9a...", 
    "browser": "Chrome",
    "os": "Windows",
    "device_type": "Desktop",
    "items": [
      {
        "id": 4,
        "qty": 2,
        "product_type": "admin",
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
        "customer_id": null,
        "sub_total": 2000.00,
        "discount": 200.00,
        "delivery_charge": 60.00,
        "grand_total": 1860.00,
        "payment_method": "cod",
        "status": "pending"
      }
    ],
    "payment_url": null
  }
  ```
* **Response (Success - Online Gateway selected, e.g. SSLCommerz)**:
  ```json
  {
    "success": true,
    "message": "Order placed successfully!",
    "orders": [ ... ],
    "payment_url": "https://sandbox.sslcommerz.com/gwprocess/v4/api.php?..." // Redirect client to this URL to complete payment
  }
  ```

### Track Order by Invoice Number
* **Endpoint**: `GET /track-order/{invoice_no}`
* **Authorization**: None (Public)
* **Response**:
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

### Get Order Details
* **Endpoint**: `GET /order-details/{invoice_no}`
* **Authorization**: None (Public)

---

## 7. Chat API

The Chat API supports communication between customers/visitors and the administrator. If the user is authenticated, their session links automatically. If unauthenticated, a tracking `session_id` can be used.

### Send Message
* **Endpoint**: `POST /chat/send`
* **Authorization**: None or Authenticated (`Sanctum Token`)
* **Request Body (Multipart Form-Data)**:
  - `session_id` (required - string): Unique browser/device tracking session UUID.
  - `receiver_id` (optional - integer): ID of receiver user (leave `null` or omit to message the Shop Administrator).
  - `message` (optional if image present - string): Text message content.
  - `image` (optional - file): Binary image upload (supported: jpeg, png, jpg, gif, webp; Max 5MB).
* **Response**:
  ```json
  {
    "success": true,
    "message": "Message sent",
    "data": {
      "id": 43,
      "chat_session_id": 8,
      "sender_type": "user",
      "message": "Hi, is this product in stock?",
      "image": null,
      "created_at": "2026-06-04T09:05:32.000000Z"
    }
  }
  ```

### Get Chat Messages
* **Endpoint**: `GET /chat/messages`
* **Authorization**: None or Authenticated (`Sanctum Token`)
* **Query Parameters**:
  - `session_id` (required): The active session UUID.
  - `receiver_id` (optional): Receiver's user ID.
* **Response**:
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
        "message": "Hello, how can I help you?",
        "image": null,
        "created_at": "2026-06-04T09:05:15.000000Z"
      }
    ]
  }
  ```

### Get Unread Chat Count
* **Endpoint**: `GET /chat/unread-count`
* **Authorization**: None or Authenticated
* **Query Parameters**: `session_id` (required), `receiver_id` (optional)
* **Response**:
  ```json
  {
    "success": true,
    "count": 0
  }
  ```

---

## 8. Other Content & Support APIs

### Get Customer Dashboard Stats
* **Endpoint**: `GET /customer/dashboard`
* **Authorization**: Authenticated (`Sanctum Token`)
* **Response**: Returns count of placed orders and items currently in wishlist.

### Get Customer Past Orders
* **Endpoint**: `GET /customer/orders`
* **Authorization**: Authenticated (`Sanctum Token`)

### Update Profile Info
* **Endpoint**: `POST /customer/update-profile`
* **Authorization**: Authenticated (`Sanctum Token`)
* **Request Body (Multipart Form-Data)**:
  - `name` (required - string)
  - `email` (required - email string)
  - `phone` (optional - string)
  - `address` (optional - string)
  - `profile_image` (optional - file)

### Change Customer Account Password
* **Endpoint**: `POST /customer/update-password`
* **Authorization**: Authenticated (`Sanctum Token`)
* **Request Body**:
  ```json
  {
    "current_password": "oldpassword",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
  }
  ```

### Get Company Pages & Support Info
* **Get Blogs**: `GET /blogs` (supports query filter: `category_id`, `category_slug`)
* **Get Blog Details**: `GET /blog/{slug}`
* **Get Custom Page Details**: `GET /page/{slug}`
* **Get About Company Info**: `GET /about-company`
* **Get Privacy Policy**: `GET /privacy-policy`
* **Get Landing Page content**: `GET /landingpage/{slug}`
