<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;

class CompanyAssetController extends Controller
{
    public function logo(Company $company)
    {
        abort_unless($company->logo_path && Storage::exists($company->logo_path), 404);

        return Storage::response($company->logo_path);
    }
}
