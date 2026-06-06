import os

def main():
    doc_path = r"e:\JhrBazar\api_documentation.md"
    
    # Read the file content
    encodings = ['utf-8', 'utf-16', 'utf-16-le', 'utf-16-be']
    content = None
    used_encoding = None
    for enc in encodings:
        try:
            with open(doc_path, 'r', encoding=enc) as f:
                content = f.read()
                used_encoding = enc
                break
        except Exception:
            continue
            
    if content is None:
        print("Failed to read file.")
        return
        
    print(f"Successfully read with encoding: {used_encoding}")
    
    # 1. Update Base URL explanation to clarify apiPrefix
    old_base_url = """### Base URL
- **Production Base URL**: `https://your-domain.com/api`
- **Development/Local URL**: `http://127.0.0.1:8000/api`"""

    new_base_url = """### Base URL
- **Production Base URL**: `https://your-domain.com/api` (or `https://your-domain.com` if not using /api prefix reverse proxy)
- **Development/Local URL**: `http://127.0.0.1:8000` (Note: `bootstrap/app.php` sets `apiPrefix: ''`, so API endpoints are directly on the root. Example: `http://127.0.0.1:8000/settings` or `http://127.0.0.1:8000/v1/auth/login`)"""

    content = content.replace(old_base_url, new_base_url)

    # 2. Update Table of Contents
    old_toc = """## Table of Contents
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
12. [API Version 1 (JWT) Routes](#10-api-version-1-jwt-routes)"""

    new_toc = """## Table of Contents
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
13. [API Version 1 (JWT) Routes](#11-api-version-1-jwt-routes)"""

    content = content.replace(old_toc, new_toc)

    # 3. Add Reviews API Section before Section 9 (which is currently "Other Content & Supporting APIs")
    old_other_section = """## 9. Other Content & Supporting APIs"""
    
    reviews_api_text = """## 9. Reviews API

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

## 10. Other Content & Supporting APIs"""

    content = content.replace(old_other_section, reviews_api_text)

    # 4. Update the section header for Version 1
    content = content.replace("## 10. API Version 1 (JWT) Routes", "## 11. API Version 1 (JWT) Routes")

    # Save the modified content back
    with open(doc_path, 'w', encoding='utf-8') as f:
        f.write(content)
        
    print("Successfully updated api_documentation.md!")

if __name__ == '__main__':
    main()
