<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'folio',
        'buyer_id',
        'construction_project_id',
        'company_id',
        'warehouse',
        'provider_id',
        'created_on',
        'due_date',
        'is_credit',
        'credit_days',
        'reference',
        'payment_concept',
        'observations',
        'quote_file_path',
        'quote_original_name',
        'delivery_date',
        'status',
        'receipt_status',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'created_on' => 'date',
            'due_date' => 'date',
            'is_credit' => 'boolean',
            'credit_days' => 'integer',
            'delivery_date' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function constructionProject()
    {
        return $this->belongsTo(ConstructionProject::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(PurchaseOrderPayment::class);
    }

    public function receipts()
    {
        return $this->hasMany(PurchaseOrderReceipt::class);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function getRouteKeyName(): string
    {
        return 'folio';
    }

    public function scopeGeneral(Builder $query): Builder
    {
        return $query->whereNull('construction_project_id');
    }

    public function scopeForConstruction(Builder $query): Builder
    {
        return $query->whereNotNull('construction_project_id');
    }

    public function isEditableByBuyer(): bool
    {
        return $this->status === 'draft';
    }

    public function isOpenForInventory(): bool
    {
        return $this->status === 'paid' && $this->receipt_status !== 'completed';
    }
}
