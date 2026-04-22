<?php
// database/seeders/PermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Shop > Order
            ['group' => 'Order',                'name' => 'list',                   'key' => 'order.list'],
            ['group' => 'Order',                'name' => 'view details',            'key' => 'order.view_details'],
            ['group' => 'Order',                'name' => 'change status',           'key' => 'order.change_status'],

            // Shop > Product
            ['group' => 'Product',              'name' => 'list',                   'key' => 'product.list'],
            ['group' => 'Product',              'name' => 'create',                 'key' => 'product.create'],
            ['group' => 'Product',              'name' => 'view details',            'key' => 'product.view_details'],
            ['group' => 'Product',              'name' => 'edit',                   'key' => 'product.edit'],
            ['group' => 'Product',              'name' => 'enable/disable',         'key' => 'product.enable_disable'],
            ['group' => 'Product',              'name' => 'delete',                 'key' => 'product.delete'],
            ['group' => 'Product',              'name' => 'barcode',                'key' => 'product.barcode'],
            ['group' => 'Product',              'name' => 'generate AI data',       'key' => 'product.generate_ai_data'],

            // Shop > Flash Sale
            ['group' => 'Flash Sale',           'name' => 'list',                   'key' => 'flash_sale.list'],
            ['group' => 'Flash Sale',           'name' => 'view details',            'key' => 'flash_sale.view_details'],
            ['group' => 'Flash Sale',           'name' => 'product store',          'key' => 'flash_sale.product_store'],
            ['group' => 'Flash Sale',           'name' => 'product remove',         'key' => 'flash_sale.product_remove'],
            ['group' => 'Flash Sale',           'name' => 'product edit',           'key' => 'flash_sale.product_edit'],

            // Shop > Promo Code
            ['group' => 'Promo Code',           'name' => 'list',                   'key' => 'promo_code.list'],
            ['group' => 'Promo Code',           'name' => 'create',                 'key' => 'promo_code.create'],
            ['group' => 'Promo Code',           'name' => 'edit',                   'key' => 'promo_code.edit'],
            ['group' => 'Promo Code',           'name' => 'enable/disable',         'key' => 'promo_code.enable_disable'],
            ['group' => 'Promo Code',           'name' => 'delete',                 'key' => 'promo_code.delete'],

            // Shop > Bulk Product Import
            ['group' => 'Bulk Product Import',  'name' => 'list',                   'key' => 'bulk_import.list'],
            ['group' => 'Bulk Product Import',  'name' => 'create',                 'key' => 'bulk_import.create'],

            // Shop > Bulk Product Export
            ['group' => 'Bulk Product Export',  'name' => 'list',                   'key' => 'bulk_export.list'],
            ['group' => 'Bulk Product Export',  'name' => 'demo',                   'key' => 'bulk_export.demo'],
            ['group' => 'Bulk Product Export',  'name' => 'export',                 'key' => 'bulk_export.export'],

            // Shop > Gallery Import
            ['group' => 'Gallery Import',       'name' => 'list',                   'key' => 'gallery_import.list'],
            ['group' => 'Gallery Import',       'name' => 'create',                 'key' => 'gallery_import.create'],

            // Shop > Pos
            ['group' => 'Pos',                  'name' => 'list',                   'key' => 'pos.list'],
            ['group' => 'Pos',                  'name' => 'sales',                  'key' => 'pos.sales'],
            ['group' => 'Pos',                  'name' => 'draft',                  'key' => 'pos.draft'],

            // Employee
            ['group' => 'Employee',             'name' => 'list',                   'key' => 'employee.list'],
            ['group' => 'Employee',             'name' => 'create',                 'key' => 'employee.create'],
            ['group' => 'Employee',             'name' => 'edit',                   'key' => 'employee.edit'],
            ['group' => 'Employee',             'name' => 'delete',                 'key' => 'employee.delete'],
            ['group' => 'Employee',             'name' => 'enable/disable',         'key' => 'employee.enable_disable'],
            ['group' => 'Employee',             'name' => 'reset password',         'key' => 'employee.reset_password'],
            ['group' => 'Employee',             'name' => 'permission',             'key' => 'employee.permission'],
            ['group' => 'Employee',             'name' => 'update permission',      'key' => 'employee.update_permission'],

            // Profile
            ['group' => 'Profile',              'name' => 'list',                   'key' => 'profile.list'],
            ['group' => 'Profile',              'name' => 'edit',                   'key' => 'profile.edit'],
            ['group' => 'Profile',              'name' => 'change password',        'key' => 'profile.change_password'],

            // ReturnOrder
            ['group' => 'ReturnOrder',          'name' => 'list',                   'key' => 'return_order.list'],
            ['group' => 'ReturnOrder',          'name' => 'view details',            'key' => 'return_order.view_details'],
            ['group' => 'ReturnOrder',          'name' => 'change status',          'key' => 'return_order.change_status'],

            // Supplier
            ['group' => 'Supplier',             'name' => 'list',                   'key' => 'supplier.list'],
            ['group' => 'Supplier',             'name' => 'create',                 'key' => 'supplier.create'],
            ['group' => 'Supplier',             'name' => 'view details',            'key' => 'supplier.view_details'],
            ['group' => 'Supplier',             'name' => 'edit',                   'key' => 'supplier.edit'],
            ['group' => 'Supplier',             'name' => 'update',                 'key' => 'supplier.update'],
            ['group' => 'Supplier',             'name' => 'delete',                 'key' => 'supplier.delete'],
            ['group' => 'Supplier',             'name' => 'enable/disable',         'key' => 'supplier.enable_disable'],
            ['group' => 'Supplier',             'name' => 'statistic',              'key' => 'supplier.statistic'],
            ['group' => 'Supplier',             'name' => 'payment',                'key' => 'supplier.payment'],

            // Purchase
            ['group' => 'Purchase',             'name' => 'list',                   'key' => 'purchase.list'],
            ['group' => 'Purchase',             'name' => 'create',                 'key' => 'purchase.create'],
            ['group' => 'Purchase',             'name' => 'view details',            'key' => 'purchase.view_details'],
            ['group' => 'Purchase',             'name' => 'edit',                   'key' => 'purchase.edit'],
            ['group' => 'Purchase',             'name' => 'update',                 'key' => 'purchase.update'],
            ['group' => 'Purchase',             'name' => 'delete',                 'key' => 'purchase.delete'],
            ['group' => 'Purchase',             'name' => 'attach.product',         'key' => 'purchase.attach_product'],
            ['group' => 'Purchase',             'name' => 'products',               'key' => 'purchase.products'],
            ['group' => 'Purchase',             'name' => 'makeReceived',           'key' => 'purchase.make_received'],
            ['group' => 'Purchase',             'name' => 'product.delete.barcode', 'key' => 'purchase.product_delete_barcode'],
            ['group' => 'Purchase',             'name' => 'invoice.search',         'key' => 'purchase.invoice_search'],
            ['group' => 'Purchase',             'name' => 'invoice.add',            'key' => 'purchase.invoice_add'],
            ['group' => 'Purchase',             'name' => 'summary',                'key' => 'purchase.summary'],
            ['group' => 'Purchase',             'name' => 'purchaseInvoice',        'key' => 'purchase.purchase_invoice'],
            ['group' => 'Purchase',             'name' => 'allProduct.stockSummary','key' => 'purchase.all_product_stock_summary'],

            // PurchaseReturn
            ['group' => 'PurchaseReturn',       'name' => 'list',                   'key' => 'purchase_return.list'],
            ['group' => 'PurchaseReturn',       'name' => 'create',                 'key' => 'purchase_return.create'],
            ['group' => 'PurchaseReturn',       'name' => 'view details',            'key' => 'purchase_return.view_details'],
            ['group' => 'PurchaseReturn',       'name' => 'invoice.search',         'key' => 'purchase_return.invoice_search'],
            ['group' => 'PurchaseReturn',       'name' => 'Invoice',                'key' => 'purchase_return.invoice'],
            ['group' => 'PurchaseReturn',       'name' => 'invoice.add',            'key' => 'purchase_return.invoice_add'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrInsert(
                ['key' => $permission['key']],
                $permission
            );
        }
    }
}
