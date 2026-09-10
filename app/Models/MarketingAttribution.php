<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingAttribution extends Model
{
    use HasFactory;

    protected $table = 'marketing_attributions';

    protected $fillable = [
        'visitor_id',
        'session_id',
        'order_id',
        'invoice_id',
        'traffic_source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'click_id',
        'referrer_url',
        'landing_page_url',
        'ip_address',
        'country',
        'device_type',
        'browser',
        'os',
        'revenue',
        'order_status',
    ];

    protected $casts = [
        'revenue' => 'decimal:2',
    ];

    /**
     * Platform alias accessor
     */
    public function getDetectedPlatformAttribute()
    {
        return $this->traffic_source;
    }

    public function setDetectedPlatformAttribute($value)
    {
        $this->attributes['traffic_source'] = $value;
    }

    public function getLandingPageAttribute()
    {
        return $this->landing_page_url;
    }

    public function setLandingPageAttribute($value)
    {
        $this->attributes['landing_page_url'] = $value;
    }

    public function getConversionStatusAttribute()
    {
        return $this->order_status;
    }

    public function setConversionStatusAttribute($value)
    {
        $this->attributes['order_status'] = $value;
    }

    /**
     * Relationship with Point of Sale / Order (Pointofsalepo)
     */
    public function order()
    {
        return $this->belongsTo(Pointofsalepo::class, 'order_id');
    }

    /**
     * Relationship with PosInvoice
     */
    public function invoice()
    {
        return $this->belongsTo(PosInvoice::class, 'invoice_id');
    }
}
