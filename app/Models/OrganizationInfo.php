<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationInfo extends Model
{
    public const DEFAULT_INVOICE_FOOTER_TEXT = "VAT against this challan is payable through central registration. Thank you for your business!\nThank You For Shopping With Us. Please Come Again";

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'website',
        'logo_path',
        'invoice_footer_text',
    ];
}
