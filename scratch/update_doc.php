<?php

$doc_path = "e:\\JhrBazar\\api_documentation.md";

// Try standard read
$content = file_get_contents($doc_path);

if ($content === false) {
    echo "Failed to read file.\n";
    exit(1);
}

echo "Successfully read file. Size: " . strlen($content) . " bytes\n";

// 1. Update Base URL explanation to clarify apiPrefix
$old_base_url = "### Base URL
- **Production Base URL**: `https://your-domain.com/api`
- **Development/Local URL**: `http://127.0.0.1:8000/api`";

$new_base_url = "### Base URL
- **Production Base URL**: `https://your-domain.com/api` (or `https://your-domain.com` if not using /api prefix reverse proxy)
- **Development/Local URL**: `http://127.0.0.1:8000` (Note: `bootstrap/app.php` sets `apiPrefix: ''`, so API endpoints are directly on the root. Example: `http://127.0.0.1:8000/settings` or `http://127.0.0.1:8000/v1/auth/login`)";

$content = str_replace($old_base_url, $new_base_url, $content);

// 2. Update Table of Contents
$old_toc = "## Table of Contents
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
11. [Other Content & Supporting APIs](#9-other-content--supporting-apis)
12. [API Version 1 (JWT) Routes](#10-api-version-1-jwt-routes)";

$new_toc = "## Table of Contents
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
13. [API Version 1 (JWT) Routes](#11-api-version-1-jwt-routes)";

$content = str_replace($old_toc, $new_toc, $content);

// 3. Add Reviews API Section before Section 9 (which is currently "Other Content & Supporting APIs")
$old_other_section = "## 9. Other Content & Supporting APIs";

$reviews_api_text = <<<'MARKDOWN'
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
MARKDOWN;

$content = str_replace($old_other_section, $reviews_api_text, $content);

// 4. Update the section header for Version 1
$content = str_replace("## 10. API Version 1 (JWT) Routes", "## 11. API Version 1 (JWT) Routes", $content);

$result = file_put_contents($doc_path, $content);
if ($result === false) {
    echo "Failed to write file.\n";
    exit(1);
}

echo "Successfully updated api_documentation.md! Written: $result bytes\n";
exit(0);
