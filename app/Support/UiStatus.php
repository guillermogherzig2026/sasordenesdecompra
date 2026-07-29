<?php

namespace App\Support;

class UiStatus
{
    public static function purchaseOrder(string $status, string $role): string
    {
        return match ($role) {
            'finance' => [
                'sent' => 'Pendiente de Aprobacion',
                'approved' => 'Pendiente de pago',
                'paid' => 'Pagada',
                'rejected' => 'Rechazada',
                'canceled' => 'Cancelada',
            ][$status] ?? ucfirst($status),
            'buyer' => [
                'sent' => 'Enviada a Finanzas',
                'approved' => 'Aprobada por Finanzas',
                'paid' => 'Pagada',
                'rejected' => 'Rechazada por Finanzas',
                'canceled' => 'Cancelada',
            ][$status] ?? ucfirst($status),
            default => [
                'sent' => 'En revision',
                'approved' => 'Autorizada',
                'paid' => 'Pagada',
                'rejected' => 'Rechazada',
                'canceled' => 'Cancelada',
            ][$status] ?? ucfirst($status),
        };
    }

    public static function receipt(string $status, string $role): string
    {
        return match ($role) {
            'inventory' => [
                'pending' => 'Pendiente de recepcion',
                'partial' => 'Recepcion parcial',
                'completed' => 'Recepcion completada',
            ][$status] ?? ucfirst($status),
            'finance' => [
                'pending' => 'Sin recepcion',
                'partial' => 'Recepcion parcial',
                'completed' => 'Recibida completa',
            ][$status] ?? ucfirst($status),
            default => [
                'pending' => 'Pendiente',
                'partial' => 'Parcial',
                'completed' => 'Completada',
            ][$status] ?? ucfirst($status),
        };
    }

    public static function purchaseOrderClass(string $status, string $role): string
    {
        return match ($role) {
            'finance' => [
                'sent' => 'pending',
                'approved' => 'pending',
                'paid' => 'paid',
                'rejected' => 'rejected',
                'canceled' => 'canceled',
            ][$status] ?? 'pending',
            'buyer' => [
                'sent' => 'pending',
                'approved' => 'approved',
                'paid' => 'paid',
                'rejected' => 'rejected',
                'canceled' => 'canceled',
            ][$status] ?? 'pending',
            default => [
                'sent' => 'pending',
                'approved' => 'approved',
                'paid' => 'paid',
                'rejected' => 'rejected',
                'canceled' => 'canceled',
            ][$status] ?? 'pending',
        };
    }

    public static function receiptClass(string $status, string $role): string
    {
        return match ($role) {
            'inventory' => [
                'pending' => 'pending',
                'partial' => 'partial',
                'completed' => 'approved',
            ][$status] ?? 'pending',
            'finance' => [
                'pending' => 'pending',
                'partial' => 'partial',
                'completed' => 'approved',
            ][$status] ?? 'pending',
            default => [
                'pending' => 'pending',
                'partial' => 'partial',
                'completed' => 'approved',
            ][$status] ?? 'pending',
        };
    }

    public static function supplyOrder(string $status, string $role = 'finance'): string
    {
        return [
            'sent' => 'Pendiente de Autorizacion',
            'approved' => $role === 'inventory' ? 'Autorizada para remision' : 'Autorizada',
            'remitted' => match ($role) {
                'buyer' => 'Pendiente de recepcion',
                'inventory' => 'Remision generada',
                default => 'Pendiente de recepcion',
            },
            'delivered' => 'Recibida',
            'rejected' => 'Rechazada',
            'canceled' => 'Cancelada',
        ][$status] ?? ucfirst($status);
    }

    public static function reimbursementOrder(string $status, string $role = 'finance'): string
    {
        return [
            'sent' => 'Pendiente de Autorizacion',
            'approved' => 'Pendiente de pago',
            'paid' => 'Pagada',
            'rejected' => 'Rechazada',
            'canceled' => 'Cancelada',
        ][$status] ?? ucfirst($status);
    }

    public static function workflowClass(string $status): string
    {
        return [
            'sent' => 'pending',
            'approved' => 'approved',
            'remitted' => 'partial',
            'paid' => 'paid',
            'delivered' => 'paid',
            'rejected' => 'rejected',
            'canceled' => 'canceled',
        ][$status] ?? 'pending';
    }

    public static function service(string $status, string $role, bool $hasSupport = false, bool $isPaid = false): string
    {
        if ($isPaid) {
            return 'Pagado';
        }

        if ($role === 'finance' && $hasSupport) {
            return 'Listo para pago';
        }

        return match ($role) {
            'finance' => [
                'active' => 'Pendiente de factura',
                'paused' => 'Servicio pausado',
                'inactive' => 'Dado de baja',
            ][$status] ?? ucfirst($status),
            default => [
                'active' => 'Activo',
                'paused' => 'Pausado',
                'inactive' => 'Dado de baja',
            ][$status] ?? ucfirst($status),
        };
    }

    public static function serviceClass(string $status, bool $isPaid = false, string $role = 'services', bool $hasSupport = false): string
    {
        if ($isPaid) {
            return 'approved';
        }

        if ($role === 'finance' && $hasSupport) {
            return 'approved';
        }

        return match ($status) {
            'active' => $role === 'finance' ? 'pending' : 'approved',
            'paused' => 'partial',
            'inactive' => 'canceled',
            default => 'pending',
        };
    }
}
