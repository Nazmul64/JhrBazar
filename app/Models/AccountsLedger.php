<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountsLedger extends Model
{
    use HasFactory;

    protected $table = 'accounts_ledgers';

    protected $fillable = [
        'transaction_type',
        'category_id',
        'voucher_no',
        'title',
        'description',
        'income_amount',
        'expense_amount',
        'running_balance',
        'payment_method',
        'reference',
        'document_file',
        'transaction_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'income_amount'    => 'decimal:2',
        'expense_amount'   => 'decimal:2',
        'running_balance'  => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Generate next voucher number, e.g. VCH-202609-0001
     */
    public static function generateVoucherNo(string $type = 'ACC'): string
    {
        $prefix = strtoupper($type) . '-' . date('Ym');
        $last = self::where('voucher_no', 'like', "{$prefix}-%")->latest('id')->first();
        if ($last && preg_match('/-(\d+)$/', $last->voucher_no, $m)) {
            $num = (int)$m[1] + 1;
        } else {
            $num = 1;
        }
        return $prefix . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
